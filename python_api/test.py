import os
from dotenv import load_dotenv
from langchain_core.prompts import ChatPromptTemplate, MessagesPlaceholder
from langchain_core.messages import HumanMessage, AIMessage, SystemMessage
from langchain.memory import ConversationBufferMemory # Standard LangChain memory
from langchain_core.runnables.history import RunnableWithMessageHistory


# Assuming these are your custom classes/functions
from classes.super_memory import chain_chan_memory
from classes.super_models import chain_chan_models
from classes.super_vdb import chain_chan_vector_store
# Load environment variables
load_dotenv()

# # --- Helper: Convert (role, message) tuples to LangChain messages ---
# def convert_to_lc_messages(history_tuples):
#     lc_messages = []
#     for role, content in history_tuples:
#         if role == "human":
#             lc_messages.append(HumanMessage(content=content))
#         elif role == "ai":
#             lc_messages.append(AIMessage(content=content))
#         elif role == "system":
#             # Note: System messages are usually at the start and not part of ongoing turn-by-turn history
#             # but if your custom history includes them, this will handle it.
#             lc_messages.append(SystemMessage(content=content))
#     return lc_messages

# # --- Define Session ID and User ID ---
# # These would typically come from your application's session management
# CONVO_ID = "client_197171B6BFB_4eeb4e191b4e5"
# USER_ID = 2

# # --- Store for Session Histories ---
# # In a real application, you might use a more persistent store like Redis or a DB
# # For this example, a simple dictionary will suffice.
# # The key will be the session_id (CONVO_ID in this case).
# # The value will be the BaseChatMessageHistory object (e.g., ChatMessageHistory from memory).
# store = {}

# def get_session_history(session_id: str):
#     """
#     Retrieves or creates a chat message history for a given session_id.
#     """
#     if session_id not in store:
#         # If no history for this session_id, create a new one.
#         # We will pre-populate it later if existing history is found.
#         store[session_id] = ConversationBufferMemory(
#             memory_key="history", # Must match MessagesPlaceholder
#             return_messages=True # Crucial for ChatPromptTemplate
#         ).chat_memory # We need the BaseChatMessageHistory object
#     return store[session_id]

# # --- Fetch and Convert Chat History ---
# print(f"Fetching history for convo_id: {CONVO_ID}, user_id: {USER_ID}")
# history_raw = chain_chan_memory().fetch_history(
#     convo_id=CONVO_ID,
#     user_id=USER_ID
# )
# initial_lc_messages = convert_to_lc_messages(history_raw)

# # --- Pre-populate memory for the current session ---
# # Get the ChatMessageHistory object for this session
# # If it doesn't exist, get_session_history will create an empty one.
# session_chat_history = get_session_history(CONVO_ID)

# # Clear any existing messages in the session_chat_history (if it was just created, it's empty)
# # or if we want to strictly replace it with fetched history.
# session_chat_history.clear()

# # Add the fetched messages to this specific session's history
# for msg in initial_lc_messages:
#     session_chat_history.add_message(msg)

# print("\n--- Initial History Loaded into Memory ---")
# for msg in session_chat_history.messages: # Access messages from the ChatMessageHistory object
#     print(f"{type(msg).__name__}: {msg.content}")
# print("----------------------------------------")


# # --- Build Prompt ---
# prompt = ChatPromptTemplate.from_messages([
#     ("system", "You are an AI Chatbot named Chain Chan. You MUST pay close attention to the provided conversation history to understand the context and recall information, such as the user's name if they have mentioned it."),
#     MessagesPlaceholder(variable_name="history"), # This name must match memory_key
#     ("human", "{input}")
# ])

# # --- Load LLM ---
# llm = chain_chan_models(model="claude-3-haiku")

# # --- Build the Core Chain (without history management yet) ---
# base_chain = prompt | llm

# # --- Wrap the chain with RunnableWithMessageHistory ---
# # This adds the memory management capabilities.
# chain_with_history = RunnableWithMessageHistory(
#     base_chain,
#     get_session_history, # Function to load/retrieve chat history
#     input_messages_key="input",  # The key for the user's input in the invoke dict
#     history_messages_key="history", # The key for MessagesPlaceholder in the prompt
# )

# # --- Invoke the Chain ---
# # Now, we don't pass "history" directly. RunnableWithMessageHistory handles it.
# # We need to provide a "session_id" in the config.
# print("\n--- Invoking Chain ---")
# user_input = "say my name?"
# if not history_raw: # Add a specific instruction if history is empty for testing
#     user_input = "My name is Alex. What is my name?"
#     print(f"History was empty, so asking: {user_input}")


# res = chain_with_history.invoke(
#     {"input": user_input},
#     config={"configurable": {"session_id": CONVO_ID}}
# )

# # --- Print the Response ---
# print("\n--- LLM Response ---")
# if isinstance(res, AIMessage):
#     print(f"AI: {res.content}")
# else:
#     print(res) # If it's not an AIMessage, print raw for debugging (e.g. string content)

# # --- Check Memory After Invocation ---
# # The new human input and AI response should now be in the memory for CONVO_ID
# print("\n--- Memory After Invocation ---")
# updated_session_history = get_session_history(CONVO_ID) # Retrieve it again
# for msg in updated_session_history.messages:
#     print(f"{type(msg).__name__}: {msg.content}")
# print("-------------------------------")

# # --- Second Invocation (to test memory persistence) ---
# print("\n--- Invoking Chain (Second Time) ---")
# res_2 = chain_with_history.invoke(
#     {"input": "What was the first thing I asked you in this current interaction?"},
#     config={"configurable": {"session_id": CONVO_ID}}
# )
# print("\n--- LLM Response (Second Time) ---")
# if isinstance(res_2, AIMessage):
#     print(f"AI: {res_2.content}")
# else:
#     print(res_2)

# print("\n--- Memory After Second Invocation ---")
# final_session_history = get_session_history(CONVO_ID)
# for msg in final_session_history.messages:
#     print(f"{type(msg).__name__}: {msg.content}")
# print("------------------------------------")

# rag
llm = chain_chan_models(model='claude-3.5-sonnet')  # or 'claude-3-haiku'
# chain_chan_vector_store()._get_embedding_model()  # Ensure embeddings are initialized
# import os
# from dotenv import load_dotenv
# import urllib.parse
# load_dotenv()
# def helper_RAG(role, query, k=2):
#     try:
#         similarity_search = chain_chan_vector_store().sim_search(query=query, k=k)
#         is_role = "is_" + role
#         accessible_docs = []
#         links = []
#         url = []
#         python_api_base_url = os.getenv("python_api_base_url")

#         if similarity_search:
#             for doc in similarity_search:
#                 meta = doc.metadata
#                 if meta.get(is_role, False):
#                     print("Access granted:", doc.metadata.get("source", "title"))
#                     accessible_docs.append(doc.page_content)
#                     links.append(doc.metadata.get("source"))
#                 else:
#                     print("Access denied for this document.")
#             if not accessible_docs:
#                 print("No accessible documents found for your role.")
#         else:
#             print("No documents found.")
        
#         for link in links:
#             link = os.path.basename(link)
#             link= urllib.parse.quote(link)
#             main_man = python_api_base_url + "/pdf/" + link + "/content"
            
#             url.append(main_man)
#         print("Links:", url)
#         return {"documents": accessible_docs, "links": url}

#     except Exception as e:
#         print(f"Error in RAG helper: {str(e)}")
#         return {"error": "Failed to perform RAG operation"}

# helper_RAG("manager", "What is my password?", k=2)
import sqlite3
import os

# --- Global Variables for LLM, DB, and Schema (initialized once) ---
LLM_INSTANCE = None
DB_INSTANCE = None
ALL_TABLE_SCHEMAS_STRING = None # Will hold the schema for all tables

def initialize_components():
    """
    Initializes the LLM, Database connection, and retrieves the database schema.
    This should be called once before using get_answer_from_database.
    Returns True on success, False on failure.
    """
    global LLM_INSTANCE, DB_INSTANCE, ALL_TABLE_SCHEMAS_STRING

    # 1. Initialize LLM
    try:
        from classes.super_models import chain_chan_models # Ensure this path is correct
        LLM_INSTANCE = chain_chan_models(model="claude-3-haiku", temperature=0)
        print("LLM initialized successfully.")
    except ImportError:
        print("ERROR: Could not import 'chain_chan_models' from 'classes.super_models'.")
        print("Please ensure 'classes/super_models.py' exists and is correctly set up.")
        return False
    except Exception as e:
        print(f"ERROR: Could not initialize the custom LLM: {e}")
        return False

    # 2. Initialize Database Connection
    from langchain_community.utilities import SQLDatabase
    db_folder = "db"
    db_file_name = "DataCoSupplyChainDataset.db"
    db_path = os.path.join(db_folder, db_file_name)
    db_uri = f"sqlite:///{db_path}"

    if not os.path.exists(db_path):
        print(f"ERROR: Database file not found at '{db_path}'.")
        return False

    try:
        DB_INSTANCE = SQLDatabase.from_uri(db_uri, sample_rows_in_table_info=0)
        print(f"Database '{db_file_name}' connected successfully.")
    except Exception as e:
        print(f"ERROR: Could not connect to or initialize the database at '{db_uri}': {e}")
        return False

    # 3. Retrieve Schema for ALL tables
    try:
        all_table_names_in_db = DB_INSTANCE.get_usable_table_names()
        if not all_table_names_in_db:
            print("ERROR: No tables found in the database.")
            return False
        ALL_TABLE_SCHEMAS_STRING = DB_INSTANCE.get_table_info(all_table_names_in_db)
        print("Database schema retrieved successfully.")
    except Exception as e:
        print(f"ERROR: Could not retrieve table schemas from the database: {e}")
        return False
    
    return True


def get_answer_from_database(user_question: str) -> str:
    """
    Takes a natural language question, queries the pre-configured database
    using the pre-initialized LLM, and returns a natural language answer.
    """
    if not LLM_INSTANCE or not DB_INSTANCE or not ALL_TABLE_SCHEMAS_STRING:
        return "ERROR: System not initialized. Call initialize_components() first."

    # --- Step 1: Generate SQL Query using LLM ---
    generated_sql_query = ""
    try:
        sql_generation_prompt = f"""
You are an expert SQL writer. Given the following database schema which contains one or more tables:
<schema>
{ALL_TABLE_SCHEMAS_STRING}
</schema>

Your task is to generate a single, executable SQL query to answer the question below.
Question: "{user_question}"

Important considerations:
- The question uses "this table," which might be ambiguous if there are multiple tables in the schema.
- If there are multiple tables, either:
    1. Identify the most prominent or central table (e.g., a table named 'Orders', 'Sales', 'DataCoSupplyChain', or the first table if no other obvious choice).
    2. Or, provide a query that counts rows for all tables, or a representative selection of important ones if there are very many.
- Output ONLY the SQL query and nothing else. Do not include explanations or markdown formatting like ```sql.

SQL Query:
"""
        sql_query_response_object = LLM_INSTANCE.invoke(sql_generation_prompt)
        generated_sql_query = sql_query_response_object.content if hasattr(sql_query_response_object, 'content') else str(sql_query_response_object)
        generated_sql_query = generated_sql_query.strip()

        if not generated_sql_query:
            raise ValueError("LLM did not return a SQL query.")
        # print(f"DEBUG: LLM Generated SQL: {generated_sql_query}") # Uncomment for debugging

    except Exception as e:
        return f"ERROR during SQL generation by LLM: {e}"

    # --- Step 2: Execute the Generated SQL Query ---
    sql_query_execution_result = ""
    try:
        sql_query_execution_result = DB_INSTANCE.run(generated_sql_query)
        # print(f"DEBUG: SQL Query Result (raw): {sql_query_execution_result}") # Uncomment for debugging
    except Exception as e:
        return f"ERROR executing SQL query '{generated_sql_query}': {e}. LLM might have generated invalid SQL."

    # --- Step 3: Formulate Natural Language Answer using LLM ---
    try:
        answer_generation_prompt = f"""
The user originally asked: "{user_question}"
To answer this, the following SQL query was generated and executed:
SQL Query: "{generated_sql_query}"
The result of executing this SQL query was:
<query_result>
{sql_query_execution_result}
</query_result>

Based on the original question, the SQL query, and its result, provide a concise and clear natural language answer.
If the query provided counts for multiple tables, summarize that information clearly.
Answer:
"""
        final_answer_response_object = LLM_INSTANCE.invoke(answer_generation_prompt)
        final_natural_language_answer = final_answer_response_object.content if hasattr(final_answer_response_object, 'content') else str(final_answer_response_object)
        return final_natural_language_answer

    except Exception as e:
        return f"ERROR during final answer generation by LLM: {e}"


# --- Example Usage ---
if __name__ == "__main__":
    # Initialize components once at the start of your application
    if not initialize_components():
        print("Failed to initialize. Exiting.")
        exit()

    print("\n--- Asking a question to the database ---")
    question1 = "How many rows are there in this table?"
    print(f"User Question: \"{question1}\"")
    answer1 = get_answer_from_database(question1)
    print(f"LLM's Answer: {answer1}")
