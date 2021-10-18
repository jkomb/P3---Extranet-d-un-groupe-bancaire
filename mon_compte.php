<?php

include('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'modification';

$bdd = connexionBDD();

$_SESSION['mon_compte'] = '';

$my_account = 'infos';

if ( empty($_POST) )
{
  $my_account = 'infos';

  if ( !empty($_FILES['image']) )
  {
    $_SESSION['mon_compte'] = uploadAvatar($bdd, $_FILES['image']);
  }
}

elseif (isset($_POST['delete_avatar']) && !empty($_POST['delete_avatar']))
{
  if ( array_key_exists('avatar', $_SESSION) )
  {
    $_SESSION['mon_compte'] = deleteAvatar($bdd);
  }

  header('Location: mon_compte.php');
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

  //construction dynamique de la requête SQL pour ne mettre à jour que les données saisies
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

  $_SESSION['mon_compte'] = 'modified';
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

  <?php displayMessage($_SESSION['mon_compte']); ?>

  <div id="page_connexion">
    <div>
      <form  method="post" action="mon_compte.php" class="champs_connexion">

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
      <form action="mon_compte.php" method="post" enctype="multipart/form-data">

        <div class="champs_connexion">
            <label><strong>Insérer votre avatar </strong>(taille max. : 8 mo)</label>
            <br>
            <input type="file" name="image"/>
            <br>
            <input type="submit" value="Importer"/>
        </div>

        <?php buttonDeleteAvatar()?>

      </form>

      <br><br>

      <form  method="post" action="mon_compte.php">

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

unset($_SESSION['mon_compte']);

include('footer.php');
