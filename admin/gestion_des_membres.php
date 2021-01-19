<?php require_once '../inc/header.inc.php'; ?>
<?php

//Restriction d'accès à la page administrative gestion_membre.php :
if( !adminConnect() ){
	//SI l'admin N'EST PAS connecté, on le redirige vers la page de connexion

	header('location:../connexion.php');
	exit();
}

//--------------------------------------------------SUPPRESSION

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ //S'il y a une 'action' dans l'URL ET QUE cette action est égale à 'suppression'

execute_requete(" DELETE FROM membre WHERE id_membre = '$_GET[id_membre]' ");
}


//----------------------------------------------------
//----------------------------------------AFFICHAGE MEMBRE
if( isset( $_GET )  ){

	$r = execute_requete(" SELECT * FROM membre ");

	$content .= "<h2>Liste des membres</h2>";
	$content .= "<p>Nombre de membre dans la boutique : ". $r->rowCount() ."</p>";

	$content .= "<table class='table table-bordered' cellpadding='5'>";
		$content .= "<tr>";
			for( $i = 0; $i < $r->columnCount(); $i++ ){

				$colonne = $r->getColumnMeta( $i );
				$content .= "<th>$colonne[name]</th>";
			}
			$content .= '<th>Suppression</th>';
			$content .= '<th>Modification</th>';
		$content .= "</tr>";

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			$content .= "<tr>";

				foreach( $ligne as $indice => $valeur ){

						$content .= "<td> $valeur </td>";
					
				}

				$content .= '<td class="text-center">
								<a href="?action=suppression&id_membre='. $ligne['id_membre'].'" 
									onclick="return( confirm(\'En etes vous certain ?\') )" >
									<i class="far fa-trash-alt"></i>
								</a>
                            </td>';
                            
                            $content .= '<td class="text-center">
                            <a href="?action=modification&id_membre='. $ligne['id_membre'].'">
                            <i class="far fa-edit"></i>
                            </a>
                        </td>';

			$content .= "</tr>";
		}
	$content .= "</table>";
}

?>
<h1>Gestion des membres</h1>

<?php echo $error; ?>

<?= $content; ?>

<?php
if( isset( $_GET['action'] ) && $_GET['action'] == 'modification' ) : 

$r = execute_requete(" SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]' ");

$membre = $r->fetch( PDO::FETCH_ASSOC );
    //debug( $membre );

$pseudo = $membre['pseudo'];
$nom = $membre['nom'];
$prenom = $membre['prenom'];
$email = $membre['email'];
$sexe = $membre['sexe'];
$ville = $membre['ville'];
$cp = $membre['cp'];
$adresse = $membre['adresse'];
$statut = $membre['statut'];
if( $_POST ){

    $r = execute_requete(" UPDATE membre SET pseudo = '$_POST[pseudo]',
                    nom = '$_POST[nom]',
                    prenom = '$_POST[prenom]',
                    email = '$_POST[email]',
                    sexe = '$_POST[sexe]',
                    ville ='$_POST[ville]',
                    cp = '$_POST[cp]',
                    adresse = '$_POST[adresse]',
                    statut = '$_POST[statut]'
                       WHERE id_membre = '$_GET[id_membre]'
                    ");

$content .= "<div class='alert alert-success'> Modification validée.
</div>";
header('location:?action=affichage');

}
?>

<form method="post">
	<label for="pseudo">Pseudo</label><br>
	<input type="text" name="pseudo" id="pseudo" class="form-control" value="<?= $pseudo ?>"><br>

	<label for="prenom">Prenom</label><br>
	<input type="text" name="prenom" id="prenom" class="form-control" value="<?= $prenom ?>"><br>

	<label for="nom">Nom</label><br>
	<input type="text" name="nom" id="nom" class="form-control" value="<?= $nom ?>"><br>

	<label for="email">Email</label><br>
	<input type="text" name="email" id="email" class="form-control" value="<?= $email ?>"><br>

	<label>Civilité</label><br>
	<input type="radio" name="sexe" <?php if( $sexe == 'f' ) echo 'checked'; ?> value="f">Femme<br>
	<input type="radio" name="sexe" <?php if( $sexe == 'm' ) echo 'checked'; ?> value="m">Homme<br><br>

	<label for="ville">ville</label><br>
	<input type="text" name="ville" id="ville" class="form-control" value="<?= $ville ?>"><br>

	<label for="cp">Code Postal</label><br>
	<input type="text" name="cp" id="cp" class="form-control" value="<?= $cp ?>"><br>

	<label for="adresse">Adresse</label><br>
	<input type="text" name="adresse" id="adresse" class="form-control" value="<?= $adresse ?>"><br>


    <label for="statut">Statut</label><br>
	<select name="statut" id="statut" class="form-control">
		<option value="0" <?php if( $statut == 0 ){ echo 'selected';} ?> >Membre</option>
		<option value="1" <?php if( $statut == 1 ){ echo 'selected';} ?> >Admin</option>
	</select><br>
	<input type="submit" value="modifier" class="btn btn-secondary">
</form>
<?php endif; ?>


<?php require_once '../inc/footer.inc.php'; ?>