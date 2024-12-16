<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GhibliMovies";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse e-mail est invalide.";
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Le pseudo existe déjà. Veuillez en choisir un autre.";
        } else {
            $sql = "INSERT INTO users (username, password, role) VALUES (?, SHA2(?, 256), 'user')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="movie-container" style="max-width: 400px; margin: auto; padding: 2rem; text-align: center;">
        <h1>S'inscrire</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Pseudo" required style="margin-bottom: 1rem; padding: 0.5rem; width: 100%;">
            <input type="password" name="password" placeholder="Mot de passe" required style="margin-bottom: 1rem; padding: 0.5rem; width: 100%;">
            <input type="email" name="email" placeholder="Adresse e-mail" required style="margin-bottom: 1rem; padding: 0.5rem; width: 100%;">
            <button type="submit" style="padding: 0.5rem 1rem; background-color: #92bfb1; border: none; cursor: pointer;">S'inscrire</button>
        </form>
        <p style="margin-top: 1rem;">Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>.</p>
    </div>
</body>
</html>
