from typing import Any, Dict, Iterator, List, Optional
import json
from pydantic import Field
import requests
import os
from langchain_core.callbacks import CallbackManagerForLLMRun
from langchain_core.language_models import BaseChatModel
from langchain_core.messages import AIMessage, AIMessageChunk, BaseMessage,HumanMessage
from langchain_core.messages.ai import UsageMetadata
from langchain_core.outputs import ChatGeneration, ChatGenerationChunk, ChatResult
from dotenv import load_dotenv
load_dotenv()  # Load environment variables from .env file, overriding existing ones if necessary
class chain_chan_models(BaseChatModel):
    model_name: str = Field(alias="model")
    temperature: Optional[float] = None
    max_tokens: Optional[int] = None
    timeout: Optional[int] = 10  # seconds
    stop: Optional[List[str]] = None
    max_retries: int = 2

    
    lambda_url: str = Field(
        default=os.getenv("LAMBDA_URL"),
        description="The URL of the Lambda function to call.",
    )
    api_key: str = Field(
        default=os.getenv("SYNfull_API"),
        description="The API key for authentication.",
    )

    def _generate(
    self,
    messages: List[BaseMessage],
    stop: Optional[List[str]] = None,
    run_manager: Optional[CallbackManagerForLLMRun] = None,
    **kwargs: Any,
    ) -> ChatResult:
        
        last_message = messages[-1]
        prompt_text = last_message.content
        ct_input_tokens = sum(len(message.content) for message in messages)

        url = os.getenv("LAMBDA_URL", self.lambda_url)
        payload = {
            "api_key": os.getenv("SYNfull-API",self.api_key),  # Replace this with your actual API key
            "prompt": prompt_text,
            "model_id": self.model_name,
            "model_params": {
                "max_tokens": self.max_tokens or 500,
                "temperature": self.temperature or 0.7
            }
        }

        headers = {"Content-Type": "application/json"}

        try:
            response = requests.post(url, headers=headers, data=json.dumps(payload), timeout=self.timeout or 10)
            response.raise_for_status()
            result = response.json()
            output_text = result["response"]["content"][0]["text"] 
        except Exception as e:
            output_text = f"Error: {str(e)}"

        ct_output_tokens = len(output_text)
        message = AIMessage(
            content=output_text,
            additional_kwargs={},
            response_metadata={
                "time_in_seconds": 3,
                "model_name": self.model_name,
            },
            usage_metadata={
                "input_tokens": ct_input_tokens,
                "output_tokens": ct_output_tokens,
                "total_tokens": ct_input_tokens + ct_output_tokens,
            },
        )

        generation = ChatGeneration(message=message)
        return ChatResult(generations=[generation])

    @property
    def _llm_type(self) -> str:
        return "AWS_LAMBDa_By_Syngenta"

    @property
    def _identifying_params(self) -> Dict[str, Any]:
        return {
            "model_name": self.model_name,
            "lambda_url": self.lambda_url
        }



# model = chain_chan_models(
#     model='claude-3-haiku', # or claude-3.5-sonnet
#     # temperature=0~1,
#     # max_tokens=500,
#     # max_retries=2,
# )
# response = model.invoke([HumanMessage(content="what is the meaning of life?")])

# print(response)
