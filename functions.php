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
    exit;
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
    $my_account = 'problem_file';
  }
  else
  {
    if ( $file['size'] > $max_file_size_bytes )
    {
      $my_account = 'exceeded_size';
    }
    else
    {
      $file_mimetype = mime_content_type($file['tmp_name']);
      $authorized_mime_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');

      $verify_mime_type = array_search( $file_mimetype, $authorized_mime_types );

      if ( $verify_mime_type === false )
      {
        $my_account = 'wrong_format';
      }
      else
      {
        $uploadName = $file['name'];
        $extension = strtolower(substr($uploadName, strripos($uploadName, '.')+1));
        $filename = uniqid(rand(),true) .'.'. $extension;

        $save_path = "./uploads/".$filename;

        if ( !move_uploaded_file( $file['tmp_name'], $save_path ) )
        {
          $my_account = 'upload_issue';
        }
        else
        {
          $user_entries['avatar'] = $filename;
          $_SESSION['avatar'] = $filename;
          $modification = $bdd -> prepare('UPDATE accounts SET avatar= :avatar WHERE id_user= :id_user');
          $modification -> execute( ['avatar' => $_SESSION['avatar'], 'id_user' => $_SESSION['id_user'] ] );

          $modification -> closeCursor();
        }
      }
    }
  }
}

function deleteAvatar($bdd)
{
  try
  {
    $bdd -> beginTransaction();
    $delete_avatar = $bdd -> prepare('UPDATE accounts SET avatar=NULL  WHERE id_user= :id_user');
    $delete_avatar -> execute( ['id_user' => $_SESSION['id_user']] );
    $bdd -> commit();
  }catch(Exception $e)
    {
      $bdd ->rollback();
      $my_account = 'deletion_issue';
      exit();
    }
    unlink('./uploads/'.$_SESSION['avatar']);
    unset($_SESSION['avatar']);

    $delete_avatar -> closeCursor();
}
