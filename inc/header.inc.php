<?php require_once 'init.inc.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Site boutique</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- CDN de BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
 <!-- CDN FONT AWESOME-->
 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

  <!-- CSS PERSO -->
  <link rel="stylesheet" href="">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= URL ?>index1.php">LOGO</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?= URL ?>index1.php">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?= URL ?>panier.php">Panier</a>
        </li>

        <?php if(userConnect()) : 
          //si l'internaute est connecté on affiche les liens 'profil' et 'deconnexion'
          ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>profil.php">Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>connexion.php?action=deconnexion">Deconnexion</a>
        </li>
        <!--la deconnexion sera gerer sur la page connexion.php-->

          <?php else:
          //sinon c'est que l'on n'est pas connecté, on affiche les liens 'connexion' et 'inscription' ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>inscription.php">Inscription</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= URL ?>connexion.php">Connexion</a>
        </li>

        <?php endif; ?>

        <?php if(adminConnect() ) :
        //si l'admin est connecté, on affiche le menu sur le backoffice
         ?>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            BackOffice
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_boutique.php"> Gestion boutique </a></li>
            <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_des_membres.php"> Gestion des membres </a></li>
            <li><a class="dropdown-item" href="<?= URL ?>admin/gestion_des_commandes.php"> Gestion des commandes </a></li>

          <?php endif; ?>

          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

  <div class="container">