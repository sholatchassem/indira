<?php include "sidebar.php"; ?>
<?php
$host = 'localhost';
$dbname = 'gestionstock';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

// Vérifier si une activation/désactivation est demandée
if (isset($_GET['id']) && isset($_GET['active'])) {
    $id = $_GET['id'];
    $active = ($_GET['active'] === "oui") ? 1 : 0;

    // Vérifier si l'utilisateur est l'admin (Tchassem Indira)
    $stmtCheck = $conn->prepare("SELECT nom FROM Utilisateurs WHERE id = :id");
    $stmtCheck->execute(['id' => $id]);
    $utilisateur = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && $utilisateur['nom'] !== 'Tchassem Indira') {
        $stmt = $conn->prepare("UPDATE Utilisateurs SET active = :active WHERE id = :id");
        $stmt->execute(['active' => $active, 'id' => $id]);
    }

    header("Location: utilisateurs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="utilisateurs.css">
</head>
<body>
<div class="main-content">
    <h2>Liste des Utilisateurs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Active</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT * FROM Utilisateurs");
            $stmt->execute();
            $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($utilisateurs as $utilisateur) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($utilisateur['id']) . "</td>";
                echo "<td>" . htmlspecialchars($utilisateur['nom']) . "</td>";
                echo "<td>" . htmlspecialchars($utilisateur['email']) . "</td>";
                echo "<td>" . htmlspecialchars($utilisateur['role']) . "</td>";
                
                // Afficher "Oui" ou "Non" dans la colonne Active
                echo "<td>" . ($utilisateur['active'] ? 'Oui' : 'Non') . "</td>";

                // Protection de l'admin
                if ($utilisateur['nom'] === 'Tchassem Indira') {
                    echo "<td><strong>Admin protégé</strong></td>";
                } else {
                    echo "<td>";
                    echo "<a href='utilisateurs.php?id=" . $utilisateur['id'] . "&active=oui' class='btn-oui'>Oui</a> ";
                    echo "<a href='utilisateurs.php?id=" . $utilisateur['id'] . "&active=non' class='btn-non'>Non</a>";
                    echo "</td>";
                }

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<!--Demande d'acces du magasinier 
<h2>Demandes d'Accès</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Demande</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>-->
        <?php
        $query = $conn->prepare("SELECT d.id, u.email, d.demande, d.statut FROM demandes_acces d JOIN utilisateurs u ON d.utilisateur_id = u.id WHERE d.statut = 'en attente'");
        $query->execute();
        $demandes = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($demandes as $demande) {
            echo "<tr>";
            echo "<td>" . $demande['id'] . "</td>";
            echo "<td>" . $demande['email'] . "</td>";
            echo "<td>" . $demande['demande'] . "</td>";
            echo "<td>" . $demande['statut'] . "</td>";
            echo "<td>
                    <a href='gerer_demande.php?id=" . $demande['id'] . "&action=approuver'>Approuver</a>
                    <a href='gerer_demande.php?id=" . $demande['id'] . "&action=refuser'>Refuser</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

        </body>
        </html>