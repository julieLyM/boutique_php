<?php require_once "../inc/header.inc.php"; ?>
<?php

//Restriction d'accès à la page administrative gestion_boutique.php :
if( !adminConnect() ){
	//SI l'admin N'EST PAS connecté, on le redirige vers la page de connexion

	header('location:../connexion.php');
	exit();
}

//-------------------------------------------------------------------------
//Gestion de la SUPPRESSION :
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == 'suppression' ){ //S'il y a une 'action' dans l'URL ET QUE cette action est égale à 'suppression'

	//Récupération de la colonne 'photo' dans la table 'produit' A CONDITION que l'id corresponde à l'id ppssée dans l'URL
	$r = execute_requete(" SELECT photo FROM produit WHERE id_produit = '$_GET[id_produit]' ");

	$photo_a_supprimer = $r->fetch( PDO::FETCH_ASSOC );
		//debug( $photo_a_supprimer );

	$chemin_photo_a_supprimer = str_replace( 'http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo_a_supprimer['photo'] );

		//debug( $chemin_photo_a_supprimer );

		//str_replace( arg1, arg2, arg3 ) :fonction interne de php qui permet de remplacer une chaine de caractères
			//arg1 : la chaine que l'on souhaite remplacer
			//arg2 : la chaine de remplacement
			//arg3 : Sur quelle chaine je veux effectuer les changements

		/*Ici, je remplace 	: http//localhost
					par 	: $_SERVER['DOCUMENT_ROOT'] ( = C:/xampp/htdocs )
					dans 	: $photo_a_supprimer['photo'] ( = http://localhost/PHP/boutique/photo/nom_photo.png )
		*/

		if( !empty( $chemin_photo_a_supprimer ) && file_exists( $chemin_photo_a_supprimer ) ){ //SI le chemin est de la photo n'est pas vide ET que le fichier existe !

			unlink( $chemin_photo_a_supprimer );
			//unlink( url ) : permet de supprimer un fichier
		}

	//Suppression dans la table 'produit' A CONDITION que l'id correponde à l'id que l'on récupère dans l'URL
	execute_requete(" DELETE FROM produit WHERE id_produit = '$_GET[id_produit]' ");
}

//-------------------------------------------------------------------------
//Gestion des produits : INSERTION
if( !empty( $_POST ) ){ //SI le formulaire a été validé ET qu'il n'est pas vide

	//debug( $_POST );

	//Il faudrait penser à faire des controles pour chaque champs !

	foreach( $_POST as $key => $value ){ //Ici, je passe toutes les informations postées dans les fonctions addslashes() et htmlentities()

		$_POST[$key] = htmlentities( addslashes( $value ) );
	}

	//----------------------------------------------
	//Gestion de la photo :
	//debug( $_FILES );
    //debug( $_SERVER );
    
///------------modification de photo
    if(isset($_GET['action']) && $_GET['action'] == 'modification'){ 
        //si je suis dans le cadre d'une modification je recupere le chemin en bdd (grace à l'input ="hidden") que je stocke dans la variable $photo_bdd
        //cette condition doit imperativement se situer avant la gestion de la photo, car si on souhaite uploader une nouvelle photo, la valeur de la variable serait ecrasé par la valeur actuelle
        $photo_bdd = $_POST['photo_actuelle'];

    }

    //------------------------------------------
	if( !empty( $_FILES['photo']['name'] ) ){ //SI le nom de la photo (dans $_FILES) N'EST PAS vide, c'est que l'on a téléchargé un fichier.

		//Ici, je renomme la photo :
		$nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];
			//debug( $nom_photo );

		//Chemin pour accéder à la photo ( à insérer en BDD ):
		$photo_bdd = URL . "photo/$nom_photo";
		//La constante URL <=> http://localhost/PHP/boutique
			//debug( $photo_bdd );

		//Ou est-ce que l'on souhaite enregistrer le fichier 'physique' de la photo
		$photo_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/boutique/photo/$nom_photo";
		//$_SERVER['DOCUMENT_ROOT'] <=> C:/xampp/htdocs
			//debug( $photo_dossier );

		//Enregistrement de la photo au bon endroit, ici dans le dossier photo de notre server
		copy( $_FILES['photo']['tmp_name'], $photo_dossier );
		//copy( arg1, arg2 );
			//arg1 : chemin du fichier source
			//arg2 : chemin de destination
	}
	else{

		$error .= "<div class='alert alert-danger'>Pas de fichier uploade !</div>";
	}

    //----------------------------------------------
    if( isset( $_GET['action'] ) && $_GET['action'] == 'modification' ){//s'il y a une action dans l'url et qu'elle est egale à modification alors on effectue une requete de modification dans la bdd

        execute_requete(" UPDATE produit SET reference = '$_POST[reference]',
                                            categorie = '$_POST[categorie]',
                                            titre = '$_POST[titre]',
                                            description = '$_POST[description]',
                                            couleur = '$_POST[couleur]',
                                            taille = '$_POST[taille]',
                                            sexe = '$_POST[sexe]',
                                            photo ='$photo_bdd',
                                            prix = '$_POST[prix]',
                                            stock = '$_POST[stock]'
                           WHERE id_produit = '$_GET[id_produit]'
                        ");
                        //redirection vers l'affichage
                        header('location:?action=affichage');
    }
	else if( empty($error) ){ //Si la variable $error est vide, je fais mon insertion		

		execute_requete(" INSERT INTO produit( reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock ) VALUES (
													'$_POST[reference]',
													'$_POST[categorie]',
													'$_POST[titre]',
													'$_POST[description]',
													'$_POST[couleur]',
													'$_POST[taille]',
													'$_POST[sexe]',
													'$photo_bdd',
													'$_POST[prix]',
													'$_POST[stock]'
												)
                ");
                              //redirection vers l'affichage
                              header('location:?action=affichage');
	}
}

//-------------------------------------------------------------------------
//Affichage des produits :
//debug( $_GET );

if( isset( $_GET['action'] ) && $_GET['action'] == 'affichage' ){
	//S'il existe une 'action' dans mon URL ET QUE cette 'action' est égale à "affichage", alors on affiche la liste des produits :

	//je récupère les produits en bdd :
	$r = execute_requete(" SELECT * FROM produit ");

	$content .= "<h2>Liste des produits</h2>";
	$content .= "<p>Nombre de produits dans la boutique : ". $r->rowCount() ."</p>";

	$content .= "<table class='table table-bordered' cellpadding='5'>";
		$content .= "<tr>";
			for( $i = 0; $i < $r->columnCount(); $i++ ){

				$colonne = $r->getColumnMeta( $i );
					//debug( $colonne );
				$content .= "<th>$colonne[name]</th>";
			}
			$content .= '<th>Suppression</th>';
			$content .= '<th>Modification</th>';
		$content .= "</tr>";

		while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
			//debug( $ligne );
			$content .= "<tr>";

				foreach( $ligne as $indice => $valeur ){

					if( $indice == 'photo'){ //SI l'indice du tableau '$ligne' est égal à 'photo', on affiche une cellule avec une balise <img>

						$content .= "<td>
										<img src='$valeur' width='80'>
									</td>";
					}
					else{ //SINON, on affiche la valeur dans une cellule simple

						$content .= "<td> $valeur </td>";
					}
				}

				$content .= '<td class="text-center">
								<a href="?action=suppression&id_produit='. $ligne['id_produit'].'" 
									onclick="return( confirm(\'En etes vous certain ?\') )" >
									<i class="far fa-trash-alt"></i>
								</a>
                            </td>';
                            
                            $content .= '<td class="text-center">
                            <a href="?action=modification&id_produit='. $ligne['id_produit'].'">
                            <i class="far fa-edit"></i>
                            </a>
                        </td>';

			$content .= "</tr>";
		}
	$content .= "</table>";
}

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
?>
<h1>GESTION BOUTIQUE</h1>

<!-- 2 liens pour gérer soit l'affichage des produits soit le formulaire d'ajout selon l'action passée dans l'URL -->
<a href="?action=ajout">Ajouter un produit</a><br>
<a href="?action=affichage">Affichage des produits</a><hr>

<?php echo $error; //affichage de la variable $error ?>
<?= $content; //affichage du contenu ?>

<?php if( isset( $_GET['action'] ) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification') ) : 

///---------------------------------------MODIFICATION SUR LE FORMULAIRE
    //SI il existe 'action' dans l'URL ET QUE cette action est égale à 'ajout' ou 'modification', alors on affiche le formulaire
    
    if( isset( $_GET['id_produit'] ) ){ // s'il y a modification du produit s'il "id_produit" existe dans l'url c'est que je suis dans le cadre d'une modification

        //recuperation des informations à afficher pour pré remplir le formulaire
        $r=execute_requete("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'");

        //exploitation les données :
        $article_actuel = $r->fetch(PDO::FETCH_ASSOC);
        //debug($article_actuel);
    }


        //condition pour verifier l'existance des variables:
        if(isset( $article_actuel['reference']) ){
            $reference = $article_actuel['reference'];// on stocke dans une variable la valeur recuperer en base de donnée
        }else{//sinon on cree une variable vide
            $reference = '';
        }

        //version ternaire
        $categorie = ( isset( $article_actuel['categorie'] ) ) ? $article_actuel['categorie'] : '';
        $titre = (isset($article_actuel['titre'] ))?  $article_actuel['titre'] :  '';
        $description =(isset($article_actuel['description'] ))?  $article_actuel['description'] :  '';
        $couleur = (isset($article_actuel['couleur'])) ?  $article_actuel['couleur'] :  '';
        $prix = (isset($article_actuel['prix'] ))?  $article_actuel['prix'] :  '';
        $stock = (isset($article_actuel['stock'] ))?  $article_actuel['stock'] :  '';

        $taille_s = ( isset($article_actuel['taille'] ) && $article_actuel['taille'] == 'S') ? $taille_s = 'selected' : "" ;
        $taille_m = ( isset($article_actuel['taille'] ) && $article_actuel['taille'] == 'M') ? $taille_m = 'selected' : "" ;
        $taille_l = ( isset($article_actuel['taille'] ) && $article_actuel['taille'] == 'L') ? $taille_s = 'selected' : "" ;
        $taille_xl = ( isset($article_actuel['taille'] ) && $article_actuel['taille'] == 'XL') ? $taille_s = 'selected' : "" ;
        //civilite :
        $sexe_f = ( isset( $article_actuel['sexe']) && $article_actuel['sexe'] == 'f' ) ? 'checked' : '';
        $sexe_m = ( isset( $article_actuel['sexe']) && $article_actuel['sexe'] == 'm' ) ? 'checked' : '';



//--------------------------------------------------------FIN MODIFICATION
    ?>


<form method="post" enctype="multipart/form-data">
	<!--  enctype="multipart/form-data" : cet attribut est OBLIGATOIRE lorsque l'on souhaite uploader des fichiers et les récupérer via $_FILES -->
	<label>Référence</label><br>
	<input 
        type="text" 
        name="reference" 
        class="form-control" 
        value="<?= $reference ?>"
    >
    <br>

	<label>Catégorie</label><br>
	<input 
        type="text" 
        name="categorie" 
        class="form-control"
        value="<?= $categorie ?>"
    ><br>

	<label>Titre</label><br>
	<input 
        type="text"
        name="titre" 
        class="form-control" 
        value="<?= $titre ?>"
    ><br>

	<label>Description</label><br>
	<input 
        type="text" 
        name="description" 
        class="form-control"
        value="<?= $description ?>"
    ><br>

	<label>Couleur</label><br>
	<input 
        type="text" 
        name="couleur" 
        class="form-control"
        value="<?= $couleur ?>"
    ><br>

	<label>Taille</label><br>
	<select name="taille" class="form-control">
		<option value="S" <?= $taille_s ?> > S </option>
		<option value="M" <?= $taille_m ?>> M </option>
		<option value="L" <?= $taille_l ?>> L </option>
		<option value="XL" <?= $taille_xl ?>> XL </option>
	</select><br>

	<label>Civilite</label><br>
	<input 
        type="radio" 
        name="sexe" 
        value="m" 
        <?= $sexe_m ?>> Homme <br>
	<input 
        type="radio"
        name="sexe"
        value="f"
        <?= $sexe_f ?>> Femme <br><br>

	<label>Photo</label><br>
	<input 
        type="file" 
        name="photo"
    ><br><br>
    <?php 
        if( isset( $article_actuel['photo'] )){//s'il existe $article_actuel['photo'], c'est que je suis dans le cadre d'une modification
            echo "<i>Vous pouvez uploader une nouvelle photo</i><br><br>";

            echo "<img src='$article_actuel[photo]' width='80'><br><br>";

            echo "<input 
                    type='hidden' 
                    name='photo_actuelle'
                    value='$article_actuel[photo]'
                >";
        }
    ?>
 
	<label>Prix</label><br>
	<input 
        type="text" 
        name="prix" 
        class="form-control"
        value="<?= $prix ?>"
    ><br>

	<label>Stock</label><br>
	<input 
        type="text" 
        name="stock" 
        class="form-control"
        value="<?= $stock ?>"
    ><br>

	<input 
        type="submit" 
        value="<?php echo ucfirst($_GET['action'])//mot dynamique soit ajout ou modification sur la gestion des produits?>" 
        class="btn btn-secondary"
    >
</form>

<?php endif; ?>

<?php require_once "../inc/footer.inc.php"; ?>