<?php

function connexionBDD()
{

  $login = getenv('HTTP_DATABASE_USER');
  $pwd = getenv('HTTP_DATABASE_PWD');
  $server_location = getenv('HTTP_DATABASE_HOST');
  $database_name = getenv('HTTP_DATABASE_NAME');

  return
    new PDO('mysql:host='.$server_location.';dbname='.$database_name.';charset=utf8', $login, $pwd,
             [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}

function isConnected()
{
  if (isset( $_SESSION['id_user']) )
  {
    return true;
  }

  return array_key_exists('id_user', $_SESSION);
}

function redirectMainIfConnected()
{
  if ( isConnected() === true )
  {
    header('Location: main.php');
    exit;
  }
}

function redirectIndexIfNotConnected()
{
  if ( isConnected() === false )
  {
    header('Location: index.php');
    exit;
  }
}

function postAlreadyExists($pdo, $id_user, $id_acteur_choisi)
{
    $request = $pdo -> prepare("SELECT 1 FROM posts WHERE id_user= :id_user AND id_acteur= :id_acteur");
    $request -> execute(['id_user' => $id_user, 'id_acteur' => $id_acteur_choisi]);
    return (bool) $request -> fetchColumn();
}

function voteAlreadyExists($pdo, $id_user, $id_acteur_choisi)
{
    $request = $pdo -> prepare("SELECT 1 FROM vote WHERE id_user= :id_user AND id_acteur= :id_acteur");
    $request -> execute(['id_user' => $id_user, 'id_acteur' => $id_acteur_choisi]);
    return (bool) $request -> fetchColumn();
}

function isAdmin($bdd, $id_user)
{
    $request = $bdd -> prepare("SELECT 1 FROM admin WHERE id_user= :id_user");
    $request -> execute(['id_user' => $id_user]);
    return (bool) $request -> fetchColumn();
}

function displayAvatar()
{
  if( isset($_SESSION['avatar']) && $_SESSION['avatar'] != "" )
  {
?>
    <img src="./uploads/<?php echo $_SESSION['avatar'];?>" alt="<?php $_SESSION['username'];?>_avatar" class ="avatar" />
<?php
  }
}

function uploadAvatar($bdd, $file)
{
  $max_file_size_bytes = 8000000 ;

  if ( is_uploaded_file($file['tmp_name'] ) === false )
  {
    return 'problem_file';
  }

  if ( $file['size'] > $max_file_size_bytes )
  {
    return 'exceeded_size';
  }

  $file_mimetype = mime_content_type($file['tmp_name']);
  $authorized_mime_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');

  $verify_mime_type = array_search( $file_mimetype, $authorized_mime_types );

  if ( $verify_mime_type === false )
  {
    return 'wrong_format';
  }

  $uploadName = $file['name'];
  $extension = strtolower(substr($uploadName, strripos($uploadName, '.')+1));
  $filename = uniqid("",true) .'.'. $extension;

  $save_path = "./uploads/".$filename;

  if ( !move_uploaded_file( $file['tmp_name'], $save_path ) )
  {
    return 'upload_issue';
  }

  $user_entries['avatar'] = $filename;
  $_SESSION['avatar'] = $filename;
  $modification = $bdd -> prepare('UPDATE accounts SET avatar= :avatar WHERE id_user= :id_user');
  $modification -> execute( ['avatar' => $_SESSION['avatar'], 'id_user' => $_SESSION['id_user'] ] );

  $modification -> closeCursor();
}

function buttonDeleteAvatar()
{
  if (isset($_SESSION['avatar']))
  {
    ?>
    <div class="champs_connexion">
        <label><strong>Supprimer votre avatar</strong></label>
        <br>
        <input type="submit" name="delete_avatar" value="Supprimer"/>
    </div>
    <?php
  }
}

function deleteAvatar($bdd)
{
  try
  {
    $bdd -> beginTransaction();
    $delete_avatar = $bdd -> prepare('UPDATE accounts SET avatar=NULL  WHERE id_user= :id_user');
    $delete_avatar -> execute( ['id_user' => $_SESSION['id_user']] );

    unlink('./uploads/'.$_SESSION['avatar']);
    unset($_SESSION['avatar']);

    $bdd -> commit();
    $delete_avatar -> closeCursor();
  }catch(Exception $e)
    {
      $bdd ->rollback();
      return 'deletion_issue';
    }
}

function displayMessage($message)
{
  if (isset($message))
  {
    switch ($message)
    {
      //Page creation_compte
      case 'not_modified':
        ?>
          <div id="titre_connexion">
            <h2>Vos modifications n'ont PAS été prises en compte!</h2>
            <br>
            <p><strong>Les 2 saisies de votre nouveau mot de passe ne sont pas identiques!</strong></p>
          </div>
        <?php
      break;
      case 'unknown':
        ?>
          <div id="titre_connexion">
            <h1>Utilisateur inconnu</h1>
            <br>
            <h2>Merci de rentrer un nom d'utilisateur existant!</h2>
          </div>
        <?php
      break;
      case 'wrong_answer':
        ?>
          <div id="titre_connexion">
            <h1>Réponse incorrecte !</h1>
            <br>
            <h2>Merci de réessayer</h2>
          </div>
        <?php
      break;

      //Page mon_compte
      case 'modified':
        ?>
          <div id="titre_connexion">
            <h2>Vos informations ont bien été enregistrées.</h2>
          </div>
        <?php
      break;
      case 'problem_file':
        ?>
          <div id="titre_connexion">
            <h2>Il y a eu un problème lors de l'importation de votre fichier.</h2>
            <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
              situé en bas de page</p>
          </div>
        <?php
      break;
      case 'exceeded_size':
        ?>
          <div id="titre_connexion">
            <h2>Votre fichier est trop volumineux.</h2>
            <p>Veuillez essayer avec un autre fichier ou tentez de le compresser avant de l'importer à nouveau.</p>
          </div>
        <?php
      break;
      case 'wrong_format':
        ?>
          <div id="titre_connexion">
            <h2>Votre fichier n'a pas le bon format.</h2>
            <p>Les formats d'image autorisés sont : JPEG, JPG, GIF, PNG .</p>
          </div>
        <?php
      break;
      case 'upload_issue':
        ?>
          <div id="titre_connexion">
            <h2>Il y a eu un problème lors de l'importation de votre fichier et nous nous en excusons.</h2>
            <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
              situé en bas de page</p>
          </div>
        <?php
      break;
      case 'deletion_issue':
        ?>
          <div id="titre_connexion">
            <h2>Nous n'avons pas pu supprimer votre avatar et nous nous en excusons.</h2>
            <p>Veuillez essayer avec un autre fichier ou contacter notre support dans la rubrique <strong>Contact</strong>
              situé en bas de page</p>
          </div>
        <?php
      break;
    }
  }
}

function valueFilled($value)
{
  if (isset($_SESSION[$value]))
  {
    echo 'value='.$_SESSION[$value];
  }
}
