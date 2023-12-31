<?php

$file = 'export.csv';

if (($_POST)) {
    $content = downloadFile();
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    echo $content;
    exit; 
}





// READING ACTION VALOR ///////////////////////////////////////////////////
$_POST['action'];





// FUNCTION FOR GETTING DATABASE ROOT ///////////////////////////////////////////////////////
function getPdo()
{
    $pdo = null;
    $hostname = "localhost";
    $dbname = "restaurant_arredo";
    $user = "root";
    $pass = "root";
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pass);
    } catch (PDOException $e) {
        die("Impossibile connettersi al server");
    };
    return $pdo;
}
function downloadFile() {
    
    $pdo = getPdo();
    
    $tableToShow = "votes";
    $queryToExport = "SELECT * FROM `$tableToShow`";
    $toExport = $pdo->query($queryToExport);
    $gotdata = $toExport->fetchAll(PDO::FETCH_NUM); 
    $result = "";
    $f = fopen('php://output', 'w+');

    for($i = 0; $i < 10; $i++) {
        $result .= implode(";" ,$gotdata[$i]) . "\n";
        
    }
    return $result;
    
}
downloadFile();


// FUNCTION FOR CREATE FIRST TABLE AND POPULATE ///////////////////////////////////////////////////////
function init()
{
    $pdo = getPdo();
    $create_restaurant = "CREATE TABLE IF NOT EXISTS `restaurant`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(40) NOT NULL,
        `address` VARCHAR(60) NOT NULL,
        `phone` VARCHAR(60) NOT NULL,
        `opening_time` TEXT NOT NULL,
        PRIMARY KEY(`id`))
        ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;";
    $result = $pdo->exec($create_restaurant);
    if ($result) {
        echo "Tabella creata";
    }
    if (!count($pdo->query('SELECT * FROM restaurant')->fetchAll())) {
        $populate_restaurant_table = "
        INSERT INTO restaurant (name, address, phone, opening_time)
        VALUES ('Ristorante da Mario','Via Roma, 123','+39 123 456789','{\"lunedì\" : \"09:00 - 22:00\",
        \"martedì\" : \"09:00 - 22:00\",
        \"mercoledì\" : \"09:00 - 22:00\",
        \"giovedì\" : \"09:00 - 22:00\",
        \"venerdì\" : \"09:00 - 23:00\",
        \"sabato\" : \"10:00 - 23:00\",
        \"domenica\" : \"10:00 - 21:00\"
        }')
        ";
        // var_dump($seeder_restaurant);
        $pdo->exec($populate_restaurant_table);
    }
    $create_vote_table = "CREATE TABLE IF NOT EXISTS `votes`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `client_name` VARCHAR(40) NOT NULL,
        `vote` INT(10) NOT NULL,
        `comment` VARCHAR(60),
        PRIMARY KEY(`id`))
        ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;";
    $pdo->exec($create_vote_table);
    if (!count($pdo->query('SELECT * FROM votes')->fetchAll())) {
        $populate_vote_table = "
        INSERT INTO votes (client_name, vote, comment)
        VALUES ";

        $pdo->exec($populate_vote_table);
        $votes_data = file_get_contents("recensioni.json");
        $votes_decoded = json_decode($votes_data);

        foreach ($votes_decoded->recensioni as $key => $vote) {

            $populate_vote_table .= '("' . $vote->nome_cliente . '",' .  $vote->voto . ',"' . $vote->commento . '")';
            if ($key !== array_key_last($votes_decoded->recensioni)) {
                $populate_vote_table .= ", ";
            }
        }
        $populate_vote_table .= ";";
        $pdo->exec($populate_vote_table);
    }
    $create_prenotation_table = "CREATE TABLE IF NOT EXISTS `prenotations`(
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `client_name` VARCHAR(60) NOT NULL,
        `prenotation_date` VARCHAR(60) NOT NULL,
        `hour` VARCHAR(20) NOT NULL,
        `n_people` INT(11) NOT NULL,
        PRIMARY KEY(`id`))
        ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;";
    $pdo->exec($create_prenotation_table);
    if (!count($pdo->query('SELECT * FROM prenotations')->fetchAll())) {
        $populate_prenotation_table = " INSERT INTO prenotations (client_name, prenotation_date, hour,n_people) VALUES ";
        $prenotation_data = fopen("prenotazioni.csv", "r");
        $index = 0;
        while (!feof($prenotation_data)) {
            $row  = fgetcsv($prenotation_data);
            if ($index !== 0) {
                $row_collection = [];

                for ($i = 0; $i < count($row); $i++) {
                    if ($i < count($row) - 1) {
                        array_push($row_collection, '"' . $row[$i] . '"');
                    } else {
                        array_push($row_collection, $row[$i]);
                    }
                }
                $query_row = "(" . implode(",", $row_collection) . ")";
                if (!feof($prenotation_data) == true) {
                    $query_row .= ",";
                }
                $populate_prenotation_table .= $query_row;
            }
            $index++;
        }
        var_dump($populate_prenotation_table);
        $pdo->exec($populate_prenotation_table);
    }
}

//ADD VALUES INTO DB TABLES WITH EASY QUESRY ////////////////////////////////////////////////////

function reservation()
{
    $get_name = $_POST['client_name'];
    $get_date = $_POST['prenotation'];
    $get_hour = $_POST['hour'];
    $get_number = $_POST['number'] ?? 0;
    $prenotation_into_db = "INSERT INTO prenotations (client_name, prenotation_date, hour,n_people) VALUES ( '$get_name' , '$get_date' , '$get_hour' , $get_number);";
    getPdo()->exec($prenotation_into_db);
};
function vote()
{
    $get_recens_name = $_POST['recens_name'];
    $get_vote = $_POST['vote'];
    $get_comment = $_POST['comment'];
    $comment_into_db = "INSERT INTO votes(client_name, vote, comment) VALUES ( '$get_recens_name' , $get_vote , '$get_comment');";
    getPdo()->exec($comment_into_db);
    
}
//FUNCTION FOR WRITE ON DIFFERERNT FILE CSV /// JSON


function getFile($dataType = "all") {
    // CSV STAMP ON FILE //////////////////////////////////////////////////////////////////////
    
    $pdo = getPdo();
    function getReviews($paramsPdo) {
        $tableToShow = "votes";
        $filename = "export.csv";
        $file = fopen($filename, "w+");
        $queryToExport = "SELECT * FROM `$tableToShow`";
        $toExport = $paramsPdo->query($queryToExport);
        
        $col = $toExport->columnCount();
        $row = $toExport->rowCount();
        
        $gotdata = $toExport->fetchAll();  
        
        // var_dump($gotdata);
        $headerCSV = array('id', 'client_name', 'vote', 'comment');
        fputcsv($file, $headerCSV);
        for($i = 0 ; $i < $row; $i++) {
            $smelted =array();
           for($j = 0; $j < $col; $j++) {
            $smelted[] = $gotdata[$i][$j];
           }
           fputcsv($file, $smelted);
        }   
        fclose($filename);
    }
 
  
    function getOrders($paramsPdo) {
        $tableToShowJson = "prenotations";
        $queryToExportJson = "SELECT * FROM `$tableToShowJson`";
        $toExportJson = $paramsPdo->query($queryToExportJson);
        $jsonArray = $toExportJson->fetchAll(PDO::FETCH_NUM);
        $jsonname = "export.json";
        file_put_contents($jsonname, json_encode($jsonArray));
        
    }
    //JSON STAMP ON FILE //////////////////////////////////////////////////////////////////
    switch($dataType) {
        case "reviews" : {
            getReviews($pdo);
            break;
        } 
        case "order": {
            getOrders($pdo);
            break;
        }
        default: {
            getReviews($pdo);
            getOrders($pdo);
            break;
        }
    }

}  

//SWITCH CASE FORM WITH HIDDEN INPUT VALUE AND CALL OF FUNCTION FOR EACH CASE ///////////////////////////////////////////////////////////////////////////
switch ($_POST["action"]) {
    case "init": {
            init();
            break;
        }
    case "reservation": {
            reservation();
            break;
        }
    case "vote": {
            vote();
            break;
        }
    case "export": {
        getFile($_POST['data_type']);
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
                <select name="data_type" class="form-select" id="data_type" aria-label="Default select example">
                    <option selected>Cosa vuoi salvare</option>
                    <option value="all">Tutto</option>
                    <option value="reviews">Recensioni</option>
                    <option value="order">Ordini</option>
                    
                </select>
                <input type="hidden" name="action"  value="export">
            <button type="submit" class="btn mt-3 btn-primary">CREA CSV</button>
            </form>
          
        
            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="reservation">
                <div class="mb-3 mt-5">
                    <label for="client_name" class="form-label">Nome Cliente</label>
                    <input type="text" class="form-control" id="client_name" name="client_name" aria-describedby="emailHelp" required>
                    <div id="client_name" class="form-text">Grazie per questa informazione</div>
                </div>
                <div class="mb-5">
                    <label for="prenotation" class="form-label">Data Prenotazione</label>
                    <input type="text" class="form-control" name="prenotation" id="prenotation" required>
                </div>
                <div class="mb-5">
                    <label for="hour" class="form-label">Orario</label>
                    <input type="number" name="hour" class="form-control" id="hour" required>
                </div>
                <div class="mb-5">
                    <label for="person_number" class="form-label">Numero Persone</label>
                    <input type="number" name="number" class="form-control" id="person_number" required>
                </div>
                <button type="submit" class="btn mt-3 btn-primary">Submit</button>
            </form>
            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="vote">
                <div class="mb-3 mt-5">
                    <label for="recens_name" class="form-label">Nome Cliente</label>
                    <input type="text" class="form-control" id="recens_name" name="recens_name" aria-describedby="emailHelp" required>
                    <div id="recens_name" class="form-text">Grazie per questa informazione</div>
                </div>
                <div class="mb-5">
                    <label for="vote" class="form-label">Voto</label>
                    <input type="number" name="vote" class="form-control" id="vote" required>
                </div>
                <div class=" form-floating">
                    <textarea class="form-control" placeholder="Leave a comment here" id="vote" name="comment" style="height: 100px"></textarea>
                    <label for="vote">Lascia un commento</label>
                </div>
                <button type="submit" class="btn mt-3 btn-primary">Submit</button>
            </form>
            
        </div>
    </main>
</body>

</html> 