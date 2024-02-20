<?php
session_start(); // Rozpoczęcie sesji

// Ustanowienie połączenia z bazą danych
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'baza';
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);


// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    // Jeśli użytkownik nie jest zalogowany lub nie jest administratorem, przekieruj go do strony logowania lub innej strony informującej o braku dostępu.
    header('Location: brak_dostepu.php');
    exit(); // Zatrzymaj dalsze wykonywanie skryptu
}

// Sprawdzenie czy połączenie się powiodło
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Logika rejestracji
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Generowanie kodu autoryzacji
    $authorizationCode = generateAuthorizationCode();

    // Zabezpieczenie przed atakami SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);
    $email = $conn->real_escape_string($email);
    $authorizationCode = $conn->real_escape_string($authorizationCode);

    // Sprawdzenie czy użytkownik o podanej nazwie już istnieje
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "Użytkownik o podanej nazwie już istnieje.";
    } else {
        // Dodanie nowego użytkownika do bazy danych
        $sql = "INSERT INTO users (username, password, email, authorization_code) VALUES ('$username', '$password', '$email', '$authorizationCode')";
        if ($conn->query($sql) === TRUE) {
            echo "Rejestracja przebiegła pomyślnie!";
        } else {
            echo "Błąd podczas rejestracji: " . $conn->error;
        }
    }
}

// Funkcja do generowania kodu autoryzacji
function generateAuthorizationCode() {
    $code = mt_rand(10, 99) . mt_rand(100, 999);
    return $code;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Formularz rejestracji</title>
</head>
<body>
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
        width: 300px;
        margin: 0 auto;
    }
    label {
        font-weight: bold;
        color: #555;
        margin-bottom: 8px;
        display: block;
    }
    input[type="text"],
    input[type="password"],
    input[type="email"],
    input[type="submit"] {
        padding: 10px;
        width: calc(100% - 20px);
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    input[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background-color: #0056b3;
    }
</style>
<h2>Rejestracja</h2>
<form method="post" action="">
    <label>Nazwa użytkownika:</label><br>
    <input type="text" name="username"><br>
    <label>Hasło:</label><br>
    <input type="password" name="password"><br>
    <label>Email:</label><br>
    <input type="email" name="email"><br><br>
    <input type="submit" name="register" value="Zarejestruj">
</form>
</body>
</html>