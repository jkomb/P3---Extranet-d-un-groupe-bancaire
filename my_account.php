<?php

include_once('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'modification';

$bdd = connexionBDD();

$max_file_size_bytes = 8000000 ;

if ( empty($_POST) )
{
  $my_account = 'infos';
}

else
{
  $user_entries['nom'] = mb_strtoupper( htmlspecialchars( $_POST['nom'] ) );
  $user_entries['prenom'] = ucfirst( mb_strtolower( htmlspecialchars( $_POST['prenom'] ) ) );
  $user_entries['username'] = htmlspecialchars( $_POST['username'] );
  $user_entries['question'] = htmlspecialchars( $_POST['question'] );
  $user_entries['reponse'] = htmlspecialchars( $_POST['reponse'] );

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

  }

  $sql_request = 'UPDATE accounts SET ';
  $keys = array();

  $user_data = array_filter($user_entries);

  foreach ( $user_data as $key => $value )
  {
    array_push( $keys, sprintf( '%s= :%s', $key, $key ) );
  }

  $user_data['id_user'] = $_SESSION['id_user'];

  if ( array_key_exists( 'password', $user_data ) )
  {
    $user_data['password'] = password_hash( $user_data['password'], PASSWORD_DEFAULT );
  }

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
    <h3>Si vous le souhaitez, vous pouvez saisir les nouvelles informations à prendre en compte ci-dessous:</h3>
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
          <label><strong>Mot de passe</strong></label>
          <div>
            <a href="creation_compte.php?mdp=oublie" >Réinitialiser mon mot de passe</a>
          </div>
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
          <input type="submit" placeholder="Valider"/>
        </div>

      </form>
      <form action="my_account.php" method="post" enctype="multipart/form-data">

        <div class="champs_connexion">
            <label><strong>Insérer votre avatar :</strong></label><br/>
            <input type="file" name="image"/><br/>
            <input type="submit" value="Importer" />
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
