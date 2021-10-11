<?php

function connexionBDD()
{
  // Make sure to declare those 2 environment variables in the proper directory
  $login = getenv('HTTP_DATABASE_USER');
  $pwd = getenv('HTTP_DATABASE_PWD');
  $server_location = getenv('HTTP_DATABASE_LOCATION');
  $database_host = getenv('HTTP_DATABASE_HOST');
  $database_name = getenv('HTTP_DATABASE_NAME');

  return
    new PDO('mysql:'.$database_host.'='.$server_location.';dbname='.$database_name.';charset=utf8', $login, $pwd,
             [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}

function isConnected()
{
  if ( isset( $_SESSION['id_user'] ) )
  {
    return true;
  }
  else
  {
    return false;
  }
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
    $page = 'main';
    header('Location: main.php');
    include('header.php');
  }
}

function displayAvatar()
{
  if( $_SESSION["avatar"] != "" )
  {
?>
    <img src="./uploads/<?php $_SESSION['avatar'];?>" alt="<?php $_SESSION['username'];?>_avatar" />
<?php
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
