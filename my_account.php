<?php

include('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'modification';

$bdd = connexionBDD();

$my_account = "";

if ( empty($_POST) )
{
  $my_account = 'infos';

  if ( !empty($_FILES['image']) )
  {
    uploadAvatar($bdd, $_FILES['image']);
  }
}

elseif (isset($_POST['delete_avatar']) && !empty($_POST['delete_avatar']))
{
  if ( array_key_exists('avatar', $_SESSION) )
  {
    deleteAvatar($bdd);
  }

  header('Location: my_account.php');
  exit;
}

else
{
  foreach ( $_POST as $key => $value )
  {
    $user_entries[$key] = htmlspecialchars( $value );
  }

  if ( isset($_POST['password']) )
  {
    $password_check = password_verify( $user_entries['password'], $_SESSION['hash_password'] );
    if ( $password_check )
    {
      if ( $user_entries['new_password'] === $user_entries['new_passwordbis'] && !empty(trim($user_entries['new_password'])) )
      {
        $user_entries['password'] = password_hash($user_entries['new_password'], PASSWORD_DEFAULT);
      }
      unset($user_entries['new_password']);
      unset($user_entries['new_passwordbis']);
    }
  }

  $sql_request = 'UPDATE accounts SET ';
  $keys = [];

  $user_data = array_filter($user_entries);

  foreach ( $user_data as $key => $value )
  {
    array_push( $keys, sprintf( '%s= :%s', $key, $key ) );
  }

  $user_data['id_user'] = $_SESSION['id_user'];

  if ( array_key_exists( 'reponse', $user_data ) )
  {
    $user_data['reponse'] = password_hash( $user_data['reponse'], PASSWORD_DEFAULT );
  }

  if ( array_key_exists( 'nom', $user_data ) )
  {
    $_SESSION['nom'] = strtoupper( htmlspecialchars( $user_data['nom'] ) );
  }

  if ( array_key_exists( 'prenom', $user_data ) )
  {
    $_SESSION['prenom'] = ucfirst( strtolower( htmlspecialchars( $user_data['prenom'] ) ) );
  }

  $sql_request .= implode( ",", $keys);
  $sql_request .= ' WHERE id_user= :id_user';

  $modification = $bdd -> prepare( $sql_request );
  $modification -> execute( $user_data );

  $modification -> closeCursor();
  $user_entries = array();
  $user_data = array();

  $my_account = 'modifie';
}


/*
Affichage de la page
Display of the page
*/


if ( $my_account  === 'infos' )
{
  include('header.php');
?>


  <div id="titre_connexion">
    <h1>Vos informations personnelles</h1>
    <br>
    <h3>Si vous le souhaitez, vous pouvez modifier vos informations personnelles ci-dessous :</h3>
  </div>

  <div id="page_connexion">
    <div>
      <form  method="post" action="my_account.php" class="champs_connexion">

        <div class="champs_connexion">
            <label><strong>Nom</strong></label>
            <input type="text" name="nom"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Prénom</strong></label>
          <input type="text" name="prenom"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Nom d'utilisateur</strong></label>
          <input type="text" name="username"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Question secrète</strong></label>
          <input type="text" name="question"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Réponse à la réponse secrète</strong></label>
          <input type="password" name="reponse" />
        </div>

        <div class="champs_connexion">
          <input type="submit" value="Valider"/>
        </div>

      </form>
      <br><br>
      <form action="my_account.php" method="post" enctype="multipart/form-data">

        <div class="champs_connexion">
            <label><strong>Insérer votre avatar </strong>(taille max. : 8 mo)</label>
            <br>
            <input type="file" name="image"/>
            <br>
            <input type="submit" value="Importer" />
        </div>
        <div class="champs_connexion">
            <label><strong>Supprimer votre avatar</strong></label>
            <br>
            <input type="submit" name="delete_avatar" value="Supprimer" />
        </div>

      </form>
      <br><br>
      <form  method="post" action="my_account.php">

        <label><strong>Modification du mot de passe</strong></label>
        <br><br>
        <div class="champs_connexion">
          <label>Mot de passe actuel</label>
          <input type="password" name="password" required/>
        </div>

        <div class="champs_connexion">
          <label>Nouveau mot de passe</label>
          <input type="password" name="new_password" required/>
        </div>

        <div class="champs_connexion">
          <label>Veuillez saisir votre nouveau mot de passe à nouveau</label>
          <input type="password" name="new_passwordbis" required/>
        </div>

        <div class="champs_connexion">
            <input type="submit" value="Valider"/>
        </div>

      </form>

    </div>
  </div>


<?php
}

if ( $my_account  === 'modifie' )
{
  header("Refresh:3; url=my_account.php");
  include('header.php');
?>

        <div id="titre_connexion">
          <h2>Vos informations ont bien été enregistrées.</h2>
        </div>

<?php
}

if ( $my_account  === 'problem_file' )
{
  header("Refresh:4; url=my_account.php");
  include('header.php');
?>

        <div id="titre_connexion">
          <h2>Il y a eu un problème lors de l'importation de votre fichier.</h2>
          <br><br>
          <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
            situé en bas de page</p>
        </div>

<?php
}

if ( $my_account  === 'exceeded_size' )
{
  header("Refresh:4; url=my_account.php");
  include('header.php');
?>

        <div id="titre_connexion">
          <h2>Votre fichier est trop volumineux.</h2>
          <br><br>
          <p>Veuillez essayer avec un autre fichier ou tentez de le compresser avant de l'importer à nouveau.</p>
        </div>

<?php
}

if ( $my_account  === 'wrong_format' )
{
  header("Refresh:3; url=my_account.php");
  include('header.php');
?>

        <div id="titre_connexion">
          <h2>Votre fichier n'a pas le bon format.</h2>
          <br><br>
          <p>Les formats d'image autorisés sont : JPEG, JPG, GIF, PNG .</p>
        </div>

<?php
}

if ( $my_account  === 'upload_issue' )
{
  header("Refresh:5; url=my_account.php");
  include('header.php');
?>

    <h2>Il y a eu un problème lors de l'importation de votre fichier et nous nous en excusons.</h2>
    <br><br>
    <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
      situé en bas de page</p>

<?php
}


if ( $my_account  === 'deletion_issue' )
{
  header("Refresh:5; url=my_account.php");
  include('header.php');
?>

    <h2>Nous n'avons pas pu supprimer votre avatar et nous nous en excusons.</h2>
    <br><br>
    <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
      situé en bas de page</p>

<?php
}

include('footer.php');
