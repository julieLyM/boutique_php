<?php require_once 'inc/header.inc.php'; ?>


<?php
//affichage des produits:

//ici recupere les differentes categories de ma table 'produit'
$r = execute_requete(" SELECT DISTINCT(categorie) FROM produit ");//DISTINCT EVITER LES DOUBLONS

$content .= '<div class="row">';

	//affichage des catégories :
	$content .= '<div class="col-3">';
		$content .= '<div class="list-group-item">';

			while( $info = $r->fetch( PDO::FETCH_ASSOC ) ){

				//debug( $info );
				$content .= "<a href='?categorie=$info[categorie]' class='list-group-item' >
								$info[categorie]
							</a>";
			}
		$content .= '</div>';
	$content .= '</div>';


	//affichage des produits correspondants à la catégorie selectionnée :
	$content .= '<div class="col-8 offset-1">';
		$content .= '<div class="row">';

			//debug($_GET);
			if( isset( $_GET['categorie'] ) ){//s'il existe 'categorie" dans l'url c'est que l'on a cliqué sur une catégorie
				$r = execute_requete(" SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ");
				while($produit = $r->fetch(PDO::FETCH_ASSOC)){
					//debug($produit);
					$content .= '<div class="col-4">';
					$content .= '<div class="thumbnail" style="border:1px solid #eee">';

						$content .= "<a href='fiche_produit.php?id_produit=$produit[id_produit]'>";
							$content .= "<img src='$produit[photo]' width='80'>";
							$content .= "<p>$produit[titre]</p>";
							$content .= "<p>$produit[prix]</p>";
						$content .= "</a>";

					$content .= '</div>';
				$content .= '</div>';
				}

			}
		$content .= '</div>';
	$content .= '</div>';

$content .= '</div>';

//-------------------
?>
	<h1>MON SITE BOUTIQUE</h1>
<?= $content; //affichage du contenu ?>
<?php require_once 'inc/footer.inc.php'; ?>	