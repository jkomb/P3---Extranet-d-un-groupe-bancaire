<?php

include('functions.php');

session_start();
$_SESSION =array();

redirectMainIfConnected();

$page = 'creation';

$bdd = connexionBDD();

$account_state = 'creation';

$_SESSION['creation_compte'] = '';

/*
2.

Lorsque l'utilisateur a oublié son mot de passe, on lui demande de saisir son
nom d'utilisateur.

When the user forgot his password, we ask him to type its user name.
*/
if ( isset($_GET['mdp']) )
{
  $mot_cle = htmlspecialchars($_GET['mdp']);

  if ( $mot_cle === 'oublie' )
  {
    if ( isConnected() === false )
    {
      $account_state = 'mdp_oublie';

    }
    else
    {
      $question = $_SESSION['question'];
      $account_state = 'question_secrete';
    }
  }
}

/*
3.

On demande ensuite à ce qu'il réponde à sa question secrète.

We then ask him to answer his secret question.
*/

elseif ( isset($_POST['username']) && !isset($_POST['nom']) )
{
  $username = htmlspecialchars( $_POST['username'] );

  $request_infos = $bdd -> prepare('SELECT question, id_user FROM accounts WHERE username=:username LIMIT 0,1' );
  $request_infos -> execute( array( 'username' => $username ) );
  $infos_user = $request_infos -> fetch();

  if ( !empty( $infos_user ) )
  {

    $_SESSION['question'] = $infos_user['question'];
    $_SESSION['temp_id_user'] = $infos_user['id_user'];

    $request_infos -> closeCursor();

    $account_state = 'question_secrete';
  }

  else
  {
    $_SESSION['creation_compte'] = 'unknown';
  }
}
/*
4.

On demande enfin à l'utilisateur de saisir un nouveau mot de passe, 2 fois.

Finally, we ask the user to type a new password, twice.
*/
elseif ( isset($_POST['reponse']) && !isset($_POST['nom']) )
{
  $reponse = htmlspecialchars( $_POST['reponse'] );

  $request = $bdd -> prepare('SELECT reponse FROM accounts WHERE id_user=:id_user LIMIT 0,1' );
  $request -> execute( array( 'id_user' => $_SESSION['temp_id_user'] ) );

  $reponse_secrete = $request -> fetch();

  $reponse_check = password_verify( $reponse, $reponse_secrete['reponse'] );

  if ( $reponse_check )
  {
    $account_state = 'bonne_reponse';
  }

  else
  {
    $account_state = 'question_secrete';
    $_SESSION['creation_compte'] = 'wrong_answer';
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
  $password = htmlspecialchars( $_POST['password'] );
  $passwordbis = htmlspecialchars( $_POST['passwordbis'] );

  if ( $password === $passwordbis )
  {
    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $update_password = $bdd -> prepare( 'UPDATE accounts SET password=:password WHERE id_user=:id_user' );


    $update_password -> execute( array( 'password' => $hash_password, 'id_user' => $_SESSION['temp_id_user'] ) );

    $update_password -> closeCursor();
    $account_state = 'modifie';
  }

  else
  {
    $account_state = 'bonne_reponse';
    $_SESSION['creation_compte'] = 'not_modified';
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
elseif ( array_key_exists('nom', $_POST) )
{
  $empty_fields = [];
  $filled_fields = [];
  foreach ($_POST as $key => $value)
  {
    $val = trim($value);
    if ( $val === "" )
    {
      array_push( $empty_fields, $key );
    }
    else
    {
      if ($key != 'password' || $key != 'reponse')
      {
        $_SESSION[$key] = $val;
      }
    }
  }

  if ( !empty( $empty_fields ) )
  {
      $list_empty_fields = implode( ", ", $empty_fields);
      $_SESSION['creation_compte'] = 'vide';
      $account_state = 'creation';
  }

  else
  {
    $user_data['nom'] = mb_strtoupper( htmlspecialchars( $_POST['nom'] ) );
    $user_data['prenom'] = ucfirst( mb_strtolower( htmlspecialchars( $_POST['prenom'] ) ) );
    $user_data['username'] = strtolower( htmlspecialchars( $_POST['username'] ) );
    $user_data['password'] = password_hash( htmlspecialchars( $_POST['password'] ), PASSWORD_DEFAULT);
    $user_data['question'] = htmlspecialchars( $_POST['question'] );
    $user_data['reponse'] = password_hash( htmlspecialchars( $_POST['reponse'] ), PASSWORD_DEFAULT);

    //TRAITEMENT DES CHAÎNES DE CARACTÈRES EN SQL
    /*
    $subscription = $bdd -> prepare('INSERT INTO accounts(nom,prenom,username,password,question,reponse)
                   VALUES(UPPER(:nom), CONCAT(UPPER(LEFT(:prenom, 1)),SUBSTRING(LOWER(:prenom), 2)),
                    :username,:password,:question,:reponse)');
    */

    $subscription = $bdd -> prepare('INSERT INTO accounts(nom,prenom,username,password,question,reponse)
                   VALUES( :nom, :prenom, :username, :password, :question, :reponse )' );

    $subscription -> execute( $user_data );

    $subscription -> closeCursor();

    $account_state = 'cree';
  }
}

/*
1.

Lorsqu'un nouvel utilisateur souhaite créer un compte, on lui demande toutes
les informations requises.

When a new user wants to create a account, we ask him all the needed
informations.
*/
if ($account_state === 'creation')
{
  redirectMainIfConnected();
  include('header.php');
?>

  <div id="titre_connexion">

    <h1>Veuillez renseigner vos données personnelles</h1>
    <br>

  </div>

  <?php if ( $_SESSION['creation_compte'] === 'vide' ): ?>

    <div id="titre_connexion">

      <h2>Un ou plusieurs champs saisis sont vides :</h2>
      <br><br>
      <?php echo $list_empty_fields ?>
      <br><br>
      <p><strong>Merci de compléter le formulaire entièrement.</strong></p>

    </div>

  <?php endif; ?>

  <?php displayMessage($_SESSION['creation_compte']); ?>

  <div id="page_connexion">

    <form  method="post" action="creation_compte.php">

      <div class="champs_connexion">
        <label><strong>Nom</strong></label>
        <input type="text" name="nom" autofocus <?php valueFilled('nom')?> required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Prénom</strong></label>
        <input type="text" name="prenom" <?php valueFilled('prenom')?> required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Nom d'utilisateur</strong></label>
        <input type="text" name="username" <?php valueFilled('username')?> required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Mot de passe</strong></label>
        <input type="password" name="password" required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Question secrète</strong></label>
        <input type="text" name="question" <?php valueFilled('question')?> required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Réponse à la réponse secrète</strong></label>
        <input type="password" name="reponse" required/>
      </div>

      <div class="champs_connexion">
        <input type="submit" value="Valider"/>
      </div>

    </form>

  </div>

<?php
}

if ( $account_state === "mdp_oublie" )
{
  include('header.php');
?>

      <div id="titre_connexion">
        <h1>Afin de réinitialiser votre mot de passe,</h1>
        <br>
      </div>

      <?php displayMessage($_SESSION['creation_compte']); ?>

      <div id="page_connexion">

          <form  method="post" action="creation_compte.php">

            <div class="champs_connexion">
              <label><strong>merci de saisir le nom d'utilisateur défini lors de la création de votre compte</strong></label>
              <br>
              <input type="text" name="username" autofocus required/>
            </div>

            <div class="champs_connexion">
              <input type="submit" value="Valider"/>
            </div>
          </form>

      </div>';

<?php
}

if ( $account_state === 'question_secrete' )
{
  include('header.php');
?>

  <div id="titre_connexion">
    <h1>Veuillez répondre à votre question secrète</h1>
    <br>
  </div>

  <?php displayMessage($_SESSION['creation_compte']); ?>

  <div id="page_connexion">

    <form  method="post" action="creation_compte.php">

      <div class="champs_connexion">
        <label><strong><?php echo $_SESSION['question']; ?></strong></label>
        <br>
        <input type="password" name="reponse"  autofocus required/>
      </div>

      <div class="champs_connexion">
        <input type="submit" value="Valider"/>
      </div>
    </form>

  </div>';

<?php
}

if ( $account_state === 'bonne_reponse' )
{
  include('header.php');
?>

  <div id="titre_connexion">

    <h1>Veuillez saisir votre nouveau mot de passe</h1>
    <br>

  </div>

  <?php displayMessage($_SESSION['creation_compte']); ?>

  <div id="page_connexion">

    <form  method="post" action="creation_compte.php">

      <div class="champs_connexion">
        <label><strong>Nouveau mot de passe</strong></label>
        <br>
        <input type="password" name="password" autofocus required/>
      </div>

      <div class="champs_connexion">
        <label><strong>Veuillez le saisir à nouveau</strong></label>
        <br>
        <input type="password" name="passwordbis" required/>
      </div>

      <div class="champs_connexion">
          <input type="submit" value="Valider"/>
      </div>
    </form>

  </div>;

<?php
}

if ( $account_state === 'modifie' )
{
    header("Refresh:3; url=index.php");
    include('header.php');
?>

    <div id="titre_connexion">

      <h2>Vos modifications ont bien été prises en compte</h2>
      <br><br>
      <p>Vous allez être redirigé vers la page d'accueil</p>

    </div>

<?php
}

if ( $account_state === 'cree' )
{
  header("Refresh:3; url=index.php");
  include('header.php');
?>

    <div id="titre_connexion">

      <h2>Vos informations ont bien été enregistrées.</h2>
      <br><br>
      <p>Vous allez être redirigé vers la page d'accueil</p>

    </div>

<?php
}

$_SESSION = array();

include('footer.php');
