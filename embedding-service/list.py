import requests
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


def list_schemas():

    wait_for_weaviate()

    r = requests.get(f"{WEAVIATE_URL}/v1/schema")
    r.raise_for_status()

    classes = r.json()["classes"]

    if not classes:
        print("No schemas found")
        return

    print(f"Found {len(classes)} schema(s):\n")
    for c in classes:
        props = c.get("properties", [])
        print(f"  - {c['class']} ({len(props)} properties)")


if __name__ == "__main__":
    list_schemas()
