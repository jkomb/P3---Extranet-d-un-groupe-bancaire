<?php

include('functions.php');

session_start();

redirectMainIfConnected();

$page = 'index';

if ( isset($_POST['username']) && isset($_POST['password']) )
{

  $username = htmlspecialchars( $_POST['username'] );
  $password = htmlspecialchars( $_POST['password'] );

  $bdd = connexionBDD();

  $request_infos = $bdd -> prepare('SELECT nom, prenom, password, id_user, avatar, question, reponse FROM accounts WHERE username= :username');
  $request_infos -> execute( array( 'username' => $username ) );
  $infos_user = $request_infos -> fetch();
  $request_infos -> closeCursor();

  if ( empty($infos_user) )
  {
    $_SESSION['index'] = 'not_found';
  }

  else
  {
    $password_check = password_verify( $password, $infos_user['password'] );

    if ( $password_check )
    {
      $_SESSION['nom'] = $infos_user['nom'];
      $_SESSION['prenom'] = $infos_user['prenom'];
      $_SESSION['id_user'] = intval( $infos_user['id_user'] );
      $_SESSION['username'] = $username;
      $_SESSION['hash_password'] = $infos_user['password'];
      $_SESSION['question'] = $infos_user['question'];
      $_SESSION['reponse'] = $infos_user['reponse'];
      $_SESSION['avatar'] = $infos_user['avatar'];

      unset($_SESSION['index']);

      header('Location: main.php');
      exit;
    }

    else
    {
      $_SESSION['index'] = 'not_found';
    }
  }
  header('Location: index.php');
  exit;
}

/*
Affichage de la page
Display of the page
*/

else
{
  include('header.php');
?>

  <div id="titre_connexion">

    <h1>Veuillez vous connecter</h1>
    <br><br>

    <?php if( isset($_SESSION['index']) && $_SESSION['index'] === 'not_found' ):?>
      <h2>Vos identifiants ne sont pas corrects !</h2>
      <br><br
    <?php endif;?>

  </div>

  <div id="page_connexion">

      <div class="champs_connexion">

        <form  method="post" action="index.php">

          <div class="champs_connexion">
            <input type="text" name="username" placeholder="Nom d'utilisateur" autofocus required/>
          </div>

          <div class="champs_connexion">
            <input type="password" name="password" placeholder="Mot de passe" required/>
          </div>

          <div class="champs_connexion">
            <input type="submit" value="Valider"/>
          </div>

        </form>

        <div>
          <a href="creation_compte.php?mdp=oublie" id="pwd_forgotten">J'ai oublié mon mot de passe</a>
        </div>

    </div>

  </div>

<?php
}

unset($_SESSION['index']);

include('footer.php');
