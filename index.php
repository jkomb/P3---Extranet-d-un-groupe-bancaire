<?php

session_start();

$_SESSION['page'] = 'index';

include('Admin/admin.php');

if ( isset($_POST['username']) && isset($_POST['password']) )
{

  $username = htmlspecialchars( $_POST['username'] );
  $password = htmlspecialchars( $_POST['password'] );

  $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', $login, $pwd,
             array( PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION ));

  $request_infos = $bdd -> prepare('SELECT nom, prenom, password,id_user FROM accounts WHERE username=?');
  $request_infos -> execute( array( $username ) );
  $infos_user = $request_infos -> fetch();

  $password_check = password_verify( $password, $infos_user['password'] );

  if ( empty($infos_user) )
  {
    include('header.php');
    unset( $_SESSION['page'] );
  ?>

    <div id="titre_connexion">

      <h1>Vos identifiants ne sont pas corrects !</h1>
      <br><br><br>
      <h2>Vous allez être redirigé vers la page d'accueil.</h2>

    </div>

  <?php
  header('Refresh:4; url=index.php');
  exit();
  }

  elseif ( $password_check )
  {
    unset( $_SESSION['page'] );
    include('header.php');

    $_SESSION['prenom'] = $infos_user['prenom'];
    $_SESSION['nom'] = $infos_user['nom'];
    $_SESSION['id_user'] = $infos_user['id_user'];

    $request_infos -> closeCursor();

    header('Location: main.php');
    exit();
  }

}

elseif( isset($_GET['log']) )
{
  $deconnexion = htmlspecialchars($_GET['log']);

  if ( $deconnexion === 'off' )
  {
    $_SESSION = array();
    $_POST = array();
    $_GET = array();

    session_destroy();

    header('Location: index.php');
    exit();
  }

}

else
{

  if( isset($_SESSION['nom']) )
  {
      unset( $_SESSION['page'] );
      include('header.php');
  ?>

    <div id="titre_connexion">

      <h1>Page d'accueil</h1></br>
      <h3>Vous êtes déjà connecté, vous allez être redirigé vers la page principale.</h3>

    </div>

<?php
    header('Refresh:4; url=main.php');
    exit();
  }

  else
  {
    include('header.php');
?>
    <div id="titre_connexion">

      <h1>Veuillez vous connecter</h1>
      <br><br><br>

    </div>

    <div id="page_connexion">

        <div class="champs_connexion">

          <form  method="post" action"index.php">

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

}

include('footer.php');

?>
