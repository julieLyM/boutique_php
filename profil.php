<?php require_once 'inc/header.inc.php'; ?>

<?php

//restriction d'acces à la page profil:

if( !userConnect() ){
    //si l'utilisateur n'est pas connecté

//redirection vers la page connexion.php
header('location:connexion.php');

exit();//exit():permet de terminer la lecture du script courant a cet endroit precis, je quitte la page!
}
///-----------------------

if(adminConnect() ){

    $content .= "<h3 style='color: tomato'>ADMINISTRATEUR</h3>";
}
?>


<?php 
    //debug($_SESSION);
    //ici on recupere le pseudo de la personne connectee et on l'affiche dans la balise <h2>
    $pseudo = $_SESSION['membre']['pseudo'];

    $content .= "<h3>Vos information personnelles</h3>";

    $content .='<p>Votre prénom : ' . $_SESSION['membre']['prenom'] . '</p>'; //Nous sommes obligés de faire de la concatenation lorsqu'on souhaites afficher des valeurs d'un tableau multidimensionnel (mm si on est guilllemets!)
    $content .= '<p>Votre nom : '. $_SESSION['membre']['nom'] .'</p>';
    $content .= '<p>Votre email : '. $_SESSION['membre']['email'] .'</p>';
    
    $content .= '<p>Votre adresse : '. $_SESSION['membre']['adresse'] .' - '. $_SESSION['membre']['cp'] .' à '. $_SESSION['membre']['ville'] .'</p>';
    

///--------------------------------------------------
?>
<h1>PROFIL</h1>

<h2>Bonjour <?= $pseudo ?></h2>

<?= $content; //affichage de la variable $content ?>

<?php require_once 'inc/footer.inc.php'; ?>
