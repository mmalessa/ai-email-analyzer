from fastapi import FastAPI
import requests

app = FastAPI()

OLLAMA_URL = "http://ollama:11434/api/embeddings"

@app.post("/embed")
async def embed(data: dict):

    text = data["text"]

    payload = {
        "model": "nomic-embed-text",
        "prompt": text
    }

    r = requests.post(OLLAMA_URL, json=payload)

    return r.json()
