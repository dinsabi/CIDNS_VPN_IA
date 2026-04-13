# Policy Engine (PHP)

Moteur de gouvernance multi-fichiers en PHP pour décider si une interaction IA est autorisée,
bloquée, masquée, tokenisée, ou soumise à revue.

## API
POST `/policy/evaluate`

```json
{
  "context": {
    "business_unit": "Finance",
    "country": "BE",
    "data_classification": "critical",
    "model_type": "public_llm",
    "frameworks": ["GDPR", "NIS2"]
  }
}
```
