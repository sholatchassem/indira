<?php

session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'magasinnier') {
    header("Location: magasinier.php"); // Rediriger vers login si non connecté
    exit();
}
//Desactiver les bouttons
if ($_SESSION['user_role'] == 'administrateur') {
    echo "<td><a href='modifier_produit.php?id=" . $produit['id'] . "'>Modifier</a></td>";
    echo "<td><a href='supprimer_produit.php?id=" . $produit['id'] . "'>Supprimer</a></td>";
}
include "sidebar.php"; // Sidebar spécifique magasinier

// Connexion à la base de données
$host = "localhost";
$dbname = "gestionstock";
$username = "root"; 
$password = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
//demande d'acces
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'magasinier') {
    header("Location: ENREGISTREMENT.php"); // Rediriger vers login si non connecté
    exit();
}

include "db-connexion.php"; // Connexion à la base

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utilisateur_id = $_SESSION['user_id'];
    $demande = $_POST['demande'];

    $query = $conn->prepare("INSERT INTO demandes_acces (utilisateur_id, demande) VALUES (?, ?)");
    $query->execute([$utilisateur_id, $demande]);

    echo "<script>alert('Votre demande a été envoyée à l\'administrateur.');</script>";
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GestionStock";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8");

    // Vérifier si la table Categories existe, sinon la créer
    $stmt = $conn->query("SHOW TABLES LIKE 'Categories'");
    if ($stmt->rowCount() == 0) {
        $sql = "CREATE TABLE Categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            description TEXT
        )";
        $conn->exec($sql);
        
        // Ajouter quelques catégories par défaut
        $conn->exec("INSERT INTO Categories (nom, description) VALUES 
            ('Informatique', 'Matériel informatique, périphériques et accessoires'),
            ('Bureautique', 'Fournitures de bureau et papeterie'),
            ('Mobilier', 'Mobilier de bureau et aménagement')
        ");
    }
    
    // Vérifier si la colonne categorie_id existe dans la table Produits, sinon l'ajouter
    $stmt = $conn->query("SHOW COLUMNS FROM Produits LIKE 'categorie_id'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE Produits ADD COLUMN categorie_id INT, ADD FOREIGN KEY (categorie_id) REFERENCES Categories(id)";
        $conn->exec($sql);
    }
    
    // Vérifier si les colonnes quantite_theorique et ecart existent dans la table Inventaires, sinon les ajouter
    $stmt = $conn->query("SHOW COLUMNS FROM Inventaires LIKE 'quantite_theorique'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE Inventaires ADD COLUMN quantite_theorique INT AFTER quantite";
        $conn->exec($sql);
    }
    
    $stmt = $conn->query("SHOW COLUMNS FROM Inventaires LIKE 'ecart'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE Inventaires ADD COLUMN ecart INT AFTER quantite_theorique";
        $conn->exec($sql);
    }
    
} catch(PDOException $e) {
    echo "Erreur de connexion ou d'initialisation: " . $e->getMessage();
    die();
}

// Fonction pour échapper les caractères HTML
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Traitement des catégories
// Ajouter une catégorie
if (isset($_POST['submit_categorie'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO Categories (nom, description) VALUES (?, ?)");
        $stmt->execute([
            $_POST['nom_categorie'],
            $_POST['description_categorie']
        ]);
        $message = "Catégorie ajoutée avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Modifier une catégorie
if (isset($_POST['update_categorie'])) {
    try {
        $stmt = $conn->prepare("UPDATE Categories SET nom = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $_POST['nom_categorie'],
            $_POST['description_categorie'],
            $_POST['categorie_id']
        ]);
        $message = "Catégorie mise à jour avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Supprimer une catégorie
if (isset($_POST['delete_categorie'])) {
    try {
        // Vérifier si des produits sont associés à cette catégorie
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Produits WHERE categorie_id = ?");
        $stmt->execute([$_POST['categorie_id']]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            $error = "Impossible de supprimer cette catégorie car des produits y sont associés.";
        } else {
            $stmt = $conn->prepare("DELETE FROM Categories WHERE id = ?");
            $stmt->execute([$_POST['categorie_id']]);
            $message = "Catégorie supprimée avec succès!";
        }
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Traitement de la suppression d'un fournisseur
if (isset($_POST['delete_fournisseur'])) {
    try {
        // Vérifier si des produits sont associés à ce fournisseur
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Produits WHERE fournisseur_id = ?");
        $stmt->execute([$_POST['fournisseur_id']]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            $error = "Impossible de supprimer ce fournisseur car des produits y sont associés.";
        } else {
            $stmt = $conn->prepare("DELETE FROM Fournisseurs WHERE id = ?");
            $stmt->execute([$_POST['fournisseur_id']]);
            $message = "Fournisseur supprimé avec succès!";
        }
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Traitement de la modification d'un fournisseur
if (isset($_POST['update_fournisseur'])) {
    try {
        $stmt = $conn->prepare("UPDATE Fournisseurs SET nom = ?, contact = ?, adresse = ? WHERE id = ?");
        $stmt->execute([
            $_POST['nom_fournisseur'],
            $_POST['contact_fournisseur'],
            $_POST['adresse_fournisseur'],
            $_POST['fournisseur_id']
        ]);
        $message = "Fournisseur mis à jour avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Traitement de la suppression d'un produit
if (isset($_POST['delete_produit'])) {
    try {
        // Vérifier si des entrées/sorties sont associées à ce produit
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Entrees WHERE produit_id = ?");
        $stmt->execute([$_POST['produit_id']]);
        $count_entrees = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Sorties WHERE produit_id = ?");
        $stmt->execute([$_POST['produit_id']]);
        $count_sorties = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Inventaires WHERE produit_id = ?");
        $stmt->execute([$_POST['produit_id']]);
        $count_inventaires = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count_entrees > 0 || $count_sorties > 0 || $count_inventaires > 0) {
            $error = "Impossible de supprimer ce produit car des entrées, sorties ou inventaires y sont associés.";
        } else {
            $stmt = $conn->prepare("DELETE FROM Produits WHERE id = ?");
            $stmt->execute([$_POST['produit_id']]);
            $message = "Produit supprimé avec succès!";
        }
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Traitement de la modification d'un produit
if (isset($_POST['update_produit'])) {
    try {
        $stmt = $conn->prepare("UPDATE Produits SET nom = ?, description = ?, prix = ?, marque = ?, fournisseur_id = ?, categorie_id = ? WHERE id = ?");
        $stmt->execute([
            $_POST['nom_produit'],
            $_POST['description_produit'],
            $_POST['prix_produit'],
            $_POST['marque_produit'],
            $_POST['fournisseur_produit'] ?: null,
            $_POST['categorie_produit'] ?: null,
            $_POST['produit_id']
        ]);
        $message = "Produit mis à jour avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Ajouter un fournisseur
if (isset($_POST['submit_fournisseur'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO Fournisseurs (nom, contact, adresse) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['nom_fournisseur'],
            $_POST['contact_fournisseur'],
            $_POST['adresse_fournisseur']
        ]);
        $message = "Fournisseur ajouté avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Ajouter un produit
if (isset($_POST['submit_produit'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO Produits (nom, description, quantite, prix, marque, fournisseur_id, categorie_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom_produit'],
            $_POST['description_produit'],
            $_POST['quantite_produit'],
            $_POST['prix_produit'],
            $_POST['marque_produit'],
            $_POST['fournisseur_produit'] ?: null,
            $_POST['categorie_produit'] ?: null
        ]);
        $message = "Produit ajouté avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Enregistrer une entrée de stock
if (isset($_POST['submit_entree'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO Entrees (produit_id, quantite, prix, date_entree) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['produit_entree'],
            $_POST['quantite_entree'],
            $_POST['prix_entree'],
            $_POST['date_entree']
        ]);
        
        // Mettre à jour la quantité du produit
        $stmt = $conn->prepare("UPDATE Produits SET quantite = quantite + ? WHERE id = ?");
        $stmt->execute([
            $_POST['quantite_entree'],
            $_POST['produit_entree']
        ]);
        
        $message = "Entrée de stock enregistrée avec succès!";
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Enregistrer une sortie de stock
if (isset($_POST['submit_sortie'])) {
    try {
        // Vérifier si la quantité est suffisante
        $stmt = $conn->prepare("SELECT quantite FROM Produits WHERE id = ?");
        $stmt->execute([$_POST['produit_sortie']]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit['quantite'] < $_POST['quantite_sortie']) {
            $error = "Erreur: Stock insuffisant!";
        } else {
            $stmt = $conn->prepare("INSERT INTO Sorties (produit_id, quantite, prix, date_sortie) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_POST['produit_sortie'],
                $_POST['quantite_sortie'],
                $_POST['prix_sortie'],
                $_POST['date_sortie']
            ]);
            
            // Mettre à jour la quantité du produit
            $stmt = $conn->prepare("UPDATE Produits SET quantite = quantite - ? WHERE id = ?");
            $stmt->execute([
                $_POST['quantite_sortie'],
                $_POST['produit_sortie']
            ]);
            
            $message = "Sortie de stock enregistrée avec succès!";
        }
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Ajouter un inventaire
if (isset($_POST['submit_inventaire'])) {
    try {
        // Récupérer la quantité théorique (en stock) du produit
        $stmt = $conn->prepare("SELECT quantite FROM Produits WHERE id = ?");
        $stmt->execute([$_POST['produit_inventaire']]);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        $quantite_theorique = $produit['quantite'];
        
        // Calculer l'écart : quantité réelle - quantité théorique
        $quantite_reelle = intval($_POST['quantite_inventaire']);
        $ecart = $quantite_reelle - $quantite_theorique;
        
        // Insérer dans la table Inventaires
        $stmt = $conn->prepare("INSERT INTO Inventaires (produit_id, quantite, quantite_theorique, ecart, date_inventaire) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['produit_inventaire'],
            $quantite_reelle,
            $quantite_theorique,
            $ecart,
            $_POST['date_inventaire']
        ]);
        
        // Mettre à jour la quantité du produit avec la quantité réelle
        $stmt = $conn->prepare("UPDATE Produits SET quantite = ? WHERE id = ?");
        $stmt->execute([
            $quantite_reelle,
            $_POST['produit_inventaire']
        ]);
        
        $message = "Inventaire enregistré avec succès! Écart constaté: " . ($ecart >= 0 ? '+' . $ecart : $ecart);
    } catch(PDOException $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}

// Récupérer les statistiques pour le dashboard
try {
    // Total des produits
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Produits");
    $total_produits = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total des fournisseurs
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Fournisseurs");
    $total_fournisseurs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total des catégories
     $stmt = $conn->query("SELECT COUNT(*) as total FROM Categories");
    $total_categories = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Produits en alerte de stock (moins de 10 unités)
    $stmt = $conn->query("SELECT COUNT(*) as total FROM Produits WHERE quantite < 10");
    $produits_alerte = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Dernières entrées
    $stmt = $conn->query("SELECT e.*, p.nom as produit_nom FROM Entrees e JOIN Produits p ON e.produit_id = p.id ORDER BY date_entree DESC LIMIT 5");
    $dernieres_entrees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Récupérer les fournisseurs pour les listes déroulantes
try {
    $stmt = $conn->query("SELECT * FROM Fournisseurs ORDER BY nom");
    $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Récupérer les catégories pour les listes déroulantes
try {
    $stmt = $conn->query("SELECT * FROM Categories ORDER BY nom");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

// Récupérer les produits pour les listes déroulantes
try {
    $stmt = $conn->query("SELECT * FROM Produits ORDER BY nom");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}



// Fonction pour afficher une page spécifique
function showPage($page) {
    switch($page) {
        case 'utilisateurs':
            // Inclure la page utilisateur
            include('pages/utilisateurs.php');
            break;
        case 'produits':
            // Inclure la page produits
            include('pages/produits.php');
            break;
        case 'inventaire':
            // Inclure la page inventaire
            include('pages/inventaire.php');
            break;
        default:
            // Page d'accueil par défaut
            include('pages/accueil.php');
    }
}
 //Insérer dans la table Utilisateurs
//  $stmt = $conn->prepare("INSERT INTO Utilisateurs (nom, email,role, active) VALUES (?, ?, ?, ?, ?)");
//  $stmt->execute([
    //  $_POST['nom'],
    //  $email,
    //  $role,
     
    // $_POST['active'],
//  ]);
// Récupérer la catégorie sélectionnée pour le filtre
$categorie_filter = isset($_GET['categorie']) ? intval($_GET['categorie']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        h2{
            color: var(--white);

        }
        
        .sidebar {
            width: 250px;
            background-color: var(--dark-blue);
            color: var(--white);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .main-content {
            flex-grow: 1;
            margin-left: 250px; /* Correspond à la largeur de la sidebar */
            padding: 20px;
            background-color: var(--light-gray);
            overflow-y: auto;
        }
        
        .form-editer {
            display: none;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        
        .edit-row {
            display: none;
        }
        
        .ligne-edit td {
            background-color: #e3f2fd;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        
        .confirmation-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            z-index: 1000;
            text-align: center;
        }
        
        .confirmation-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .confirmation-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .filter-bar {
            margin-bottom: 20px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .filter-bar select {
            margin-left: 10px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid var(--mouse-gray);
            width: auto;
        }
        
        .filter-bar button {
            margin-left: 10px;
        }
        
        /* Style pour les écarts d'inventaire */
        .ecart-positif {
            color: green;
            font-weight: bold;
        }
        
        .ecart-negatif {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Overlay et boîte de confirmation pour suppression -->
    <div id="confirmation-overlay" class="confirmation-overlay"></div>
    <div id="confirmation-box" class="confirmation-box">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer cet élément? Cette action est irréversible.</p>
        <div class="confirmation-actions">
            <button id="cancel-delete" class="btn-cancel">Annuler</button>
            <button id="confirm-delete" class="btn-delete">Supprimer</button>
        </div>
    </div>
    <!---Formulaire de demande d'acces-->
    <h2>Faire une Demande d'Accès</h2>
<form method="POST">
    <label for="demande">Expliquez votre demande :</label><br>
    <textarea name="demande" required></textarea><br>
    <button type="submit">Envoyer la demande</button>
</form>
    
    <!-- Formulaire caché pour la suppression -->
    <form id="delete-form" method="POST" action="" style="display: none;">
        <input type="hidden" id="delete-id" name="delete_id">
        <input type="hidden" id="delete-type" name="delete_type">
    </form>
    
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Gestion Stock</h2>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" data-section="dashboard">
                <i class="menu-icon">📊</i> Dashboard
            </div>
            <div class="menu-item" data-section="fournisseurs">
                <i class="menu-icon">🏢</i> Fournisseurs
            </div>
            <div class="menu-item" data-section="categories">
                <i class="menu-icon">🏷️</i> Catégories
            </div>
            <div class="menu-item" data-target="produits-submenu">
                <i class="menu-icon">📦</i> Produits
            </div>
            <div class="submenu" id="produits-submenu">
                <div class="submenu-item" data-section="ajouter-produit">
                    <i class="menu-icon">➕</i> Ajouter Produit
                </div>
                <div class="submenu-item" data-section="liste-produits">
                    <i class="menu-icon">📋</i> Liste des Produits
                </div>
                <div class="submenu-item" data-section="entrees-sorties">
                    <i class="menu-icon">🔄</i> Entrées/Sorties
                </div>
            </div>
            <div class="menu-item" data-section="inventaire">
                <i class="menu-icon">🔍</i> Inventaire
            </div>
            <div class="menu-item" data-section="Utilisateurs">
              <a href="utilisateurs" style= "color: white; text-decoration: none";> <i class="menu-icon">👤</i> Utilisateurs</a> 
            </div>
            
            
        </div>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">D</div>
              <button> <a href="Deconnexion" style ="color: white; text-decoration: none";> <div class="user-name">Deconnexion</div></a></button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (isset($message)): ?>
        <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Dashboard Home -->
        <div class="content-section active" id="dashboard">
            <div class="dashboard-header">
                <h1>Tableau de Bord</h1>
                <p>Bienvenue dans votre système de gestion de stock</p>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Aperçu du Stock</h2>
                <div class="card-content">
                    <div class="stats-container">
                        <div class="stats-card blue">
                            <h3>Total Produits</h3>
                            <p class="stats-value"><?php echo $total_produits ?? 0; ?></p>
                        </div>
                        <div class="stats-card green">
                            <h3>Fournisseurs</h3>
                            <p class="stats-value"><?php echo $total_fournisseurs ?? 0; ?></p>
                        </div>
                        <div class="stats-card purple">
                            <h3>Catégories</h3>
                            <p class="stats-value"><?php echo $total_categories ?? 0; ?></p>
                        </div>
                        <div class="stats-card orange">
                            <h3>Alertes Stock</h3>
                            <p class="stats-value"><?php echo $produits_alerte ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Dernières Entrées</h2>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($dernieres_entrees) && count($dernieres_entrees) > 0): ?>
                                <?php foreach($dernieres_entrees as $entree): ?>
                                <tr>
                                    <td><?php echo e($entree['produit_nom']); ?></td>
                                    <td><?php echo e($entree['quantite']); ?></td>
                                    <td><?php echo e($entree['prix']); ?> €</td>
                                    <td><?php echo date('d/m/Y', strtotime($entree['date_entree'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center;">Aucune entrée enregistrée</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fournisseurs Section
        <div class="content-section" id="fournisseurs">
            <div class="dashboard-header">
                <h1>Gestion des Fournisseurs</h1>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Ajouter un Fournisseur</h2>
                <div class="card-content">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nom_fournisseur">Nom</label>
                            <input type="text" id="nom_fournisseur" name="nom_fournisseur" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_fournisseur">Contact</label>
                            <input type="text" id="contact_fournisseur" name="contact_fournisseur">
                        </div>
                        <div class="form-group">
                            <label for="adresse_fournisseur">Adresse</label>
                            <textarea id="adresse_fournisseur" name="adresse_fournisseur" rows="3"></textarea>
                        </div>
                        <button type="submit" name="submit_fournisseur">Enregistrer</button>
                    </form>
                </div>
            </div> -->
            
            <div class="dashboard-card">
                <h2 class="card-title">Liste des Fournisseurs</h2>
                <div class="card-content">
                    <table id="liste-fournisseurs">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Adresse</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->query("SELECT * FROM Fournisseurs ORDER BY id DESC");
                                $fournisseurs_liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($fournisseurs_liste) > 0) {
                                    foreach($fournisseurs_liste as $fournisseur) {
                                        $fournisseur_id = $fournisseur['id'];
                                        
                                        echo "<tr id='fournisseur-row-{$fournisseur_id}' class='data-row'>";
                                        echo "<td>" . e($fournisseur['id']) . "</td>";
                                        echo "<td>" . e($fournisseur['nom']) . "</td>";
                                        echo "<td>" . e($fournisseur['contact']) . "</td>";
                                        echo "<td>" . e($fournisseur['adresse']) . "</td>";
                                        echo "<td>";
                                        echo "<button type='button' class='btn-edit' data-id='{$fournisseur_id}' data-type='fournisseur'>Modifier</button> ";
                                        echo "<button type='button' class='btn-delete' onclick=\"confirmerSuppression('fournisseur_id', {$fournisseur_id}, 'delete_fournisseur')\">Supprimer</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                        
                                        // Formulaire d'édition (caché par défaut)
                                        echo "<tr id='edit-form-fournisseur-{$fournisseur_id}' class='edit-row'>";
                                        echo "<td colspan='5'>";
                                        echo "<div class='form-editer'>";
                                        echo "<form method='POST' action=''>";
                                        echo "<input type='hidden' name='fournisseur_id' value='{$fournisseur_id}'>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='nom_fournisseur_{$fournisseur_id}'>Nom</label>";
                                        echo "<input type='text' id='nom_fournisseur_{$fournisseur_id}' name='nom_fournisseur' value='" . e($fournisseur['nom']) . "' required>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='contact_fournisseur_{$fournisseur_id}'>Contact</label>";
                                        echo "<input type='text' id='contact_fournisseur_{$fournisseur_id}' name='contact_fournisseur' value='" . e($fournisseur['contact']) . "'>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='adresse_fournisseur_{$fournisseur_id}'>Adresse</label>";
                                        echo "<textarea id='adresse_fournisseur_{$fournisseur_id}' name='adresse_fournisseur' rows='3'>" . e($fournisseur['adresse']) . "</textarea>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-actions'>";
                                        echo "<button type='button' class='btn-cancel' data-id='{$fournisseur_id}' data-type='fournisseur'>Annuler</button>";
                                        echo "<button type='submit' name='update_fournisseur'>Enregistrer</button>";
                                        echo "</div>";
                                        
                                        echo "</form>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center;'>Aucun fournisseur trouvé</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='5' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Catégories Section
        <div class="content-section" id="categories">
            <div class="dashboard-header">
                <h1>Gestion des Catégories</h1>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Ajouter une Catégorie</h2>
                <div class="card-content">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nom_categorie">Nom</label>
                            <input type="text" id="nom_categorie" name="nom_categorie" required>
                        </div>
                        <div class="form-group">
                            <label for="description_categorie">Description</label>
                            <textarea id="description_categorie" name="description_categorie" rows="3"></textarea>
                        </div>
                        <button type="submit" name="submit_categorie">Enregistrer</button>
                    </form>
                </div>
            </div> -->
            
            <div class="dashboard-card">
                <h2 class="card-title">Liste des Catégories</h2>
                <div class="card-content">
                    <table id="liste-categories">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $conn->query("SELECT * FROM Categories ORDER BY nom");
                                $categories_liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($categories_liste) > 0) {
                                    foreach($categories_liste as $categorie) {
                                        $categorie_id = $categorie['id'];
                                        
                                        echo "<tr id='categorie-row-{$categorie_id}' class='data-row'>";
                                        echo "<td>" . e($categorie['id']) . "</td>";
                                        echo "<td>" . e($categorie['nom']) . "</td>";
                                        echo "<td>" . e($categorie['description']) . "</td>";
                                        echo "<td>";
                                        echo "<button type='button' class='btn-edit' data-id='{$categorie_id}' data-type='categorie'>Modifier</button> ";
                                        echo "<button type='button' class='btn-delete' onclick=\"confirmerSuppression('categorie_id', {$categorie_id}, 'delete_categorie')\">Supprimer</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                        
                                        // Formulaire d'édition (caché par défaut)
                                        echo "<tr id='edit-form-categorie-{$categorie_id}' class='edit-row'>";
                                        echo "<td colspan='4'>";
                                        echo "<div class='form-editer'>";
                                        echo "<form method='POST' action=''>";
                                        echo "<input type='hidden' name='categorie_id' value='{$categorie_id}'>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='nom_categorie_{$categorie_id}'>Nom</label>";
                                        echo "<input type='text' id='nom_categorie_{$categorie_id}' name='nom_categorie' value='" . e($categorie['nom']) . "' required>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='description_categorie_{$categorie_id}'>Description</label>";
                                        echo "<textarea id='description_categorie_{$categorie_id}' name='description_categorie' rows='3'>" . e($categorie['description']) . "</textarea>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-actions'>";
                                        echo "<button type='button' class='btn-cancel' data-id='{$categorie_id}' data-type='categorie'>Annuler</button>";
                                        echo "<button type='submit' name='update_categorie'>Enregistrer</button>";
                                        echo "</div>";
                                        
                                        echo "</form>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' style='text-align: center;'>Aucune catégorie trouvée</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='4' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ajouter Produit Section -
        <div class="content-section" id="ajouter-produit">
            <div class="dashboard-header">
                <h1>Ajouter un Produit</h1>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Informations du Produit</h2>
                <div class="card-content">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nom_produit">Nom</label>
                            <input type="text" id="nom_produit" name="nom_produit" required>
                        </div>
                        <div class="form-group">
                            <label for="description_produit">Description</label>
                            <textarea id="description_produit" name="description_produit" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="quantite_produit">Quantité initiale</label>
                            <input type="number" id="quantite_produit" name="quantite_produit" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="prix_produit">Prix</label>
                            <input type="number" id="prix_produit" name="prix_produit" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="marque_produit">Marque</label>
                            <input type="text" id="marque_produit" name="marque_produit">
                        </div>
                        <div class="form-group">
                            <label for="categorie_produit">Catégorie</label>
                            <select id="categorie_produit" name="categorie_produit">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach($categories as $categorie): ?>
                                <option value="<?php echo $categorie['id']; ?>"><?php echo e($categorie['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fournisseur_produit">Fournisseur</label>
                            <select id="fournisseur_produit" name="fournisseur_produit">
                                <option value="">Sélectionner un fournisseur</option>
                                <?php foreach($fournisseurs as $fournisseur): ?>
                                <option value="<?php echo $fournisseur['id']; ?>"><?php echo e($fournisseur['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="submit_produit">Enregistrer le Produit</button>
                    </form>
                </div>
            </div>
        </div>->

        <-- Liste des Produits Section -->
        <div class="content-section" id="liste-produits">
            <div class="dashboard-header">
                <h1>Liste des Produits</h1>
            </div>
            
            <div class="filter-bar">
                <form method="GET" action="" id="filter-form">
                    <label for="categorie-filter">Filtrer par catégorie:</label>
                    <select id="categorie-filter" name="categorie" onchange="document.getElementById('filter-form').submit()">
                        <option value="0">Toutes les catégories</option>
                        <?php foreach($categories as $categorie): ?>
                        <option value="<?php echo $categorie['id']; ?>" <?php echo ($categorie_filter == $categorie['id']) ? 'selected' : ''; ?>>
                            <?php echo e($categorie['nom']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="section" value="liste-produits">
                </form>
            </div>
            
            <div class="dashboard-card">
                <div class="card-content">
                    <table id="liste-produits-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                                <th>Marque</th>
                                <th>Fournisseur</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $where = "";
                                $params = [];
                                
                                if ($categorie_filter > 0) {
                                    $where = " WHERE p.categorie_id = ?";
                                    $params = [$categorie_filter];
                                }
                                
                                $stmt = $conn->prepare("SELECT p.*, f.nom as fournisseur_nom, c.nom as categorie_nom 
                                                       FROM Produits p 
                                                       LEFT JOIN Fournisseurs f ON p.fournisseur_id = f.id 
                                                       LEFT JOIN Categories c ON p.categorie_id = c.id
                                                       $where
                                                       ORDER BY p.id DESC");
                                $stmt->execute($params);
                                $produits_liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($produits_liste) > 0) {
                                    foreach($produits_liste as $produit) {
                                        $produit_id = $produit['id'];
                                        
                                        echo "<tr id='produit-row-{$produit_id}' class='data-row'>";
                                        echo "<td>" . e($produit['id']) . "</td>";
                                        echo "<td>" . e($produit['nom']) . "</td>";
                                        echo "<td>" . e($produit['categorie_nom'] ?? 'Non définie') . "</td>";
                                        echo "<td>" . e($produit['quantite']) . "</td>";
                                        echo "<td>" . e($produit['prix']) . " €</td>";
                                        echo "<td>" . e($produit['marque']) . "</td>";
                                        echo "<td>" . e($produit['fournisseur_nom'] ?? 'Non défini') . "</td>";
                                        echo "<td>";
                                        echo "<button type='button' class='btn-edit' data-id='{$produit_id}' data-type='produit'>Modifier</button> ";
                                        echo "<button type='button' class='btn-delete' onclick=\"confirmerSuppression('produit_id', {$produit_id}, 'delete_produit')\">Supprimer</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                        
                                        // Formulaire d'édition (caché par défaut)
                                        echo "<tr id='edit-form-produit-{$produit_id}' class='edit-row'>";
                                        echo "<td colspan='8'>";
                                        echo "<div class='form-editer'>";
                                        echo "<form method='POST' action=''>";
                                        echo "<input type='hidden' name='produit_id' value='{$produit_id}'>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='nom_produit_{$produit_id}'>Nom</label>";
                                        echo "<input type='text' id='nom_produit_{$produit_id}' name='nom_produit' value='" . e($produit['nom']) . "' required>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='description_produit_{$produit_id}'>Description</label>";
                                        echo "<textarea id='description_produit_{$produit_id}' name='description_produit' rows='3'>" . e($produit['description']) . "</textarea>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='prix_produit_{$produit_id}'>Prix</label>";
                                        echo "<input type='number' id='prix_produit_{$produit_id}' name='prix_produit' min='0' step='0.01' value='" . e($produit['prix']) . "' required>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='marque_produit_{$produit_id}'>Marque</label>";
                                        echo "<input type='text' id='marque_produit_{$produit_id}' name='marque_produit' value='" . e($produit['marque']) . "'>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='categorie_produit_{$produit_id}'>Catégorie</label>";
                                        echo "<select id='categorie_produit_{$produit_id}' name='categorie_produit'>";
                                        echo "<option value=''>Sélectionner une catégorie</option>";
                                        
                                        foreach($categories as $categorie) {
                                            $selected = ($categorie['id'] == $produit['categorie_id']) ? 'selected' : '';
                                            echo "<option value='" . $categorie['id'] . "' {$selected}>" . e($categorie['nom']) . "</option>";
                                        }
                                        
                                        echo "</select>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<label for='fournisseur_produit_{$produit_id}'>Fournisseur</label>";
                                        echo "<select id='fournisseur_produit_{$produit_id}' name='fournisseur_produit'>";
                                        echo "<option value=''>Sélectionner un fournisseur</option>";
                                        
                                        foreach($fournisseurs as $fournisseur) {
                                            $selected = ($fournisseur['id'] == $produit['fournisseur_id']) ? 'selected' : '';
                                            echo "<option value='" . $fournisseur['id'] . "' {$selected}>" . e($fournisseur['nom']) . "</option>";
                                        }
                                        
                                        echo "</select>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-group'>";
                                        echo "<p>Note: La quantité ne peut être modifiée que via les entrées/sorties ou l'inventaire.</p>";
                                        echo "</div>";
                                        
                                        echo "<div class='form-actions'>";
                                        echo "<button type='button' class='btn-cancel' data-id='{$produit_id}' data-type='produit'>Annuler</button>";
                                        echo "<button type='submit' name='update_produit'>Enregistrer</button>";
                                        echo "</div>";
                                        
                                        echo "</form>";
                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' style='text-align: center;'>Aucun produit trouvé</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='8' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Entrées/Sorties Section -->
        <div class="content-section" id="entrees-sorties">
            <div class="dashboard-header">
                <h1>Gestion des Entrées et Sorties</h1>
            </div>
            
            <div class="tabs">
                <div class="tab active" data-tab="entrees">Entrées de Stock</div>
                <div class="tab" data-tab="sorties">Sorties de Stock</div>
                <div class="tab" data-tab="historique">Historique</div>
            </div>
            
          <!-- Entrées de Stock 
            <div class="tab-content active" id="entrees">
                <div class="dashboard-card">
                    <h2 class="card-title">Nouvelle Entrée de Stock</h2>
                    <div class="card-content">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="produit_entree">Produit</label>
                                <select id="produit_entree" name="produit_entree" required>
                                    <option value="">Sélectionner un produit</option>
                                    <?php foreach($produits as $produit): ?>
                                    <option value="<?php echo $produit['id']; ?>"><?php echo e($produit['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantite_entree">Quantité</label>
                                <input type="number" id="quantite_entree" name="quantite_entree" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="prix_entree">Prix unitaire</label>
                                <input type="number" id="prix_entree" name="prix_entree" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="date_entree">Date d'entrée</label>
                                <input type="date" id="date_entree" name="date_entree" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <button type="submit" name="submit_entree">Enregistrer l'entrée</button>
                        </form>
                    </div>
                </div>
            </div>-->
            
            <!-- Sorties de Stock 
            <div class="tab-content" id="sorties">
                <div class="dashboard-card">
                    <h2 class="card-title">Nouvelle Sortie de Stock</h2>
                    <div class="card-content">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="produit_sortie">Produit</label>
                                <select id="produit_sortie" name="produit_sortie" required>
                                    <option value="">Sélectionner un produit</option>
                                    <?php foreach($produits as $produit): ?>
                                    <option value="<?php echo $produit['id']; ?>"><?php echo e($produit['nom'] . ' - Stock: ' . $produit['quantite']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quantite_sortie">Quantité</label>
                                <input type="number" id="quantite_sortie" name="quantite_sortie" min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="prix_sortie">Prix unitaire de vente</label>
                                <input type="number" id="prix_sortie" name="prix_sortie" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="date_sortie">Date de sortie</label>
                                <input type="date" id="date_sortie" name="date_sortie" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <button type="submit" name="submit_sortie">Enregistrer la sortie</button>
                        </form>
                    </div>
                </div>
            </div>-->
            
            <!-- Historique des Entrées/Sorties -->
            <div class="tab-content" id="historique">
                <div class="dashboard-card">
                    <h2 class="card-title">Historique des Entrées</h2>
                    <div class="card-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT e.*, p.nom as produit_nom 
                                                        FROM Entrees e 
                                                        JOIN Produits p ON e.produit_id = p.id 
                                                        ORDER BY e.date_entree DESC LIMIT 10");
                                    $entrees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($entrees) > 0) {
                                        foreach($entrees as $entree) {
                                            echo "<tr>";
                                            echo "<td>" . e($entree['id']) . "</td>";
                                            echo "<td>" . e($entree['produit_nom']) . "</td>";
                                            echo "<td>" . e($entree['quantite']) . "</td>";
                                            echo "<td>" . e($entree['prix']) . " €</td>";
                                            echo "<td>" . date('d/m/Y', strtotime($entree['date_entree'])) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' style='text-align: center;'>Aucune entrée trouvée</td></tr>";
                                    }
                                } catch(PDOException $e) {
                                    echo "<tr><td colspan='5' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="dashboard-card">
                    <h2 class="card-title">Historique des Sorties</h2>
                    <div class="card-content">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $conn->query("SELECT s.*, p.nom as produit_nom 
                                                        FROM Sorties s 
                                                        JOIN Produits p ON s.produit_id = p.id 
                                                        ORDER BY s.date_sortie DESC LIMIT 10");
                                    $sorties = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($sorties) > 0) {
                                        foreach($sorties as $sortie) {
                                            echo "<tr>";
                                            echo "<td>" . e($sortie['id']) . "</td>";
                                            echo "<td>" . e($sortie['produit_nom']) . "</td>";
                                            echo "<td>" . e($sortie['quantite']) . "</td>";
                                            echo "<td>" . e($sortie['prix']) . " €</td>";
                                            echo "<td>" . date('d/m/Y', strtotime($sortie['date_sortie'])) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' style='text-align: center;'>Aucune sortie trouvée</td></tr>";
                                    }
                                } catch(PDOException $e) {
                                    echo "<tr><td colspan='5' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventaire Section 
        <div class="content-section" id="inventaire">
            <div class="dashboard-header">
                <h1>Gestion de l'Inventaire</h1>
            </div>
            
            <div class="dashboard-card">
                <h2 class="card-title">Nouvel Inventaire</h2>
                <div class="card-content">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="produit_inventaire">Produit</label>
                            <select id="produit_inventaire" name="produit_inventaire" required>
                                <option value="">Sélectionner un produit</option>
                                <?php foreach($produits as $produit): ?>
                                <option value="<?php echo $produit['id']; ?>"><?php echo e($produit['nom'] . ' - Stock théorique: ' . $produit['quantite']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantite_inventaire">Quantité réelle comptée</label>
                            <input type="number" id="quantite_inventaire" name="quantite_inventaire" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="date_inventaire">Date d'inventaire</label>
                            <input type="date" id="date_inventaire" name="date_inventaire" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <button type="submit" name="submit_inventaire">Enregistrer l'inventaire</button>
                    </form>
                </div>
            </div>-->
            
            <div class="dashboard-card">
                <h2 class="card-title">Historique des Inventaires</h2>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produit</th>
                                <th>Quantité réelle</th>
                                <th>Quantité théorique</th>
                                <th>Écart</th>
                                <th>Date d'inventaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Récupérer les inventaires avec les écarts
                                $stmt = $conn->query("SELECT i.*, p.nom as produit_nom
                                                    FROM Inventaires i 
                                                    JOIN Produits p ON i.produit_id = p.id 
                                                    ORDER BY i.date_inventaire DESC");
                                $inventaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($inventaires) > 0) {
                                    foreach($inventaires as $inventaire) {
                                        // Afficher l'écart avec le style approprié
                                        $ecart = $inventaire['ecart'] ?? 0;
                                        $ecart_classe = $ecart < 0 ? 'ecart-negatif' : ($ecart > 0 ? 'ecart-positif' : '');
                                        
                                        echo "<tr>";
                                        echo "<td>" . e($inventaire['id']) . "</td>";
                                        echo "<td>" . e($inventaire['produit_nom']) . "</td>";
                                        echo "<td>" . e($inventaire['quantite']) . "</td>";
                                        echo "<td>" . e($inventaire['quantite_theorique']) . "</td>";
                                        echo "<td class='{$ecart_classe}'>" . ($ecart > 0 ? '+' : '') . $ecart . "</td>";
                                        echo "<td>" . date('d/m/Y', strtotime($inventaire['date_inventaire'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align: center;'>Aucun inventaire trouvé</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='6' style='text-align: center;'>Erreur: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- <h2>Page Utilisateur</h2> 
<p>Voici les informations concernant l'utilisateur...</p>

 Exemple de liste d'utilisateurs 
<table>
    <thead>
        <tr>
            <th>id</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    
    
<script>
$(document).ready(function(){
    // Quand on clique sur un lien dans la sidebar
    $(".menu a").click(function(e){
        e.preventDefault();  // Empêche le comportement par défaut du lien
        
        var page = $(this).attr('href').split('=')[1]; // Récupère le paramètre de la page
        
        $.ajax({
            url: "index.php?page=" + page, // Charge la page via AJAX
            method: "GET",
            success: function(response){
                // Remplace le contenu dans la div "content" avec le contenu de la page chargée
                $(".content").html(response);
            }
        });
    });
});
</script>
    utilisateur section

     <div class="dashboard-card"> 
                <h2 class="card-title">Utilisateur</h2>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>nom</th>
                                <th>email</th>
                                <th>role</th>
                                <th>active</th>
                                
                            </tr>
                        </thead>
                        <tbody>-->

                        
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle menu clicks
            const menuItems = document.querySelectorAll('.menu-item, .submenu-item');
            
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Handle submenu toggling
                    if (this.dataset.target) {
                        this.classList.toggle('active');
                        return;
                    }
                    
                    // Handle section navigation
                    if (this.dataset.section) {
                        // Remove active class from all menu items
                        menuItems.forEach(mi => mi.classList.remove('active'));
                        
                        // Add active class to clicked menu item
                        this.classList.add('active');
                        
                        // Hide all sections
                        document.querySelectorAll('.content-section').forEach(section => {
                            section.classList.remove('active');
                        });
                        
                        // Show selected section
                        const targetSection = document.getElementById(this.dataset.section);
                        if (targetSection) {
                            targetSection.classList.add('active');
                        }
                    }
                });
            });
            
            // Handle tab clicks
            const tabs = document.querySelectorAll('.tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab content
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    
                    // Show selected tab content
                    const targetContent = document.getElementById(this.dataset.tab);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });
            
            // Open products submenu by default
            const productsMenuItem = document.querySelector('[data-target="produits-submenu"]');
            productsMenuItem.classList.add('active');
            
            // Gestion des boutons Modifier
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    toggleEditForm(id, type);
                });
            });
            
            // Gestion des boutons Annuler dans les formulaires
            document.querySelectorAll('.btn-cancel').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    toggleEditForm(id, type);
                });
            });
            
            // Configuration du formulaire de suppression
            document.getElementById('cancel-delete').addEventListener('click', function() {
                hideConfirmation();
            });
            
            document.getElementById('confirmation-overlay').addEventListener('click', function() {
                hideConfirmation();
            });
            
            document.getElementById('confirm-delete').addEventListener('click', function() {
                const deleteForm = document.getElementById('delete-form');
                deleteForm.submit();
            });
        });
        
        // Fonction pour afficher/masquer les formulaires d'édition
        function toggleEditForm(id, type) {
            // Fermer tous les formulaires ouverts d'abord
            document.querySelectorAll('.edit-row').forEach(row => {
                row.style.display = 'none';
            });
            
            document.querySelectorAll('.data-row').forEach(row => {
                row.classList.remove('ligne-edit');
            });
            
            // Afficher ou masquer le formulaire sélectionné
            const formRow = document.getElementById(`edit-form-${type}-${id}`);
            const dataRow = document.getElementById(`${type}-row-${id}`);
            
            if (formRow && dataRow) {
                // Si le formulaire est déjà visible, le masquer
                if (formRow.style.display === 'table-row') {
                    formRow.style.display = 'none';
                    dataRow.classList.remove('ligne-edit');
                } else {
                    // Sinon, l'afficher
                    formRow.style.display = 'table-row';
                    dataRow.classList.add('ligne-edit');
                    
                    // S'assurer que le formulaire à l'intérieur est visible
                    const formElement = formRow.querySelector('.form-editer');
                    if (formElement) {
                        formElement.style.display = 'block';
                    }
                }
            }
        }
        
        // Fonctions pour la confirmation de suppression
        function confirmerSuppression(idField, id, submitName) {
            // Préparer le formulaire de suppression
            const deleteForm = document.getElementById('delete-form');
            
            // Vider le formulaire
            while (deleteForm.firstChild) {
                deleteForm.removeChild(deleteForm.firstChild);
            }
            
            // Ajouter le champ ID
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = idField;
            idInput.value = id;
            deleteForm.appendChild(idInput);
            
            // Ajouter le bouton de soumission
            const submitInput = document.createElement('input');
            submitInput.type = 'hidden';
            submitInput.name = submitName;
            submitInput.value = '1';
            deleteForm.appendChild(submitInput);
            
            // Afficher la confirmation
            document.getElementById('confirmation-overlay').style.display = 'block';
            document.getElementById('confirmation-box').style.display = 'block';
        }
        
        function hideConfirmation() {
            document.getElementById('confirmation-overlay').style.display = 'none';
            document.getElementById('confirmation-box').style.display = 'none';
        }
    </script>
</body>
</html>