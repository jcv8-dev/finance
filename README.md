# Ersatz für Buchhaltungsexceltabelle

```yml
# docker-compose.yaml
version: "3"
services:
  finance:
    image: jcv8/finance:latest
    container_name: finance
    ports:
      - "8026:80"
    restart: "unless-stopped"
    volumes:
      - finance-persistant:/var/www/html/assets/scripts/upload
volumes:
  finance-persistant:
``` 

## Anforderungen
- [X] Einnahmen/Ausgaben buchen
  - [X] Mit Kategorie, Konto, Kommentar
    - [X] Hinzufügen von Kategorien & Konten
  - [ ] Filtern von Buchungen
    - [X] Sortieren
    - [ ] Suchen
    - [ ] Filter
  - [X] Buchungen bearbeiten
  - [ ] Konten bearbeiten
- [ ] Auflistung von Monatlichen Summen nach Kategorie
  - [X] Tabelle
  - [ ] Graph
    - [ ] Nach Kategorien
    - [ ] Nach Substring in Kommentar
- [ ] Prognosen für Jahr
- [ ] Monatliche Sparrate/Restbudget
  - [ ] Sparrate festlegen
    - [ ] pauschal
    - [ ] prozentual
    - [X] hardcoded 
  - [X] Monatliches Restbudget anzeigen

## ToDo:

- Database Credentials validation feedback in ui
- Automatically create tables
