import requests
import json
import time

WEAVIATE_URL = "http://weaviate:8080"


def wait_for_weaviate():
    for _ in range(30):
        try:
            r = requests.get(f"{WEAVIATE_URL}/v1/.well-known/ready")
            if r.status_code == 200:
                return
        except:
            pass
        time.sleep(2)

    raise RuntimeError("Weaviate not ready")


def schema_exists():

    r = requests.get(f"{WEAVIATE_URL}/v1/schema")

    classes = r.json()["classes"]

    for c in classes:
        if c["class"] == "Incident":
            return True

    return False


def create_schema():

    with open("schema.json") as f:
        schema = json.load(f)

    r = requests.post(
        f"{WEAVIATE_URL}/v1/schema",
        json=schema
    )

    r.raise_for_status()


def bootstrap():

    wait_for_weaviate()

    if not schema_exists():
        print("Creating Weaviate schema...")
        create_schema()
    else:
        print("Schema already exists")


if __name__ == "__main__":
    bootstrap()
