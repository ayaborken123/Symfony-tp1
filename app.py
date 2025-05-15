from fastapi import FastAPI
import ollama

app = FastAPI()

@app.get("/chatbot")
def chatbot_api(message: str):
    response = ollama.chat(model="mistral", messages=[{"role": "user", "content": message}])
    return {"response": response['message']}
@app.get("/chatbot")
def chatbot_api(message: str):
    response = ollama.chat(model="mistral", messages=[{"role": "user", "content": message}])
    print("Réponse API FastAPI :", response)  # ✅ Debugging
    return {"response": response['message']}  # ✅ Vérifier ici

