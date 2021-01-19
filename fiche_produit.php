<?php require_once 'inc/header.inc.php';?>

<?php

if(isset($_GET['id_produit'])){//s'il existe un 'id_produit' dans l'url c'est que l'on a choisi deliberement d'afficher la fiche d'un produit en particulier. donc ici on recupere toutes les infos du produit concerné

    $r = execute_requete( "SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'" );
    

}else{//sinon on le redirige vers la page d'accueil si jamais on essaie de forcer l'acces à cette page
    header('location:index1.php');
    exit();
}
//exploiter les données recuperer
$produit = $r->fetch(PDO::FETCH_ASSOC);
debug($produit);

$content .= "<a href='index1.php'>retour vers l'accueil</a><br>";
$content .= "<a href='index1.php?categorie=$produit[categorie]'>retour vers la catégorie $produit[categorie]</a><hr>";

foreach($produit as $cle =>$valeur){
    if($cle == 'photo'){
        $content .= "<p><img src='$valeur' width='200px'></p>";
    }else{
        if($cle != 'id_produit' && $cle != 'reference' && $cle != 'stock'){//si l'indice est different de l'id produit,reference,stock
            $content .= "
            <p>$valeur</p>";
        }
    }
}

//-------------------------------GESTION DU PANIER
if ( $produit['stock'] > 0 ){//si le stock est superieur à zero
        $content .= "<p>Nombre de produits disponibles : $produit[stock]</p>";
        $content .= "<form method='post' action='panier.php'>";
            
        $content .= "<input type='hidden' name='id_produit' value='$produit[id_produit]'>";
        $content .= "<label>Quantité </label>";
        $content .= "<select name='quantite'>";
            for( $i=1 ; $i <= $produit['stock'] ; $i++){
                $content .= "<option value='$i'> $i </option>";
            }
        
        $content .= "</select><br><br>";
        $content .= "<input type='submit' name='ajout_panier' value='Ajouter au panier' class='btn btn-secondary'>";
    $content .= '</form>';


}else{//sinon on affiche rupture de stock
    $content .= "<p>Rupture de stock</p>";
}
//---------------------------------------------
?>
<h1><?= $produit['titre']?></h1>

<?= $content; ?>
<?php require_once 'inc/footer.inc.php';?>