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


def describe(class_name):

    wait_for_weaviate()

    r = requests.get(f"{WEAVIATE_URL}/v1/schema/{class_name}")

    if r.status_code == 404:
        print(f"Schema '{class_name}' not found")
        sys.exit(1)

    r.raise_for_status()

    schema = r.json()

    print(f"Class: {schema['class']}")
    print(f"Vectorizer: {schema.get('vectorizer', 'none')}")

    module_config = schema.get("moduleConfig", {})
    if module_config:
        print(f"Module config: {module_config}")

    props = schema.get("properties", [])
    if not props:
        print("\nNo properties")
        return

    print(f"\nProperties ({len(props)}):\n")
    for p in props:
        line = f"  - {p['name']}: {','.join(p['dataType'])}"
        if p.get("description"):
            line += f"  # {p['description']}"
        print(line)


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(f"Usage: python {sys.argv[0]} <ClassName>")
        sys.exit(1)

    describe(sys.argv[1])
