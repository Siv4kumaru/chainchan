from langchain.embeddings.base import Embeddings
from typing import List
import requests
import json
import os

# Step 1: Create your custom embedding class
class chain_chan_embedder(Embeddings):
    def __init__(self,model: str):
        self.base_url = os.getenv("LAMBDA_URL", None)
        self.api_key = os.getenv("SYNfull_API", None)
        self.model = model

    def embed_documents(self, texts: List[str]) -> List[List[float]]:
        return [self._get_embedding(text) for text in texts]

    def embed_query(self, text: str) -> List[float]:
        return self._get_embedding(text)

    def _get_embedding(self, prompt: str) -> List[float]:
        payload = {
            "api_key": self.api_key,
            "prompt": prompt,
            "model_id": self.model
        }
        headers = {"Content-Type": "application/json"}
        response = requests.post(self.base_url, headers=headers, data=json.dumps(payload))
        response.raise_for_status()
        result = response.json()
        return result["response"]["embedding"]
    


# # Step 2: Initialize the embedding class
# lambda_embedder = chain_chan_embedder(model="amazon-embedding-v2")

# # Step 3: Use the embedding class
# texts = ["Hello world", "This is a test"]
# embeddings = lambda_embedder.embed_documents(texts)
# print(embeddings[1])
