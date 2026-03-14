import requests
import sys
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


def delete(class_name):

    wait_for_weaviate()

    r = requests.delete(f"{WEAVIATE_URL}/v1/schema/{class_name}")

    if r.status_code == 404:
        print(f"Schema '{class_name}' not found")
        sys.exit(1)

    r.raise_for_status()

    print(f"Deleted schema: {class_name}")


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(f"Usage: python {sys.argv[0]} <ClassName>")
        sys.exit(1)

    delete(sys.argv[1])
