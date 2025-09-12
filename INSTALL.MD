# Installazione

1. Assicurarsi che PHP e un server web siano installati.
2. Impostare i permessi corretti sulla directory `storage/`:
   ```bash
   chown -R www-data:www-data storage
   chmod -R 750 storage
   ```
   Sostituire `www-data` con l'utente e il gruppo utilizzati dal server web.
3. Avviare l'applicazione tramite il server web.