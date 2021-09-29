<?php

function connexionBDD()
{
  // Make sure to declare those 2 environment variables in the proper directory
  $login = getenv('HTTP_DATABASE_USER');
  $pwd = getenv('HTTP_DATABASE_PWD');
  return
    new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', $login, $pwd,
             [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}

function isConnected()
{
  if( !isset( $_SESSION ))
  {
      return false;
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
