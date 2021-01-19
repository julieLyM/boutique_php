<?php require_once 'inc/header.inc.php'; ?>
<?php

debug($_POST);

if (isset($_POST['ajout_panier'])) {  //Ici, on vérifie l'existence d'un "submit" dans le fichier 'fiche_produit.php' ('ajout_panier' provient de l'attribut 'name' de l'input submit du form dans fiche_produit.php) Lorsque l'on ajoute un produit au panier !
    //debug( $_POST );

    $r = execute_requete(" SELECT * FROM produit WHERE id_produit = '$_POST[id_produit]' ");
    //Ici, $_POST['id_produit'] provient de l'input type="hidden" dans fiche_produit.php

    $produit = $r->fetch(PDO::FETCH_ASSOC);
    //debug( $produit );

    ajout_panier($produit['titre'], $produit['id_produit'], $_POST['quantite'], $produit['prix']);
    //$_POST['quantite'] : provient du <select> de fiche_produit.php
}

debug($_SESSION);
//----------------------------------------------------------
//----------------------------------------------------------
//EXERCICE : 
	//gérer la validation du panier ! SI on valide le panier
		//insertion dans la table 'commande' (ne pas ce soucier du champ :'etat' dans la table)
		//récupération du numéro de commande : lastInsertId()
		//insertion du detail de la commande dans la table 'details_commande' (for) 
			//modification du stock en conséquence de la commande
		//et a la fin on vide le panier

        if( isset( $_POST['payer'] ) ){ //Si j'ai cliqué sur le bouton "payer" (submit)

            $id_membre_connecte = $_SESSION['membre']['id_membre'];
                //echo $id_membre_connecte;
        
            //insertion de la commande :
            execute_requete(" INSERT INTO commande( id_membre, montant, date ) 
        
                            VALUES( $id_membre_connecte, ". montant_total() .", NOW() )
                         ");
            //Ici, j'utilise la fonction montant_total() pour récupérer le montant de la commande car si on utilise la variable '$total_prix_panier', A CET ENDROIT PRECIS, la variable n'est pas encore déclarée, nous avons donc une erreur "undefined" car elle est déclarée plus bas
        
            //récupération de la dernière id insérée :
            $id_commande = $pdo->lastInsertId();
                //lastInsertId() : permet de récupérer le dernier id généré lors de l'insertion. Cette méthode est utilisable UNIQUEMENT sur un objet PDO
        
            $content .= "<div class='alert alert-success'>Merci pour votre commande, le numéro de la commande est le $id_commande</div>";
        
            //Insertion du détail de la commande :
            for( $i = 0; $i < sizeof( $_SESSION['panier']['id_produit'] ); $i++ ){
        
                execute_requete(" INSERT INTO details_commande( id_commande, id_produit, quantite, prix)
        
                                VALUES( 
                                        $id_commande,
                                        '". $_SESSION['panier']['id_produit'][$i] ."',
                                        '". $_SESSION['panier']['quantite'][$i] ."',
                                        '". $_SESSION['panier']['prix'][$i] ."'
                                 )
                             ");

                             //modification en stock en consequence de la commande:
                                execute_requete("UPDATE produit SET 
                                    stock = stock - ". $_SESSION['panier']['quantite'][$i] ."
                                    WHERE id_produit = ". $_SESSION['panier']['id_produit'][$i] ."
                                    ");
            }
            unset( $_SESSION['panier'] );//vider le panier apres la commande

        
        }
        
//----------------------------------------------------------
//Action de vider le panier : (Ici, cette portion de code est AVANT l'affichage car on supprime un produit session/panier avant de vouloir l'afficher)
if( isset( $_GET['action'] ) && $_GET['action'] == 'vider' ){

    unset( $_SESSION['panier'] );
    //unset() : permet de détruire une variable, ici $_SESSION['panier'] (donc de vider le panier)
}
        
//----------------------------------------------------------RETIRER UN PRODUIT
//action de retirer un produit du panier : ici cette portion de code est avant l'affichage car on supprimer un produit session/panier avant de vouloir l'afficher
//debug( $_GET );

if (isset($_GET['action']) && $_GET['action'] == 'retirer') {
    retirer_produit_panier($_GET['id_produit']);
}


//----------------------------------------------------------

//affichage du panier :
$content .= '<table class="table">';
$content .= '<tr>
					<th>Titre</th>
					<th>Quantite</th>
                    <th>prix</th>
                    <th>Supprimer</th>
				</tr>';

if (empty($_SESSION['panier']['id_produit'])) { //SI ma session/panier/id_produit est vide, c'est que n'ai rien dans mon panier

    $content .= "<tr>
						<td colspan='4'> Votre panier est vide </td>
					</tr>";
} else { //SINON, c'est qu'il y a des produits dans le panier donc on les affiches :

    //EXERCICE : affichage des infos des produits du panier (pour chaque produit les infos seront dans une ligne '<tr>')
    //bonus : affichez le prix total
    $total_prix_panier = 0; //variable pour le montant total du panier initialisé à zero

    for ($i = 0; $i < sizeof($_SESSION['panier']['id_produit']); $i++) {
        $content .= '<tr>';

        $content .= '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
        $content .= '<td>' . $_SESSION['panier']['quantite'][$i] . '</td>';

        //prix total selon la quantite : (multiplication entre le prix et la quantite)
        $prix_par_achat = $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];

        $total_prix_panier += $prix_par_achat; //Ici, j'ajoute (à chaque tour de boucle) le prix total d'un produit (en fonction de sa quantité) au montant total

        $content .= '<td>' . $prix_par_achat . ' €</td>';

        $content .= '<td>';

        $content .= '<a href="?action=retirer&id_produit=' . $_SESSION['panier']['id_produit'][$i] . '">';
        $content .= '<i class="far fa-trash-alt"></i>';
        $content .= '</a>';

        $content .= '</td>';


        $content .= '</tr>';
    }

    $content .= "<tr>
                            <th colspan='2'>&nbsp;</th>
                            <th colspan='2'>" . montant_total() . "€</th>

                        </tr>"; 
                        //<th colspan='2'> $total_prix_panier €</th>";	
                        //ici on affiche le montant total du panier dans le fichier fonction.php

    if (userConnect()) { //Si l'utilisateur est connecté
        $content .= '<form method="post">';
        $content .= '<tr>';

        $content .= '<td>';

        $content .= '<input type="submit" value="Payer" name="payer" class="btn btn-secondary">';

        $content .= '</td>';

        $content .= '</tr>';
        $content .= '</form>';
    } else { //SINON, c'est que l'on n'est pas connecté

        $content .= '<tr><td colspan="4">';
        $content .= 'Veuillez vous <a href="connexion.php">connecter</a> ou vous <a href="inscription.php">inscrire</a>.';
        $content .= '</td></tr>';
    }

    //vider le panier:
    $content .= ' <tr><td colspan="4">';
    $content .= '<a href="?action=vider">vider mon panier</a>';
    $content .= '</tr></td>';
}

$content .= '</table>';

//--------------------------------------------------------------------------------------
?>
<h1>PANIER</h1>

<?= $content; //affichage du contenu 
?>

<?php require_once 'inc/footer.inc.php'; ?>