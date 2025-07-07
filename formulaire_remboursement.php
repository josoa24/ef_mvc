<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// formulaire_remboursement.php
require_once __DIR__ . '/ws/models/Remboursement.php';
require_once __DIR__ . '/ws/models/Pret.php';

// Récupérer tous les prêts avec infos clients pour la liste déroulante
$prets = Pret::getAllWithClient();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Remboursement</title>
</head>
<body>
    <h1>Ajouter un remboursement</h1>
    <form method="POST" action="remboursements">
        <label for="pret_id">Prêt (client):</label>
        <select name="pret_id" id="pret_id" required>
            <option value="">-- Sélectionnez un prêt --</option>
            <?php foreach ($prets as $pret): ?>
                <option value="<?= htmlspecialchars($pret['id_pret']) ?>">
                    <?= htmlspecialchars($pret['nom_client']) ?> - Montant: <?= htmlspecialchars($pret['montant']) ?> Ar
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="date_paiement">Date de paiement :</label>
        <input type="date" id="date_paiement" name="date_paiement" required>
        <br><br>

        <button type="submit">Valider le remboursement</button>
    </form>
</body>
</html>
