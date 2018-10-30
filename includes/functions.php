<?php

function connexion_db()
{
	global $bdd;
		try//connexion à la bdd
	{
		$bdd = $mysql_hostname = "localhost";
$mysql_user = "root";
$mysql_password = "";
$mysql_database = "rss";

$bdd = new PDO("mysql:dbname=$mysql_database;host=$mysql_hostname;port=3306", $mysql_user, $mysql_password, array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));	}
	catch(Exception $e)
	{
		die("bdd non trouvée");
	}
}
function ParseXml ($bdd,$idFlux)
{
	$reqFlux=$bdd->query("select urlFlux,lastUpdateFlux from flux where idFlux=".$idFlux);
	$Flux=$reqFlux->fetch();
	$urlxml=$Flux['urlFlux'];
$xmlDoc = new DOMDocument();

$opts = array(
    'http' => array(
        'user_agent' => 'PHP libxml agent',
    )
);
$context = stream_context_create($opts);
libxml_set_streams_context($context);
$xmlDoc->load($urlxml);



$xmlObject = $xmlDoc->getElementsByTagName('item');
$itemCount = $xmlObject->length;

for ($i=0; $i < $itemCount; $i++){
    $title = $xmlObject->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
    $link = $xmlObject->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
    $description = $xmlObject->item($i)->getElementsByTagName('description')->item(0)->nodeValue;
    $guid = $xmlObject->item($i)->getElementsByTagName('guid')->item(0)->childNodes->item(0)->nodeValue;
	$d = $xmlObject->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
	date_default_timezone_set('Europe/Paris');
	$pubDate=date("Y-m-d h:m:s", strtotime($d) );
	//$image = $xmlObject->item($i)->getElementsByTagName("media")->item(0); 
	//$creator=$xmlObject->item($i)->getElementsByTagName('author')->item(0)->childNodes->item(0)->nodeValue;
	
	
?>
 <div class="post-preview">
            <a href="post.html">
              <h2 class="post-title"><?=$title?></h2>
              <h3 class="post-subtitle"><?=$description?></h3>
            </a>
            <p class="post-meta">Publié par: 
              <a href="#"><?php //$creator ?> ,</a>
              le <?=$d?></p>
          </div>
		  <div class="clearfix">
            <a class="btn btn-primary float-right" href="<?=$link?>">Consulter l'article &rarr;</a>
          </div>
          <hr>
<?php
    $sql = $bdd->prepare("INSERT INTO article(idFlux,titreArticle, urlArticle, descriptionArticle, datePublicationArticle, guidArticle) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->execute(array(
		$idFlux,
        $title,
        $link,
        $description,
		$pubDate,
        $guid
    ));
	var_dump($sql);
}
}
?>
