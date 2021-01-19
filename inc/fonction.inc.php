<?php
//fonction de débugage : debug() permet d'effectuer un print "amélioré":
function debug( $arg ){

	echo '<div style="background:#fda500; z-index:1000; padding: 15px;">';

		$trace = debug_backtrace();
		//debug_backtrace() : fonction interne de php qui retourner un array contenant des infos
		echo "Debug demandé dans le fichier : ". $trace[0]['file'] ." à la ligne ". $trace[0]['line'];

		print '<pre>';
			print_r( $arg );
		print '</pre>';

	echo '</div>';
}

//------------------------------------------------
//fonction execute_requete() : 
function execute_requete( $req ){

	global $pdo;

	$pdostatement = $pdo->query( $req );

	return $pdostatement;
}

//------------------------------------------------
//fonction userConnect : si l'internaute est connecté
function userConnect(){
	if( !isset($_SESSION['membre'] )){
		//si la session 'membre" n'existe pas cela signifie que l'on n'est pas connecté et donc on renvoie false
		return false;
	}else {
		//sinon c'est que la session membre existe et donc que l'on est connecté. on retourne true
		return true;
	}
}

//------------------------------------------------
function adminConnect(){//si l'internaute est connecté et qu'il est administrateur 
	if(userConnect() && $_SESSION['membre']['statut'] == 1){
	//SI l'internaute est connecté ET qu'il est admin (dnc qu'il a un statut égal à 1 !)
		return true;
	}else {
		return false;
	}
}

//------------------------------------------------
//fonction pour creer un panier
function creation_panier(){
	if( !isset( $_SESSION['panier'] ) ){//si la session/panier n'existe pas on la cree
		$_SESSION['panier'] = array();

			$_SESSION['panier']['titre'] = array();
			$_SESSION['panier']['id_produit'] = array();
			$_SESSION['panier']['quantite'] = array();
			$_SESSION['panier']['prix'] = array();
	}	
}

//--------------------------------------------------
//fonction d'ajout d'un produit au panier:
function ajout_panier($titre,$id_produit,$quantite,$prix){
	creation_panier();
	//ici on fait appel a la fonction declaré au dessus
	//soit le panier n'existe pas et on le cree
	//soit le panier existe et on l'utilise et on le creera pas

	$index = array_search($id_produit,$_SESSION['panier']['id_produit']);
		//array_search(arg1,arg2)
		//arg1: ce que l'on cherche
		//arg2: dans quel tableau on effectue la recherche
		//VALEUR DE RETOUR : la fonction renverra la 'clé' (correspondante à l'indice du tableau s'il y a une correspondance) ou 'false".

	//echo "index du produit dans le panier: $index";

	if ($index !== false){//si $index est different de false c'est que le produit est deja present dans le panier
		$_SESSION['panier']['quantite'][$index] += $quantite;
		//ici on va precissement  à l'indice du produit deja present dans le panier et on y ajoute la nouvelle quantité
	}else{//sinon c'est que le produit n'est pas dans le panier donc on insert toutes les infos necessaire

			$_SESSION['panier']['titre'][] = $titre;
			$_SESSION['panier']['id_produit'][] = $id_produit;
			$_SESSION['panier']['quantite'][] = $quantite;
			$_SESSION['panier']['prix'][] = $prix;
		}
}

//--------------------------------------------------------
//fonction pour retirer un produit dans le panier

function retirer_produit_panier($id_prod_delete){

	$index = array_search($id_prod_delete,$_SESSION['panier']['id_produit']);

	if($index !== false){//si l'index est different de 'false' c'est que le produit existe dans le panier
		array_splice($_SESSION['panier']['titre'],$index,1);
		array_splice($_SESSION['panier']['id_produit'],$index,1);
		array_splice($_SESSION['panier']['quantite'],$index,1);
		array_splice($_SESSION['panier']['prix'],$index,1);
	//array_splice(arg1,arg2,arg3): permet de supprimer un/des element(s) d'un tableau
	//arg1: le tableau dans lequel on veut faire une suppression
	//arg2: l'element que l'on cherche à supprimer
	//arg3: le nombre d'element que l'on souhaite supprimer(a partir de l'indice arg2)
	}
}

//--------------
function montant_total(){

	$total = 0;
	for( $i = 0 ; $i < sizeof($_SESSION['panier']['id_produit']) ; $i++){
		$total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
}
return $total;
}