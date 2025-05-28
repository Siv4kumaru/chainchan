import requests
from langchain_core.prompts import ChatPromptTemplate,MessagesPlaceholder
from time import time
import os

class chain_chan_memory:
    def __init__(self):
        self.api_base_url = os.getenv("LARAVEL_API_BASE_URL",None)
        self.api_token = os.getenv("LARAVEL_API_TOKEN",None)
        
        self._memory_data = None

    #use if u want in future
    @property
    def _api_headers(self):
        headers = {"Accept": "application/json", "Content-Type": "application/json"}
        if self.api_token:
            headers["Authorization"] = f"Bearer {self.api_token}"
        return headers
    
    def fetch_history(self, convo_id: str = None, user_id: int = None):
        """Fetch memory data from the API and return a ChatPromptTemplate with placeholders."""
        
        if not all([convo_id, user_id, self.api_base_url]):
            print("Memory Fetch Error: Missing critical info (convo_id or api_base_url).")
            return None

        api_url = f"{self.api_base_url}/api/chat-services/users/{user_id}/conversations/{convo_id}/messages"
        
        try:
            response = requests.get(api_url, timeout=10)
            response.raise_for_status()

            self._memory_data = response.json()
            print(f"Memory fetched successfully: {len(self._memory_data)} messages.")

            # Save history as a list of (sender, message) tuples
            history = []
            for item in self._memory_data:
                if item["sender"] == "user":
                    history.append(("human", item["text"]))
                elif item["sender"] == "ai":
                    history.append(("ai", item["text"]))
            
            # Store history for access if needed later

            # Return a prompt template using placeholders
            return history
        except requests.RequestException as e:
            print(f"Memory Fetch Error: {str(e)}")
            return None
            
    def add_memory(self, text: str, convo_id: str = None, user_id: int = None):
        """Add a new memory entry."""
        if not self.api_base_url or not text or not convo_id or not user_id:
            print("Memory Add Error: feed in all params.")
            return None

        api_url = f"{self.api_base_url}/api/chat-services/users/{user_id}/conversations/{convo_id}/messages"
        payload = {
            "text": text,
            "sender": "ai",  # Assuming the sender is always the AI
            "timestamp": time()  # Example timestamp, adjust as needed
        }
        
        try:
            response = requests.post(api_url,  json=payload, timeout=10)
            response.raise_for_status()
            print("Memory added successfully.")
            return "Success"
        except requests.RequestException as e:
            print(f"Memory Add Error: {str(e)}")
            return None
        
    
    