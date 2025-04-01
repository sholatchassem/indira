<!-- connexion a la base de donnee -->
<?php
require "db-connexion.php"; // Connexion à la BDD

if (isset($_POST['submit-inscription'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $active= 0;
    $role= 'magasinier';
    // Vérifier si les champs sont remplis
    if (empty($nom) || empty($email) || empty($mot_de_passe)) {
        echo "<script>alert('Tous les champs sont obligatoires.');</script>";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Cet email est déjà utilisé.');</script>";
        } else {

            // Insérer dans la BDD
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, active, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $mot_de_passe, $active, $role] )) {
                echo "<script>alert('Inscription réussie !'); window.location.href='ENREGISTREMENT.php';</script>";
            } else {
                echo "<script>alert('Erreur lors de l\'inscription.');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Inscription</title>
    <style>
        /* Styles globaux */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('téléchargement (1).png') ;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
    
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background im
        }

        /* Conteneur du formulaire */
        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
            border radius:20px;
        }

        .form-container h2 {
            margin-bottom: 15px;
            font-size: 22px;
            color: #333;
        }

        /* Champs du formulaire */
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Bouton d'inscription */
        .btn {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        /* Lien de connexion */
        .login-link {
            margin-top: 10px;
            font-size: 14px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Inscription</h2>
        <form method="POST" action="inscription.php">
            <div class="form-group">
                <label for="name">Nom </label>
                <input type="text" id="name" name="nom" placeholder="Votre nom">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="mot_de_passe" placeholder="Mot de passe">
            </div>
            <button type="submit" name="submit-inscription" class="btn">S'inscrire</button>
        </form>
        <p class="login-link">Déjà un compte ? <a href="ENREGISTREMENT.php">Se connecter</a></p>
    </div>

</body>
</html>
