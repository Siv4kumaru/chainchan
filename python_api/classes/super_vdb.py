from langchain_chroma import Chroma
from classes.super_embed import chain_chan_embedder
from classes.text_splitting import pdf_chunks_chain_chan
import os   
import chromadb
from dotenv import load_dotenv


class chain_chan_vector_store:
    """A class to manage a vector store for PDF documents using ChromaDB."""
    def __init__(self, collection_name="syngenta_pdf_docs.v1.0.0", db_path="chroma_db"):
        load_dotenv()
        self.base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..','..'))  # one level up
        self.db_path = os.path.join(self.base_dir, db_path)
        self.collection_name = collection_name
        self.langchain_vector_store = Chroma(
            collection_name=self.collection_name,
            persist_directory=self.db_path,
            # embedding_function will be set dynamically if needed
        )
        # core chroma client
        self.client = chromadb.PersistentClient(path=self.db_path)
        self.collection=self.client.get_or_create_collection(name=collection_name,embedding_function=None)
        self._embedding_model = None
        

    def _get_embedding_model(self):
        """Lazily load embedding model."""
        if self._embedding_model is None:
            embeder = chain_chan_embedder(model="amazon-embedding-v2")
            self._embedding_model = embeder
        return self._embedding_model
    
    # def get_pdf_list(self, path='pdf/', pattern='*.pdf',role="public"):
        

    def add_pdf_vdb(self,file, path):
        self.langchain_vector_store._embedding_function = self._get_embedding_model()
        doc_chunks = pdf_chunks_chain_chan(path=path, file=file)
        # Now inject the embedding model dynamically
        self.langchain_vector_store.add_documents(documents=doc_chunks)
        
    def delete_pdf_vdb(self, file):
        file_path="..\\pdf\\" + file
        ids = self.get_metadata_id(file_path)
        self.collection.delete(ids=ids)

    def list_all_documents(self,role="public"):
        """Return all document IDs and metadata."""
        # dict_keys(['ids', 'embeddings', 'documents', 'uris', 'included', 'data', 'metadatas']), dont include ids its default
        return self.collection.get(include=["metadatas"])

    def get_metadata_id(self, source_path) -> dict:
        return self.collection.get( where={"source": source_path}).get("ids", [])
    
    def get_pdf(self, source_path) -> dict:
        source_path = "..\\pdf\\" + source_path  # Assuming the path is relative to the base directory
        print(self.collection.get(where={"source": source_path}).get("metadatas", [{}]))


    def update_metadata_role(self, doc_name, new_role_list):
        source_path = "..\\pdf\\" + doc_name  # Assuming the path is relative to the base directory
        update_ids = self.get_metadata_id(source_path)
        new_roles = {"is_" + nr.lower(): True for nr in new_role_list}
        new_roles_list = [new_roles for _ in range(len(update_ids))]
        self.invalidate_all_roles(update_ids)
        if not update_ids:
            raise ValueError(f"No document found with source path: {source_path}")
        self.collection.update(
            ids=update_ids,
            metadatas=new_roles_list
        )
    
    #one can only be one role
    def invalidate_all_roles(self, ids):
        """Invalidate all roles for the given document IDs."""
        if not ids:
            return
        # Create a metadata dict with all roles set to False
        one_sample = self.collection.get(ids=ids[0]).get("metadatas", [{}])[0]
        if one_sample:
           one_schema = [key for key in one_sample.keys() if key.startswith("is_")] 
        self.collection.update(
            ids=ids,
            metadatas= [{key: False for key in one_schema} for _ in range(len(ids))]
        )
        
    def pdf_list(self) -> list[dict]:
        metadatas = self.collection.get(include=["metadatas"]).get("metadatas", [])
        
        unique_sources = {}

        for meta in metadatas:
            source = meta.get("source")
            if not source:
                continue

            # Extract just the file name
            file_name = os.path.basename(source)

            if file_name not in unique_sources:
                entry = {
                    "source": file_name,  # use only file name here
                    "page": meta.get("page", 0),
                    "max_pages": meta.get("max_pages", 0),
                    "size_mb": meta.get("size_mb", 0),
                }

                # Include all is_* flags
                for key, value in meta.items():
                    if key.startswith("is_"):
                        entry[key] = value

                unique_sources[file_name] = entry

        return list(unique_sources.values())

    def sim_search(self, query: str, role: str = None, k: int = 2) -> list[dict]:
        """Perform a similarity search on the vector store."""
        if not self.langchain_vector_store._embedding_function:
            self.langchain_vector_store._embedding_function = self._get_embedding_model()
        if role:
            is_role = "is_" + role.lower()
            result=self.langchain_vector_store.similarity_search(
                query,
                k=k,
                filter={is_role: True}
            )
        else: #we don need to filter by role
            result=self.langchain_vector_store.similarity_search(
                query,
                k=k
            )
        return result

   

# if __name__ == "__main__":

    # vector_store= chain_chan_vector_store().update_metadata_role("Trade Compliance.pdf", ["manager"])
    # vs= chain_chan_vector_store().get_pdf("COC.pdf")
    # chain_chan_vector_store().delete_pdf_vdb("COC.pdf")
    # vs= chain_chan_vector_store().get_pdf("COC.pdf")
    # chain_chan_vector_store().add_pdf_vdb(path='../pdf/', file='COC.pdf')
#     # metavdb = vector_store.list_all_documents()
#     # print("All documents:", metavdb["metadatas"])
    
    
    
    # Update role
    # manager.update_metadata_role(doc_id, "lemon")
    
    # # Verify update
    # print("After update:", manager.get_metadata_by_id(doc_id))