<!DOCTYPE html>
<?php
session_start(); // Rozpoczęcie sesji

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    // Jeśli użytkownik nie jest zalogowany, przekieruj go do strony logowania
    header('Location: login.php');
    exit(); // Zatrzymanie dalszego wykonywania skryptu
}

// Obsługa wylogowywania
if (isset($_POST['logout'])) {
    // Zakończenie sesji
    session_unset();
    session_destroy();
    // Przekierowanie użytkownika na stronę logowania
    header('Location: login.php');
    exit(); // Zatrzymanie dalszego wykonywania skryptu
}
?>
<html>
<head>
    <title>Wyszukiwanie gracza</title>
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* Styl dla linku "Wyloguj" */
        a {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            width: 5%;
            margin-top: 20px;
        }
        /* Styl dla kontenera użytkownika */
        #userContainer {
            position: fixed;
            top: 0;
            float:right;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        #userContainer img {
            vertical-align: middle;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }
        /* Styl dla motywu ciemnego */
        .dark-mode {
            background-color: #333; /* Kolor tła ciemnego motywu */
            color: #fff; /* Kolor tekstu dla ciemnego motywu */
            /* Dodaj dodatkowe style dla elementów w motywie ciemnym */
        }
        /* CSS dla przycisku zmiany motywu */
#toggleTheme {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    margin-top:10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    width: 8%;
    margin-bottom: 15px; /* Dodatkowy odstęp od elementów powyżej */
}

#toggleTheme:hover {
    background-color: #0056b3;
   
}

/* CSS dla modala */


    </style>
</head>
<body>
    <!-- Kontener użytkownika -->
    <div id="userContainer">
        <?php
            // Wyświetlenie nazwy zalogowanego użytkownika
            echo '<img src="zdj.png" alt="Zdjęcie użytkownika">';
            echo $_SESSION['username'];
        ?>
    </div>

    <h2>Wyszukiwanie gracza</h2>
    <form id="searchForm" method="post" action="">
        <label>Wpisz nick gracza:</label>
        <input type="text" id="nick" name="nick" placeholder="Wpisz nick gracza...">
        <input type="submit" value="Szukaj">
    </form>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="playerInfo"></p>
        </div>
    </div>
    <form method="post" action="">
        <input type="submit" name="logout" value="Wyloguj" id="wylg">
    </form>

    <?php
    // Dodatkowa zawartość tylko dla użytkownika o konkretnej nazwie
    if ($_SESSION['username'] === 'admin') {
        echo '
        <div id="nigger">
            <h3> Panel Administratora </h3>
            <a href="register.php">Rejestracja</a>
            <a href="bany.php">Bany</a>
            <a href="list.php">Lista Idiotow</a>
        </div>
        ';
    }
    ?>

    <!-- Przycisk zmiany motywu -->
    <button id="toggleTheme">Zmień motyw</button>

    <!-- Skrypty JavaScript -->
    <script>
        var modal = document.getElementById("myModal");
        var closeBtn = document.getElementsByClassName("close")[0];
        var form = document.getElementById("searchForm");

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var nickToFind = document.getElementById("nick").value;
            
            // Pobieranie danych gracza z pliku RAPY.TXT
            fetch('RAPY.TXT')
            .then(response => response.text())
            .then(data => {
                // Przetwarzanie danych gracza
                var playerData = data.split("\n").find(line => line.includes(nickToFind));
                if (playerData) {
                    var playerInfo = playerData.split(" ");
                    var playerNick = playerInfo[0];
                    var playerIP = playerInfo[1];
                    var playerDetails = "Znaleziono gracza: " + playerNick;
                    document.getElementById("playerInfo").innerHTML = playerDetails;
                    modal.style.display = "block";
                } else {
                    document.getElementById("playerInfo").innerHTML = "Gracz o podanym nicku nie został znaleziony.";
                    modal.style.display = "block";
                }
            })
            .catch(error => console.error('Błąd pobierania danych z pliku:', error));
        });

        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Funkcja do zmiany motywu
        function toggleDarkMode() {
            // Znajdź główny element body
            var body = document.body;

            // Zmień lub dodaj odpowiednie klasy CSS dla motywu ciemnego
            body.classList.toggle('dark-mode');
        }

        // Obsługa kliknięcia przycisku zmiany motywu
        document.getElementById("toggleTheme").addEventListener("click", function() {
            toggleDarkMode(); // Wywołanie funkcji zmiany motywu
        });
    </script>
</body>
</html>
