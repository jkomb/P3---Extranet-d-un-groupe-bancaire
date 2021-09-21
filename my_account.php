<!DOCTYPE html>
<?php

include_once('functions.php');

session_start();

$page = 'modification';

$bdd = connexionBDD();

if( isConnected() === true )
{
  $avatar_name = "user_photo_".strval($_SESSION['id_user']);
  $max_file_size = 8000000 ;
  $nbr_donnees_modif = 0;

  if ( empty($_POST) )
  {
    $request_infos = $bdd->prepare('SELECT nom, prenom, username, question FROM accounts WHERE id_user=:id_user LIMIT 0,1' );
    $request_infos -> execute( array( 'id_user' => $_SESSION['id_user']  ) );
    $infos_user = $request_infos -> fetch();

    $my_account = 'infos';
  }

  else
  {
    $user_entree['nom'] = htmlspecialchars( $_POST['nom'] );
    $user_entree['prenom'] = htmlspecialchars( $_POST['prenom'] );
    $user_entree['username'] = htmlspecialchars( $_POST['username'] );
    $user_entree['password'] = htmlspecialchars($_POST['password'] );
    $user_entree['question'] = htmlspecialchars( $_POST['question'] );
    $user_entree['reponse'] = htmlspecialchars( $_POST['reponse'] );

    $sql_request = 'UPDATE accounts SET ';

    foreach ( $user_entree as $cle => $valeur )
    {
      if ( $valeur != null )
      {
        if ( $cle === 'password' || $cle === 'reponse' )
        {
          $user_data[$cle] = password_hash( $valeur, PASSWORD_DEFAULT );
        }
        else
        {
          $user_data[$cle] = $valeur;
        }
        $nbr_donnees_modif ++;
      }
    }

    foreach ($user_data as $cle => $valeur )
    {
      if ( $nbr_donnees_modif >= 2 )
      {
        $sql_request .= sprintf('%s= :%s, ', $cle, $cle );
      }
      else
      {
        $sql_request .= sprintf( '%s= :%s ', $cle, $cle );
      }

      $nbr_donnees_modif --;
    }

    $sql_request .= sprintf( 'WHERE id_user= :%s', $_SESSION['id_user'] );


    //echo $sql_request;
    //var_dump($user_data);


    $modification = $bdd -> prepare($sql_request);
    $modification -> execute( $user_data );


    if ( isset($_FILES['file']) && $_FILES['file']['error'] === 0 )
    {
      if ( $_FILES['file']['size'] <= $max_file_size )
      {
        $infosfichier = pathinfo($_FILES['file']['name']);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('jpg', 'jpeg', 'png');
        if (in_array($extension_upload, $extensions_autorisees))
        {
          move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$avatar_name);
        }
      }
    }

    $modification -> closeCursor();
    $user_data = array();

    $my_account = 'modifie';
  }
}

else
{
  $my_account  = 'deconnecte';
}

//Affichage de la page

if ( $my_account  === 'infos' )
{
  include('header.php');
?>
<body>

  <div id="titre_connexion">
    <h1>Vos informations personnelles</h1>
    <br>
    <h3>Si vous le souhaitez,vous pouvez saisir les nouvelles informations à prendre en compte ci-dessous:</h3>
  </div>

  <div id="page_connexion">
    <div>
    <form  method="post" action="my_account.php">

        <div class="champs_connexion">
            <label><strong>Nom</strong></label>
            <input type=text name=nom
            placeholder="<?php echo $infos_user['nom']; ?>"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Prénom</strong></label>
          <input type=text name=prenom
          placeholder="<?php echo $infos_user['prenom']; ?>"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Nom d'utilisateur</strong></label>
          <input type=text name=username
          placeholder="<?php echo $infos_user['username']; ?>"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Mot de passe</strong></label>
          <input type=password name=password />
        </div>

        <div class="champs_connexion">
          <label><strong>Question secrète</strong></label>
          <input type=text name=question
          placeholder="<?php echo $infos_user['question']; ?>"/>
        </div>

        <div class="champs_connexion">
          <label><strong>Réponse à la réponse secrète</strong></label>
          <input type=password name=reponse />
        </div>

        <div class="champs_connexion">
          <input type=submit placeholder="Valider"/>
        </div>

      </form>
      <br>
      <form action="my_account.php" method="post" enctype="multipart/form-data">
        <div class="champs_connexion">
            <label><strong>Insérer votre avatar :</strong></label><br/>
            <input type="file" name=file/><br/>
            <input type="submit" value="Uploader" />
        </div>
    </form>
    </div>
  </div>

</body>
<?php
}

if ( $my_account  === 'modifie' )
{
  sleep(3);
  header('Location: my_account.php');
  include('header.php');
?>
  <body>
        <div id="titre_connexion">
          <h2>Vos informations ont bien été enregistrées.</h2>
        </div>
  </body>
<?php
}

redirectIndexIfNotConnected();

include('footer.php');
