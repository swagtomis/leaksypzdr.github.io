<?php
session_start(); // Rozpoczęcie sesji

// Sprawdzenie, czy użytkownik jest już zalogowany
if (isset($_SESSION['username'])) {
    // Jeśli użytkownik jest zalogowany, przekieruj go do strony głównej
    header('Location: strona.php');
    exit(); // Zatrzymanie dalszego wykonywania skryptu
}

// Ustanowienie połączenia z bazą danych
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'baza';
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Sprawdzenie czy połączenie się powiodło
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Logika logowania
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Zabezpieczenie przed atakami SQL injection
    $username = stripslashes($username);
    $password = stripslashes($password);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Zapytanie do bazy danych
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Sprawdzenie kodu autoryzacyjnego
        if ($row['authorization_code'] == $_POST['authorization_code']) {
            $_SESSION['username'] = $username; // Ustawienie sesji
            header('Location: strona.php'); // Przekierowanie na strona.php
            exit(); // Zatrzymanie dalszego wykonywania skryptu
        } else {
            echo "Błędny kod autoryzacyjny";
        }
    } else {
        echo "Błędne dane logowania dc:swagtomis / diorekk";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Formularz logowania</title>
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
<h2>Logowanie</h2>
<form method="post" action="">
    <label>Nazwa użytkownika:</label><br>
    <input type="text" name="username"><br>
    <label>Hasło:</label><br>
    <input type="password" name="password"><br>
    <label>Kod autoryzacyjny:</label><br>
    <input type="text" name="authorization_code" required><br><br>
    <input type="submit" name="login" value="Zaloguj">
</form>
</body>
</html>