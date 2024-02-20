<?php
// Dane do połączenia z bazą danych
$servername = "localhost"; // Nazwa serwera
$username = "root"; // Nazwa użytkownika bazy danych
$password = ""; // Hasło użytkownika bazy danych
$database = "baza"; // Nazwa bazy danych

// Tworzenie połączenia z bazą danych
$conn = new mysqli($servername, $username, $password, $database);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Nie udało się połączyć z bazą danych: " . $conn->connect_error);
}
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    // Jeśli użytkownik nie jest zalogowany lub nie jest administratorem, przekieruj go do strony logowania lub innej strony informującej o braku dostępu.
    header('Location: brak_dostepu.php');
    exit(); // Zatrzymaj dalsze wykonywanie skryptu
}


// Funkcja do zbanowania użytkownika
function zbanujUzytkownika($conn, $idUzytkownika) {
    // Przeniesienie zbanowanego użytkownika do tabeli banned_users
    $sql = "INSERT INTO banned_users SELECT * FROM users WHERE id = $idUzytkownika";
    if ($conn->query($sql) === TRUE) {
        // Usunięcie zbanowanego użytkownika z tabeli users
        $deleteSql = "DELETE FROM users WHERE id = $idUzytkownika";
        if ($conn->query($deleteSql) === TRUE) {
            echo "Użytkownik został zbanowany i przeniesiony do tabeli banned_users.";
        } else {
            echo "Błąd podczas usuwania zbanowanego użytkownika: " . $conn->error;
        }
    } else {
        echo "Błąd podczas banowania użytkownika: " . $conn->error;
    }
}

// Funkcja do odbanowania użytkownika (opcjonalnie)
function odbanujUzytkownika($conn, $idUzytkownika) {
    // Możesz dodać logikę odbanowania użytkownika, jeśli jest to wymagane w Twoim przypadku
}

// Sprawdzenie czy został przesłany identyfikator użytkownika do zbanowania lub odbanowania
if (isset($_GET['id']) && isset($_GET['action'])) {
    $idUzytkownika = $_GET['id'];
    $akcja = $_GET['action'];

    // Wywołanie odpowiedniej funkcji w zależności od akcji
    if ($akcja === 'zbanuj') {
        zbanujUzytkownika($conn, $idUzytkownika);
    } elseif ($akcja === 'odbanuj') {
        odbanujUzytkownika($conn, $idUzytkownika);
    } else {
        echo "Nieprawidłowa akcja.";
    }
}

// Zamykanie połączenia z bazą danych
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie użytkownikami</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 0 auto;
        }
        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }
        input[type="text"] {
            padding: 10px;
            width: calc(100% - 20px);
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Zarządzanie użytkownikami</h2>

    <h3>Zbanuj użytkownika</h3>
    <form action="bany.php" method="get">
        <input type="hidden" name="action" value="zbanuj">
        ID użytkownika do zbanowania: <input type="text" name="id">
        <input type="submit" value="Zbanuj użytkownika">
    </form>

    <h3>Odbanuj użytkownika</h3>
    <form action="bany.php" method="get">
        <input type="hidden" name="action" value="odbanuj">
        ID użytkownika do odbanowania: <input type="text" name="id">
        <input type="submit" value="Odbanuj użytkownika">
    </form>
</body>
</html>
