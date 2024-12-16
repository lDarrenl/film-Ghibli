<?php
session_start(); 

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    echo "<p id='welcome-message' style='text-align: center; color: #92bfb1; cursor: pointer;'>bienvenue , " . htmlspecialchars($_SESSION['username']) . "! tu est connectée en temps que " . htmlspecialchars($_SESSION['role']) . ".</p>";
}
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "GhibliMovies";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, release_date, duration, rating, image_url, desc_film FROM Movies";
$result = $conn->query($sql);

function getImagePath($imagePath) {
    if (file_exists($imagePath)) {
        return $imagePath;
    } else {
        return 'asset/img/default_image.jpg'; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>film ghibli</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <?php if (isset($_SESSION['username']) && isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin_edit.php">Modifier</a></li>
                <?php endif; ?>
                <li><a href="logout.php">déconnection</a></li>
            <?php else: ?>
                <li><a href="login.php">connection</a></li>
                <li><a href="register.php">S'inscrire</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="movie-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stars = round($row['rating']);
                echo "<div class='movie-box'>
                        <img src='{$row['image_url']}' alt='{$row['title']}'>
                        <h2>{$row['title']}</h2>
                        <p>Release Date: " . date("F j, Y", strtotime($row['release_date'])) . "</p>
                        <p>Duration: {$row['duration']} min</p>
                        <div class='rating'>";
                for ($i = 1; $i <= 10; $i++) {
                    echo $i <= $stars ? "<span class='star filled'>★</span>" : "<span class='star'>☆</span>";
                }
                echo "</div>
                    <button class='more-info-btn' data-id='{$row['id']}'>En savoir +</button>
                    </div>";

                echo "<div class='popup' id='popup-{$row['id']}'>
                        <div class='popup-content'>
                            <h2>{$row['title']}</h2>
                            <p>{$row['desc_film']}</p>
                            <button class='close-popup' data-id='{$row['id']}'>Fermer</button>
                        </div>
                    </div>";
            }
        } else {
            echo "<p>No movies found!</p>";
        }
        ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const welcomeMessage = document.getElementById("welcome-message");

        if (welcomeMessage) {
            setTimeout(() => {
                welcomeMessage.style.transition = "opacity 0.5s ease";
                welcomeMessage.style.opacity = "0";
                setTimeout(() => {
                    welcomeMessage.style.display = "none";
                }, 500); 
            }, 10000);

            welcomeMessage.addEventListener("click", function() {
                welcomeMessage.style.transition = "opacity 0.5s ease";
                welcomeMessage.style.opacity = "0";
                setTimeout(() => {
                    welcomeMessage.style.display = "none";
                }, 500); 
            });
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const moreInfoButtons = document.querySelectorAll('.more-info-btn');
        moreInfoButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = button.getAttribute('data-id');
                const popup = document.getElementById(`popup-${id}`);
                if (popup) {
                    popup.style.display = 'flex';
                }
            });
        });

        const closeButtons = document.querySelectorAll('.close-popup');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = button.getAttribute('data-id');
                const popup = document.getElementById(`popup-${id}`);
                if (popup) {
                    popup.style.display = 'none';
                }
            });
        });

        const popups = document.querySelectorAll('.popup');
        popups.forEach(popup => {
            popup.addEventListener('click', function(event) {
                if (event.target === popup) {
                    popup.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>
