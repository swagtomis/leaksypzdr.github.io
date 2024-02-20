<?php
// Połączenie z bazą danych - zakładając, że już istnieje połączenie z bazą danych
$servername = "localhost"; // Nazwa serwera
$username = "root"; // Nazwa użytkownika bazy danych
$password = ""; // Hasło użytkownika bazy danych
$database = "baza"; // Nazwa bazy danych

session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    // Jeśli użytkownik nie jest zalogowany lub nie jest administratorem, przekieruj go do strony logowania lub innej strony informującej o braku dostępu.
    header('Location: brak_dostepu.php');
    exit(); // Zatrzymaj dalsze wykonywanie skryptu
}



// Tworzenie połączenia z bazą danych
$connection = new mysqli($servername, $username, $password, $database);

// Sprawdzenie połączenia
if ($connection->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $connection->connect_error);
}

// Pobranie listy użytkowników
$query_users = "SELECT id, username, email FROM users";
$result_users = mysqli_query($connection, $query_users);

// Pobranie listy zbanowanych użytkowników
$query_banned_users = "SELECT id, username, email FROM banned_users";
$result_banned_users = mysqli_query($connection, $query_banned_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista użytkowników</title>
    <!-- Dodaj linki do styli CSS, jeśli są wymagane -->
    <style>
        /* Dodaj style CSS, jeśli są wymagane */
    </style>
</head>
<body>
    <h2>Lista użytkowników</h2>
    <h3>Zwykli użytkownicy</h3>
    <ul>
        <?php
        // Wyświetlenie listy użytkowników
        while ($row_users = mysqli_fetch_assoc($result_users)) {
            echo "<li>ID: ".$row_users['id']." | Username: ".$row_users['username']." | Email: ".$row_users['email']."</li>";
        }
        ?>
    </ul>

    <h3>Zbanowani użytkownicy</h3>
    <ul>
        <?php
        // Wyświetlenie listy zbanowanych użytkowników
        while ($row_banned_users = mysqli_fetch_assoc($result_banned_users)) {
            echo "<li>ID: ".$row_banned_users['id']." | Username: ".$row_banned_users['username']." | Email: ".$row_banned_users['email']."</li>";
        }
        ?>
    </ul>
    <!-- Dodaj inne elementy HTML, jeśli są wymagane -->
</body>
</html>

<?php
// Zamykanie połączenia z bazą danych
mysqli_close($connection);
?>

