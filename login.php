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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT username, role FROM users WHERE username = ? AND password = SHA2(?, 256)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>connection</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="movie-container" style="max-width: 400px; margin: auto; padding: 2rem; text-align: center;">
        <h1>se connecté</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required style="margin-bottom: 1rem; padding: 0.5rem; width: 100%;">
            <input type="password" name="password" placeholder="Password" required style="margin-bottom: 1rem; padding: 0.5rem; width: 100%;">
            <button type="submit" style="padding: 0.5rem 1rem; background-color: #92bfb1; border: none; cursor: pointer;">connection</button>
        </form>
    </div>
</body>
</html>