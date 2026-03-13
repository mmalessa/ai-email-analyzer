FROM python:3.11-slim

RUN apt-get update && apt-get install -y \
    build-essential wget

WORKDIR /app

COPY requirements.txt .
RUN pip install -r requirements.txt

RUN wget https://dl.fbaipublicfiles.com/fasttext/supervised-models/lid.176.bin

COPY app.py .

CMD ["uvicorn","app:app","--host","0.0.0.0","--port","8090"]
