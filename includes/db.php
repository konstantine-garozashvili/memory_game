
<?php
$servername = "172.20.10.2";  // Adresse IP de votre serveur MariaDB
$username = "linux-server";   // Nom d'utilisateur MariaDB
$password = "010203";         // Mot de passe MariaDB
$database = "memory_game";    // Nom de la base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
echo "Connexion réussie à la base de données memory_game";

// Fermer la connexion

?>

