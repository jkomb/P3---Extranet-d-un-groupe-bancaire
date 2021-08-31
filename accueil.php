<?php

  if(isset($_SESSION))
  {
    unset($_SESSION);
  }

  include("header.php");



  if(isset($_POST['user_name'])&&isset($_POST['password']))
  {
    /*

    $_POST['user_name']=htmlspecialchars($_POST['user_name']);
    $_POST['password']=htmlspecialchars($_POST['password']);

    $bdd=new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    $password = $bdd->query("SELECT password FROM account WHERE username='$_POST['user_name']);


    if($reponse['password']==$_POST['password'])
    {
      $nom = $bdd->query("SELECT nom FROM account WHERE username='$_POST['user_name']);
      $prenom = $bdd->query("SELECT prenom FROM account WHERE username='$_POST['user_name']);

      $_SESSION["utilisateur"]=$prenom." ".$nom;

      header("refresh:0;url=main.php");
    }
    else
    {
      <div id="titre_connexion">

        <h1>Vos identifiants ne sont pas corrects</h1>
        <br><br><br>
        <p>Vous allez être redirigé vers la page d\'accueil</p>

      </div>';

      header("refresh:2;url=accueil.php");

      </div>
    }
  */
  }
  else
  {
?>

<div id="titre_connexion">

  <h1>Veuillez vous connecter</h1>
  <br><br><br>

</div>

<div id="page_connexion">

    <div class="champs_connexion">

      <form  method="post" action"accueil.php">

        <div class="champs_connexion">
          <input type=text name=user_name placeholder="Nom d'utilisateur" autofocus required/>
        </div>

        <div class="champs_connexion">
          <input type=password name=password placeholder="Mot de passe" required/>
        </div>

        <div class="champs_connexion">
          <input type=submit value="Valider"/>
        </div>

      </form>

      <div>
        <a href="creation_compte.php?mdp=oublie" id="pwd_forgotten">J'ai oublié mon mot de passe</a>
      </div>

  </div>

</div>

<?php
  }
?>
