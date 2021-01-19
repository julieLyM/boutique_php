<?php
//Création/ouverture de session :
session_start();
//PREMIERE LIGNE DE CODE, se positionne TOUJOURS en haut et en premier avant tout traitement PHP

//------------------------------------------
//connexion à la BDD :
$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array( PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND=>"SET NAMES UTF8"));

//------------------------------------------
//definition d'une constante :
define( 'URL', 'http://localhost/PHP/boutique/');

//------------------------------------------
//definition de variables :
$content = '';
$error = '';

//------------------------------------------
//inclusion du fichier fonction.inc.php
require_once 'fonction.inc.php';
