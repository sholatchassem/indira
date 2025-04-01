<?php
$host = "localhost";
$dbname = "gestionstock";
$username = "root";  // Remplace si tu as un autre utilisateur
$password = "";      // Remplace si tu as un mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>