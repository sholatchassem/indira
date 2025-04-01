<?php
session_start();

// Connexion à la base de données
$host = "localhost";
$dbname = "gestionstock";
$username = "root";
$password_db = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification de la connexion
if (isset($_POST['submit_connexion'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification si l'utilisateur existe
    $query = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['active'] == 0) {
            echo "<script>alert('⚠️ Votre compte est désactivé. Contactez un administrateur !');</script>";
        } elseif ($mot_de_passe === $user['mot_de_passe']) { // ⚠️ Remplace par password_verify() si tu hashes les mots de passe
            // $_SESSION['user'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['nom'] = $user['nom']; // Stocke le nom

            // Redirection selon le rôle
            // if ($user['role'] == 'administrateur') {
            //    // Interface admin
            // } elseif ($user['role'] == 'magasinier') {
            //     header("Location: magasinier.php"); // Interface magasinier
            // }
            header("Location: admin.php"); 
            exit();
        } else {
            echo "<script>alert('❌ Email ou mot de passe incorrect !');</script>";
        }
    } else {
        echo "<script>alert('❌ Email ou mot de passe incorrect !');</script>";
    }
}
// if (isset($_POST['submit_connexion'])) {
//     $email = $_POST['email'];
//     $password = $_POST['mot_de_passe'];

//     // Vérifier si l'utilisateur existe
//     $query = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
//     $query->execute([$email]);
//     $user = $query->fetch();

//     if ($user && $password === $user['mot_de_passe']) {
//         if ($user['active'] == 0) {
//             echo "<script>alert('Compte non activé. Contactez un administrateur !');</script>";
//         } else {
//             // Stocker les informations en session
//             $_SESSION['user_id'] = $user['id'];
//             $_SESSION['user_email'] = $user['email'];
//             $_SESSION['user_role'] = $user['role']; // Stocker le rôle

//             // Redirection selon le rôle
//             if ($user['role'] == 'administrateur') {
//                 header("Location: admin.php"); // Page Admin
//             } else {
//                 header("Location: magasinier.php"); // Page Magasinier
//             }
//             exit();
//         }
//     } else {
//         echo "<script>alert('Email ou mot de passe incorrect !');</script>";
//     }
// }
?>
 <!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <h1>Bienvenue, <?php echo $_SESSION['utilisateurs']; ?> (Administrateur)</h1> 
    <a href="logout.php">Déconnexion</a>-->
    

    <link rel="stylesheet" href="ENREGISTREMENT.CSS">
</head>
<body>
      <section class="image">
        <header class="barenav">
            <nav class="contenair">
                <!-- <div class="logo"><img src="logo/gg.png" alt="" class="logoimage"></div> 
                <ul>
                    <li><a href="badji.html">HOME</a></li>
                    <li class="ENRE"><a href="" >ENREGISTREMENT</a></li>
                    <li><a href="DEPARTEMENT.HTML">DEPARTEMENT</a></li>
                    <li><a href="CONTACT.HTML">CONTACT</a></li>
                </ul> 
                <div class="barerecherche">
                    <table class="elementbarerecherche">
                        <tr>
                            <td><input type="text" class="input"></td>
                            <td><img src="ICONES/search_16dp_0000F5.png" alt="" class="boutonsearch"></td>
                        </tr>-->
                    </table>
                </div>
            </nav>
    </header>
    <div class="body">
          <div class="container"> 
           <div class="form">
           <div><img src="logosae.png" alt=""></div>
           
            
            <form method="POST" action="" style="height: 100%;">
            <div style="    height: 100%;
    display: flex;
    flex-direction: column;
    /* gap: 50px; */
    width: 100%;
    align-items: center;
    justify-content: space-around;">
            <div class="box">
            <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="email">
                
            </div>
          
            <div class="box">
            <label for="password">Password</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Mot de passe">
            </div>
            
            <div class="box">
            <button type="submit" name="submit_connexion">Connexion</button>
            <p style="margin-top:10px">Vous n'avez pas de compte? <span>
            <a href="inscription.php" class="btn-hover">Creer un compte</a>
            </span></p>
           
            </div>
            </div>
</form>

           </div>
         </div>
    </div>

</section>
    
</body>
</html>