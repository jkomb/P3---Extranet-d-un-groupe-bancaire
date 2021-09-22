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
  if( isset( $_SESSION ))
  {
    if ( array_key_exists('id_user', $_SESSION) === false )
    {
      return false;
    }

    else
    {
      return true;
    }
  }
}

function redirectMainIfConnected()
{
  if ( isConnected() === true )
  {
    header('Location: main.php');
    include('header.php');
  }
}

function redirectIndexIfNotConnected()
{
  if ( isConnected() === false )
  {
    $page = 'main';
    sleep(3);
    header('Location: main.php');
    include('header.php');
  }
}

function displayAvatar( $avatar, $id_user )
{
  if( $avatar === 1 )
  {
    $avatar_name = "user_photo_".strval($id_user);
?>
    <img src="upload/<?php echo $avatar_name;?>" alt="user_avatar" />
<?php
  }
}
