from fastapi import FastAPI, HTTPException , UploadFile, File,HTTPException
from fastapi.responses import FileResponse
from pathlib import Path
from fastapi.middleware.cors import CORSMiddleware
from PyPDF2 import PdfReader
import sys
import os
from classes.super_db import chain_chan_database
from classes.super_vdb import chain_chan_vector_store
from dotenv import load_dotenv
from pydantic import BaseModel
from classes.super_memory import chain_chan_memory
from classes.super_models import chain_chan_models  # YOUR CUSTOM MODEL LOADER
from langchain_core.messages import AIMessage, HumanMessage, SystemMessage
from langchain_core.prompts import ChatPromptTemplate, MessagesPlaceholder

class RoleUpdateRequest(BaseModel):
    doc_name: str
    new_role: list


load_dotenv()
app = FastAPI()

# CORS setup — allow all origins (you can restrict this as needed)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # or a list like ["https://yourdomain.com"]
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

ccvs=chain_chan_vector_store()

@app.get("/pdf")
def list_pdfs():
    try:
        pdf_list=ccvs.pdf_list()
        return pdf_list
    except Exception as e:
        return {"error getting pdf list": str(e)}
    
@app.get("/pdf/{filename}")
def get_pdf(filename: str):
    file_path = os.path.join(PDF_FOLDER, filename)
    if not os.path.exists(file_path):
        raise HTTPException(status_code=404, detail="PDF not found")
    return FileResponse(path=file_path, media_type='application/pdf', filename=filename)

@app.get("/get-assignments") #this is for roles
def get_assignments():
    try:
        assignments = ccvs.list_all_documents()
        return assignments
    except Exception as e:
        return {"error getting assignments": str(e)}
    
@app.put("/update_role")
def update_role(request: RoleUpdateRequest):
    try:
        ccvs.update_metadata_role(request.doc_name, request.new_role)
        return {"message": f"Role updated for {request.doc_name} to {request.new_role}"}
    except Exception as e:
        return {"error updating role": str(e)}
    
@app.get("/pdf/{doc_name}/content")
def get_pdf_content(doc_name: str):
    print("Fetching PDF content for:", doc_name)
    pdf_path = os.path.join("pdf", doc_name)
    print("PDF path:", pdf_path)
    if not os.path.exists(pdf_path):
        raise HTTPException(status_code=404, detail="PDF not found")

    return FileResponse(
        path=pdf_path,
        media_type='application/pdf',
        filename=doc_name,
        headers={"Content-Disposition": f'inline; filename="{doc_name}"'}
    )
    

MAX_FILE_SIZE = 10 * 1024 * 1024  # 10 MB in bytes
@app.post("/pdf/upload")
async def upload_pdf(file: UploadFile = File(...)):
    try:
        file_content = await file.read()

        # Check if file exceeds max size
        if len(file_content) > MAX_FILE_SIZE:
            raise HTTPException(status_code=413, detail="File size exceeds 10MB limit.")


        # Save file to disk
        with open("../pdf/" + file.filename, "wb") as f:
            f.write(file_content)

        # Try to add to vector DB
        try:
            ccvs.add_pdf_vdb(file=file.filename,path="..\\pdf\\")
            return {"message": "PDF uploaded and added to vector database successfully"}
        except Exception as e:
            os.remove("pdf\\" + file.filename)  # Clean up on failure
            raise HTTPException(status_code=500, detail=f"Failed to add to vector DB : {str(e)}")

    except HTTPException as http_ex:
        raise http_ex
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to upload PDF: {str(e)}")
    


@app.delete("/pdf/{doc_name}")
async def delete_pdf(doc_name: str):
    try:
        # Define the base directory for PDFs (adjust if needed)
        pdf_dir = Path(__file__).resolve().parent.parent / "pdf"
        file_path = (pdf_dir / doc_name).resolve()

        # Prevent path traversal by ensuring the resolved file is inside the intended pdf_dir
        if not str(file_path).startswith(str(pdf_dir)):
            raise HTTPException(status_code=400, detail="Invalid file path")

        # Delete from your vector DB or tracking system
        ccvs.delete_pdf_vdb(doc_name)

        # Delete from disk
        if file_path.exists():
            file_path.unlink()
        else:
            raise HTTPException(status_code=404, detail=f"File {doc_name} not found")

        return {"message": f"PDF {doc_name} deleted successfully"}

    except HTTPException as he:
        raise he  # re-raise known HTTP errors
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to delete PDF: {str(e)}")





def helper_RAG(role, query, k=2):
    try:
        similarity_search = chain_chan_vector_store().sim_search(query=query, k=k)
        is_role = "is_" + role
        accessible_docs = []
        links = []
        url = []
        python_api_base_url = os.getenv("python_api_base_url")

        if similarity_search:
            for doc in similarity_search:
                meta = doc.metadata
                if meta.get(is_role, False):
                    print("Access granted:", doc.metadata.get("source", "title"))
                
                    accessible_docs.append(doc.page_content)
                    links.append(doc.metadata.get("source"))
                else:
                    print("Access denied for this document.")
            if not accessible_docs:
                print("No accessible documents found for your role.")
        else:
            print("No documents found.")
        
        for link in links:
            link = os.path.basename(link)
            main_man = python_api_base_url + "/pdf/" + link + "/content"
            
            url.append(main_man)
        print("Links:", url)
        return  accessible_docs, url

    except Exception as e:
        print(f"Error in RAG helper: {str(e)}")
        return {"error": "Failed to perform RAG operation"}
    
def generate_html(links):
    if not links:
        return ""  # Return empty string if the list is empty

    html = '''<h2>Relevant Documents:</h2>\n<ul>\n'''
    for link in links:
        html += f'  <li><a href="{link}">{link}</a></li>\n'
    html += '''</ul>\n
    <style>
    body {{
        background: #101010;
        color: #00ff00;
        font-family: monospace;
    }}
    h2 {{
        border-bottom: 1px solid #00ff00;
    }}
    a {{
        color: #00ff00;
        text-decoration: none;
    }}
    a:hover {{
        color: #55ff55;
        text-decoration: underline;
    }}
    </style>
    '''
    return html

class ChatRequest(BaseModel):
    user_input: str
    conversation_id: str
    target_user_id: int
    role: str

class ChatResponse(BaseModel):
    ai_response: str
    conversation_id: str
    tokens: int
    


@app.post("/process-chat", response_model=ChatResponse)
def process_chat(request: ChatRequest):
    try:
        llm = chain_chan_models(model="claude-3.5-sonnet")
    
        # Load memory and model
        role = request.role
        ccmemory = chain_chan_memory()
        history=ccmemory.fetch_history(
            convo_id=request.conversation_id,
            user_id=request.target_user_id,
            # system_prompt="You are an AI Chatbot named Chain Chan."
        )
        docs,links = helper_RAG(role, request.user_input, k=1)
        if len(links)>0:
            html_links= "\n Relevant documents:" + "\n".join(links)
        else:
            html_links = ""
        prompt = ChatPromptTemplate.from_messages([
            ("system", "Your role is to refer to  relevent documents and answer from that knowledge alone.Use the given history  as conversation history to respond and responde to latest query of the user."),
            ("human", "history:{history} , UserInput:{input}, relevent documents: {docs}")
        ])
        print(prompt.format_messages(
            history=history,  # Pass the history as a list of tuples
            input=request.user_input,  # Placeholder for user input
            docs=html_links  # Pass the generated HTML links
        ))
        chain = prompt | llm

        # Run the chain
        print(f"History: {history}")
        response = chain.invoke({"history":history,"input": request.user_input,"docs":docs})
        
        #custom memory service to add AI response
        add_memory_response = ccmemory.add_memory(
            text=response.content+html_links,
            convo_id=request.conversation_id,
            user_id=request.target_user_id
        )
        
        return {
            "ai_response": response.content+ html_links,
            "conversation_id": request.conversation_id,
            "tokens": response.usage_metadata["total_tokens"]
        }

    except Exception as e:
        print(f"Error processing chat: {str(e)}")
        add_memory_response = ccmemory.add_memory(
            text="Error occurred while processing chat.",
            convo_id=request.conversation_id,
            user_id=request.target_user_id
        )
        raise HTTPException(status_code=500, detail=str(e))
    
    