from fastapi import FastAPI
import fasttext

app = FastAPI()
model = fasttext.load_model("lid.176.bin")

@app.post("/detect")
async def detect_language(data: dict):

    text = data["text"]
    labels, probs = model.predict(text)
    lang = labels[0].replace("__label__", "")

    return {
        "language": lang,
        "confidence": float(probs[0])
    }
