# Prompt Inspection Engine (PHP)

Moteur d'inspection de prompts structuré en multi-fichiers, avec séparation claire des responsabilités.

## Capacités
- Détection PII (nom probable, email, IBAN, téléphone, identifiant personnel)
- Détection secrets (API keys, tokens, passwords, private keys, connection strings)
- Détection données métier sensibles (RH, finance, juridique, clients, sécurité, code source)
- Classification automatique : public / confidentiel / critique
- Approche hybride : règles + NLP léger + scoring de type ML

## Lancement local
```bash
php -S localhost:8080 -t public
```

Puis ouvre :
```text
http://localhost:8080
```

## API
POST `/inspect`

Body JSON :
```json
{
  "prompt": "Analyse ce contrat confidentiel pour John Doe..."
}
```
