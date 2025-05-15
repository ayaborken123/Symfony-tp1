import ollama

class Chatbot:
    def __init__(self):
        self.model = "mistral"  # ✅ Change ici pour utiliser le modèle que tu as téléchargé

    def get_response(self, message: str) -> str:
        response = ollama.chat(model=self.model, messages=[{"role": "user", "content": message}])
        return response['message']  # ✅ Correction de l'accès à la réponse
