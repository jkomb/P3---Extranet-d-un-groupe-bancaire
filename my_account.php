<?php

include('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'modification';

$bdd = connexionBDD();

$my_account = "";

$max_file_size_bytes = 8000000 ;

if ( empty($_POST) )
{
  $my_account = 'infos';

  if ( !empty($_FILES['image']) )
  {
    if ( is_uploaded_file($_FILES['image']['tmp_name'] ) === false )
    {
      echo '1:Error on upload: Invalid file definition';
      exit;
    }

    if ( $_FILES['image']['size'] > $max_file_size_bytes )
    {
      echo '2:Exceeded file size limit.';
      exit;
    }

    $file_mimetype = mime_content_type($_FILES['image']['tmp_name']);
    $authorized_mime_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');

    $verify_mime_type = array_search( $file_mimetype, $authorized_mime_types );


    if ( $verify_mime_type === false )
    {
      echo '3:Invalid file format.';
      exit;
    }

    $uploadName = $_FILES['image']['name'];
    $extension = strtolower(substr($uploadName, strripos($uploadName, '.')+1));
    $filename = hash_file('sha256', $_FILES['image']['tmp_name']) . '.' . $extension;

    if ( !move_uploaded_file( $_FILES['image']['tmp_name'], './uploads/'.$filename ) )
    {
      echo __LINE__;
    }

    $user_entries['avatar'] = $filename;
    $_SESSION['avatar'] = $filename;
    $modification = $bdd -> prepare('UPDATE accounts SET avatar= :avatar WHERE id_user= :id_user');
    $modification -> execute( ['avatar' => $_SESSION['avatar'], 'id_user' => $_SESSION['id_user'] ] );

    $modification -> closeCursor();
  }

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
<body>

  <div id="titre_connexion">
    <h1>Vos informations personnelles</h1>
    <br>
    <h3>Si vous le souhaitez, vous pouvez modifier vos informations personnelles ci-dessous :</h3>
  </div>

  <div id="page_connexion">
    <div>
      <form  method="post" action="my_account.php" class="champs_connexion" name="infos_form">

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
      <form action="my_account.php" method="post" enctype="multipart/form-data" name="avatar_form">

        <div class="champs_connexion">
            <label><strong>Insérer votre avatar :</strong></label><br/>
            <input type="file" name="image"/><br/>
            <input type="submit" value="Importer" />
        </div>

      </form>
      <br><br>
      <form  method="post" action="my_account.php" name="password_form">

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
          <label>Veuillez saisir votre nouveau </br> mot de passe à nouveau</label>
          <input type="password" name="new_passwordbis" required/>
        </div>

        <div class="champs_connexion">
            <input type="submit" value="Valider"/>
        </div>

      </form>

    </div>
  </div>

</body>
<?php
}

if ( $my_account  === 'modifie' )
{
  header("Refresh:2; url=my_account.php");
  include('header.php');
?>
  <body>
        <div id="titre_connexion">
          <h2>Vos informations ont bien été enregistrées.</h2>
        </div>
  </body>
<?php
}

include('footer.php');
