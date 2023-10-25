<?php 

$_POST['action'];




function init() {
    $hostname = "localhost";
    $dbname = "restaurant_arredo";
    $user = "root";
    $pass = "root";
   
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pass);
        echo "connesso con successo";
    } catch(PDOException $e) {
        die("Impossibile connettersi al server");
    };
    $create_restaurant = "CREATE TABLE IF NOT EXISTS `restaurant`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(40) NOT NULL,
        `address` VARCHAR(60) NOT NULL,
        `phone` VARCHAR(60) NOT NULL,
        `opening_time` TEXT NOT NULL,
        PRIMARY KEY(`id`))
        ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;";
    $result = $pdo->exec($create_restaurant);
    if($result) {
        echo "Tabella creata";
    }
    $seeder_restaurant = 'INSERT INTO restaurant (name, address, phone, opening_time) VALUES ("Ristorante da Mario", "Via Roma, 123", "+39 123 456789", "{ )'


}
switch ($_POST["action"]) {
    case "init" : {
        init();
        break;
    }
    case "reservation" : {
        break;
    }
    case "vote" : {
        break;
    }

        
    
}



?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <header>
    <div class="container">
        <div class="jumbotron">
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="init">
        <button type="submit" class="btn mt-3 btn-primary">Init</button>
        
        </form>
        </div>
        
    </div>
    </header>
    <main>
        <div class="container">
            <form action="index.php" method="POST">
            <input type="hidden" name="action" value="reservation">
                <div class="mb-3 mt-5">
                    <label for="nome_cliente" class="form-label">Nome Cliente</label>
                    <input type="text" class="form-control" id="nome_cliente" aria-describedby="emailHelp">
                    <div id="nome_cliente" class="form-text">Grazie per questa informazione</div>
                </div>
                <div class="mb-5">
                    <label for="prenotation" class="form-label">Data Prenotazione</label>
                    <input type="text" class="form-control" id="prenotation">
                </div>
                <div class="mb-5">
                    <label for="hour" class="form-label">Orario</label>
                    <input type="number" class="form-control" id="hour">
                </div>
                <div class="mb-5">
                    <label for="numero_persone" class="form-label">Numero Persone</label>
                    <input type="number" class="form-control" id="numero_perswone">
                </div>
            </form>
            <form action="index.php" method="POST">
            <input type="hidden" name="action" value="vote">
            <div class=" form-floating">
                    <textarea class="form-control" placeholder="Leave a comment here" id="vote" style="height: 100px"></textarea>
                    <label for="vote">Lascia un commento</label>
                </div>
                <button type="submit" class="btn mt-3 btn-primary">Submit</button>
            </form>
            
        </div>

    </main>
</body>
</html>