<?php

session_start();

$_SESSION['page'] = 'creation';

include('header.php');

unset($_SESSION['page']);

/*
2.

Lorsque l'utilisateur a oublié son mot de passe, on lui demande de saisir son
nom d'utilisateur.

When the user forgot his password, we ask him to type its user name.
*/
if ( isset($_GET['mdp']) )
{
  $mot_cle = htmlspecialchars($_GET['mdp']);

  if( $mot_cle === 'oublie' )
  {
?>

    <div id="titre_connexion">

      <h1>Afin de réinitialiser votre mot de passe,</h1>
      <h2>merci de renseigner votre nom d'utilsateur</h2>
      <br>

    </div>

    <div id="page_connexion">

        <form  method="post" action="creation_compte.php">

          <div class="champs_connexion">
            <label>Merci de saisir le nom d'utilisateur défini lors de la création de votre compte</label></br>
            <input type=text name=username autofocus required/>
          </div>

          <div class="champs_connexion">
            <input type=submit value="Valider"/>
          </div>
        </form>

    </div>';

<?php
    }
}

/*
3.

On demande ensuite à ce qu'il réponde à sa question secrète.

We then ask him to answer his secret question.
*/
elseif ( isset($_POST['username']) && !isset($_POST['name']) )
{
  $username = htmlspecialchars( $_POST['username'] );

  $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
           array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

  $request_infos = $bdd -> prepare('SELECT question,id_user FROM accounts WHERE username=?' );
  $request_infos -> execute( array( $username ) );
  $infos_user = $request_infos -> fetch();

  if ( !empty($infos_user) )
  {

    $question = $infos_user['question'];
    $_SESSION['id_user'] = $infos_user['id_user'];

    $request_infos -> closeCursor();

?>

  <div id="titre_connexion">

    <h1>Veuillez répondre à votre question secrète</h1>
    <br>
  </div>

  <div id="page_connexion">

      <form  method="post" action="creation_compte.php">

        <div class="champs_connexion">
          <label><strong><?php echo $question; ?></strong></label></br>
          <input type=password name=reponse  autofocus required/>
        </div>

        <div class="champs_connexion">
            <input type=submit value="Valider"/>
        </div>
      </form>

  </div>';

<?php
  }

  else
  {
?>
<div id="titre_connexion">

  <h1>Utilisateur inconnu</h1></br>
  <h2>Merci de rentrer un nom d'utilisateur existant!</h2>
  <br>

</div>
<?php
  header("refresh:3;url=creation_compte.php?mdp=oublie");
  exit();
  }
}
/*
4.

On demande enfin à l'utilisateur de saisir un nouveau mot de passe, 2 fois.

Finally, we ask the user to type a new password, twice.
*/
elseif ( isset($_POST['reponse']) && !isset($_POST['name']) )
{
  $reponse = htmlspecialchars( $_POST['reponse'] );

  $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
           array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

  $request = $bdd -> prepare('SELECT reponse FROM accounts WHERE id_user=?' );
  $request -> execute( array( $_SESSION['id_user'] ) );

  $reponse_secrete = $request -> fetch();

  $reponse_check = password_verify( $reponse, $reponse_secrete['reponse'] );


  if ( $reponse_check )
  {
?>

  <div id="titre_connexion">

    <h1>Veuillez saisir votre nouveau mot de passe</h1>
    <br>

  </div>

  <div id="page_connexion">

      <form  method="post" action="creation_compte.php">

        <div class="champs_connexion">
          <label><strong>Nouveau mot de passe</strong></label></br>
          <input type=password name=password autofocus required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Veuillez saisir votre nouveau </br> mot de passe à nouveau</strong></label></br>
          <input type=password name=passwordbis required/>
        </div>

        <div class="champs_connexion">
            <input type=submit value="Valider"/>
        </div>
      </form>

  </div>';

<?php
  }

  else
  {
?>
    <div id="titre_connexion">

      <h1>Réponse incorrecte !</h1></br>
      <h2>Merci de vous identifier à nouveau</h2>
      <br>

    </div>
<?php
  header("refresh:10;url=creation_compte.php?mdp=oublie");
  exit();
  }
}

/*
5.

Que les 2 saisies de mot de passe soient identiques (le nouveau mot de passe
est enregistré) ou non (reprise du processus à zéro), l'utilisateur est dans toutes
les cas renvoyé à la page d'accueil.

Wether the two typings are identical (the new password is saved) or not (starting
the whole process from scratch), the user is sent back to the welcome page.
*/
elseif ( isset($_POST['password']) && isset($_POST['passwordbis']) )
{
  $hash_password = password_hash( htmlspecialchars( $_POST['password'] ), PASSWORD_DEFAULT );
  $passwordbis = htmlspecialchars( $_POST['passwordbis'] );

  $password_check = password_verify($passwordbis, $hash_password);

  if ( $password_check )
  {
    $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
             array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    $update_password = $bdd->prepare( 'UPDATE accounts SET password=? WHERE id_user=?' );
    $update_password -> execute( array( $hash_password, $_SESSION['id_user'] ) );

    $update_password ->closeCursor();

?>
    <div id="titre_connexion">

      <h2>Vos modifications ont bien été prises en compte</h2>
      <br><br>
      <p>Vous allez être redirigé vers la page d'accueil</p>

    </div>;
<?php

    header("refresh:2;url=index.php");
    exit();
  }

  else
  {
?>

    <div id="titre_connexion">

      <h2>Vos modifications n'ont PAS été prises en compte!</h2>
      <br><br>
      <p><strong>Les 2 saisies de votre nouveau mot de passe ne sont pas identiques!</strong></p>

    </div>
<?php

    header("refresh:3;url=creation_compte.php?mdp=oublie");
    exit();
  }

}

/*
6.

Lorsque la saisie des informations personnelles du compte a été un succès, on
les enregistre dans la base de données et on redirige l'utilisateur
vers l'accueil.

When the user has successfully typed all his personal informations, we save them
in the database and we redirect him to the welcome page.
*/
elseif ( isset($_POST['nom']) )
{

    $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
             array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    $user_datas = array();

    foreach ( $_POST as $cle => $valeur )
    {
      $user_datas[$cle] = htmlspecialchars($valeur);
    }

    $user_datas['password'] = password_hash($user_datas['password'], PASSWORD_DEFAULT);
    $user_datas['reponse'] = password_hash($user_datas['reponse'], PASSWORD_DEFAULT);

    $subscription = $bdd->prepare('INSERT INTO accounts(nom,prenom,username,password,question,reponse)
                   VALUES(UPPER(:nom), CONCAT(UPPER(LEFT(:prenom, 1)),SUBSTRING(LOWER(:prenom), 2)),
                    :username,:password,:question,:reponse)');
    $subscription->execute($user_datas);

    $subscription->closeCursor();

?>

    <div id="titre_connexion">

      <h2>Vos informations ont bien été enregistrées.</h2>
      <br><br>
      <p>Vous allez être redirigé vers la page d'accueil</p>

    </div>

<?php

    header("refresh:2;url=index.php");
    exit();

}


/*
1.

Lorsqu'un nouvel utilisateur souhaite créer un compte, on lui demande toutes
les informations requises.

When a new user wants to create a account, we ask him all the needed
informations.
*/
else
{
?>

  <div id="titre_connexion">

    <h1>Veuillez renseigner vos données personnelles</h1>
    <br>

  </div>

  <div id="page_connexion">

      <form  method="post" action="creation_compte.php">

        <div class="champs_connexion">
          <label><strong>Nom</strong></label>
          <input type=text name=nom autofocus required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Prénom</strong></label>
          <input type=text name=prenom required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Nom d'utilisateur</strong></label>
          <input type=text name=username required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Mot de passe</strong></label>
          <input type=password name=password required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Question secrète</strong></label>
          <input type=text name=question required/>
        </div>

        <div class="champs_connexion">
          <label><strong>Réponse à la réponse secrète</strong></label>
          <input type=password name=reponse required/>
        </div>

        <div class="champs_connexion">
          <input type=submit value="Valider"/>
        </div>
      </form>

  </div>

<?php
}

include('footer.php');
?>
