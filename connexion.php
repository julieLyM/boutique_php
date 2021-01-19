<?php require_once 'inc/header.inc.php'; ?>
<?php

//deconnexion : (avant la redirection sinon le script de la deconnexion ne sera pas lu par l'interpretaeur php)
debug($_GET);
if( isset( $_GET['action'] ) && ( $_GET['action'] == "deconnexion" ) ){
    session_destroy();//detruit le fichier de session
}

//-----------------------------

//restriction de l'acces à la page connexion.php si on est connecté:
if(userConnect()){
	header('location:profil.php'); //redirection vers la page profil
	exit();
}

//-----------------------------

if($_POST){ //s'il y a validation du formulaire
    //debug($_POST);
//comparaison du pseudo et celui en bdd
//variable $r c'est commme le pdostatement
$r = execute_requete("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]' ");
//execute_requete(): fonction qui revient à faire $pdo->query() et donc qui me retournera un objet "$pdostatement", ici c'est la variable "$r"!!!

if($r->rowCount() >=1){ //si il y a une correspondance la table 'membre', $r renverra 1 ligne de resultat et c'est donc que le pseudo existe!


    //je recupere les donnes et les exploiter
    $membre = $r->fetch(PDO::FETCH_ASSOC);
   // debug($membre);

    //verification du mdp:
    if( password_verify( $_POST['mdp'],$membre['mdp']) ){
        //password_verify(arg1,arg2): permet de comparer une chaine de caractere à une chaine cryptee
            //arg1: le mot de passe(ici,posté par l'internaute)
            //arg2: la chaine cryptee (par la fonction password_hash(), ici, le mdp de bdd)

            echo "<br><div class='alert alert-success'>Pseudo ok</div>";

            //ici on va renseigner les informations concernant la personne connecté dans le fichier de session
            foreach($membre as $index => $value){
                $_SESSION['membre'][$index] = $value;
            }
            //debug($_SESSION);

            /*  FACON LONGUE MM CHOSE QUE la boucle foreach

            $_SESSION['membre']['id_membre'] = $membre['id_membre'];
            $_SESSION['membre']['pseudo'] = $membre['pseudo'];
            $_SESSION['membre']['mdp'] = $membre['mdp'];
            $_SESSION['membre']['nom'] = $membre['nom'];
            $_SESSION['membre']['prenom'] = $membre['prenom'];
            $_SESSION['membre']['email'] = $membre['email'];
            $_SESSION['membre']['sexe'] = $membre['sexe'];
            $_SESSION['membre']['ville'] = $membre['ville'];
            $_SESSION['membre']['cp'] = $membre['cp'];
            $_SESSION['membre']['adresse'] = $membre['adresse'];
            $_SESSION['membre']['statut'] = $membre['statut'];
 */

            //redirection vers la page profil
            header('location:profil.php');
    }else{ //sinon c'est que le mdp ne correspond pas
        $error .= "<div class='alert alert-danger'>Erreur mdp</div>";
    }
}else { //sinon c'est que le pseudo n'existe pas
    $error .= "<div class='alert alert-danger'>Erreur pseudo</div>";
    }
}

//---------------------------------------------
?>
<h1>CONNEXION</h1>

<?php echo $error; //affichage de la variable error ?>

<form method='post'>
    <label>Pseudo</label><br>
    <input type="text" name="pseudo" class="form-control" placeholder="Votre pseudo"><br><br>

    <label>Mot de passe</label><br>
    <input type="text" name="mdp"class="form-control" placeholder="Votre mot de passe"><br><br>

    <input type="submit" value="Connexion" class="btn btn-secondary">
</form>


<?php require_once 'inc/footer.inc.php'; ?>