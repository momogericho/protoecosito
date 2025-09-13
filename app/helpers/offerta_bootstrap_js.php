<script>
  // Bootstrap dati per la mappa lato frontend
  window.__CSRF__ = <?= json_encode($csrf) ?>;
  window.__AZIENDA_ID__ = <?= (int)$aziendaId ?>;
  window.__MATERIALI_IDS__ = <?= json_encode(array_values($myIds)) ?>; // array di id numerici
</script>
<script src="/public/js/offerta.js"></script>