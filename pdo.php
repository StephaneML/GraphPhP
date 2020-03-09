<?php
include ("jpgraph-4.2.11/src/jpgraph.php");
include ("jpgraph-4.2.11/src/jpgraph_bar.php");

$user = 'root';
$password = ''; //To be completed if you have set a password to root
$database = 'servdb'; //To be completed to connect to a database. The database must exist.
$port = '3308'; //Default must be NULL to use default port
$servname = 'localhost:3308';
$mysqli = new mysqli('127.0.0.1', $user, $password, $database, $port);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}


$tableauTime = array();
$tableauConnect = array();

$dbco = new PDO("mysql:host=$servname;dbname=$database", $user, $password);
                $dbco->setAttribute(PDO::PARAM_STR,PDO::ERRMODE_EXCEPTION);
                
                $time1 = '11:13:35.10';
                $time2 = '13:16:37.83';

                $sql = 'SELECT CONECT, TIME FROM serv 
                    WHERE TIME BETWEEN 
                    VALUES (:time1) AND VALUES (:time2)';
  try {
    $stmt = $dbco->prepare($sql, array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
    $stmt->execute(array(
                                    ':time1' => $time1,
                                    ':time2' => $time2
                                ));
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $tableauTime[] = 'Time ' . $row['TIME'];
        $tableauConnect[] = $row['CONECT'];
    }
    $stmt = null;
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }
    
// *******************
// Création du graphique
// *******************


// Construction du conteneur
// Spécification largeur et hauteur
$graph = new Graph(800,500);

// Réprésentation linéaire
$graph->SetScale("textlin");

// Ajouter une ombre au conteneur
$graph->SetShadow();

// Fixer les marges
$graph->img->SetMargin(40,30,25,40);

// Création du graphique histogramme
$bplot = new BarPlot($tableauConnect);

// Une ombre pour chaque barre
$bplot->SetShadow();

// Afficher les valeurs pour chaque barre
$bplot->value->Show();
// Fixer l'aspect de la police
$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,9);

// Ajouter les barres au conteneur
$graph->Add($bplot);

// Le titre
$graph->title->Set("Graphique PULSR TOWER : resultats tests services distant ");
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Titre pour l'axe horizontal(axe x) et vertical (axe y)
$graph->xaxis->title->Set("Time");
$graph->yaxis->title->Set("tests services");

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

// Légende pour l'axe horizontal
$graph->xaxis->SetTickLabels($tableauTime);

// Afficher le graphique
$graph->Stroke();

?>    