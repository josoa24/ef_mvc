<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajout de remboursement</title>
  <style>
      body { font-family: sans-serif; padding: 20px; }
      input, select, button { margin: 5px; padding: 5px; }
      table { border-collapse: collapse; width: 100%; margin-top: 20px; }
      th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; }
      </style>
</head>
<body>
    
    <h1>Ajout de remboursement</h1>
    
    <div>
    <select id="pret_id"></select>
    <input type="date" id="date_paiement" />
    <button onclick="ajouterRemboursement()">Valider</button>
</div>

<table id="table-remboursements">
    <thead>
        <tr>
            <th>Prêt</th>
            <th>Date Paiement</th>
            <th>Montant attendu</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script src="url.js" defer></script>
<script src="assets/js/remboursement.js" defer></script>
</body>
</html>
