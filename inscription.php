<?php require_once 'inc/header.inc.php'; ?>

<?php 
//restriction de l'acces à la page inscription.php:
if(userConnect()){
	header('location:profil.php'); //redirection vers la page profil
	exit();
}
//-----------------------------
?>

<?php

if( $_POST ){ //S'il y a eu validation du formulaire

	debug( $_POST );

	//Controles sur les saisies de l'internaute : (Il faudrait faire des controles sur TOUS les inputs du formulaire)

	if( strlen( $_POST['pseudo'] ) <= 3 || strlen( $_POST['pseudo'] ) >= 15 ){
		//SI la taille du pseudo posté est inférieur ou égale à 3 OU QUE la taille est supérieure ou égale à 15, alors on affiche un message d'erreur

		$error .= '<div class="alert alert-danger">Erreur taille pseudo</div>';
	}

	//Teste si le pseudo est disponible (car on ne peut pas avoir 2 fois le même pseudo car nous avons indiqué une clé UNIQUE lors de la création de la bdd pour le champ 'pseudo')
	$r = execute_requete(" SELECT pseudo FROM membre WHERE pseudo = '$_POST[pseudo]' ");
	//le $r représente un object(PDOStatement)

	if( $r->rowCount() >= 1 ){ //SI le résultat est supérieur ou égal à 1, c'est que le pseudo est déjà attribué car il aura trouvé une correspondance dans la table 'membre' et renverra donc une ligne de résultat et donc le : $r->rowCount() sera égal à 1 )

		$error .= "<div class='alert alert-danger'>Pseudo indisponible</div>";
	}

	//boucle sur toutes les saisies afin de les passer dans les fonctions addslashes et htmlentities :
	foreach( $_POST as $indice => $valeur ){

		$_POST[$indice] = htmlentities( addslashes( $valeur ) );
	}

	//cryptage du mot de passe :
	$_POST['mdp'] = password_hash( $_POST['mdp'], PASSWORD_DEFAULT );
	//password_hash() : permet de créer une clé de hachage
		//echo $_POST['mdp'];

	//INSERTION :
	if( empty( $error ) ){ //SI la variable '$error' est vide (c'est que l'on a rempli le formulaire correctement), on fait l'insertion

		execute_requete(" INSERT INTO membre(pseudo, mdp, prenom, nom, email, sexe, ville, cp, adresse)  
						VALUES( 
								'$_POST[pseudo]',
								'$_POST[mdp]',
								'$_POST[prenom]',
								'$_POST[nom]',
								'$_POST[email]',
								'$_POST[sexe]',
								'$_POST[ville]',
								'$_POST[cp]',
								'$_POST[adresse]'
							)
					 ");

		$content .= "<div class='alert alert-success'> Inscription validée.
						<a href='".URL."connexion.php'>Cliquez ici pour vous connecter</a>
					</div>";
	}
}

//----------------------------------------------------------------------------------------
?>

<h1>INSCRIPTION</h1>

<?= $error; //affichage des messages d'erreurs ?>
<?= $content; //affichage des messages ?>

<form method="post">
	<label>Pseudo</label><br>
	<input type="text" name="pseudo" class="form-control"><br>

	<label>Mot de passe</label><br>
	<input type="text" name="mdp" class="form-control"><br>

	<label>Prenom</label><br>
	<input type="text" name="prenom" class="form-control"><br>

	<label>Nom</label><br>
	<input type="text" name="nom" class="form-control"><br>

	<label>Email</label><br>
	<input type="text" name="email" class="form-control"><br>

	<label>Civilite</label><br>
	<input type="radio" name="sexe" value="f">Femme<br>
	<input type="radio" name="sexe" value="m">Homme<br><br>

	<label>Adresse</label><br>
	<input type="text" name="adresse" class="form-control"><br>

	<label>Ville</label><br>
	<input type="text" name="ville" class="form-control"><br>

	<label>Code postal</label><br>
	<input type="text" name="cp" class="form-control"><br>

	<input type="submit" value="S'inscrire" class="btn btn-secondary">
</form>

<?php require_once 'inc/footer.inc.php'; ?>