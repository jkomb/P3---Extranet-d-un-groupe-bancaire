<?php

  if(isset($_SESSION))
  {
    unset($_SESSION);
  }

  include('header.php');

  session_start();

  if(isset($_POST['username'])&&isset($_POST['password']))
  {

    $_POST['username']=htmlspecialchars($_POST['username']);
    $_POST['password']=htmlspecialchars($_POST['password']);

    $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
               array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    $password = $bdd->prepare('SELECT password FROM accounts WHERE username=?');
    $password->execute(array($_POST['username']));
    $motdepasse = $password->fetch();

    if($motdepasse['password']==$_POST['password'])
    {
      $name = $bdd->prepare('SELECT nom FROM accounts WHERE username=?');
      $name->execute(array($_POST['username']));
      $nom = $name->fetch();

      $surname = $bdd->prepare('SELECT prenom FROM accounts WHERE username=?');
      $surname->execute(array($_POST['username']));
      $prenom = $surname->fetch();

      $_SESSION['prenom']=$prenom['prenom'];
      $_SESSION['nom']=$nom['nom'];

      $password->closeCursor();
      $name->closeCursor();
      $surname->closeCursor();

      header('refresh:0;url=main.php');
    }
    else
    {
      echo'
      <div id="titre_connexion">

        <h1>Vos identifiants ne sont pas corrects !</h1>
        <br><br><br>
        <h2>Vous allez être redirigé vers la page d\'accueil.</h2>

      </div>';

      header('refresh:3;url=accueil.php');

    }

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
          <input type=text name=username placeholder="Nom d'utilisateur" autofocus required/>
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

  include('footer.php');
  
?>
