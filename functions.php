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
  ?>

  <body>
    <div id="titre_connexion">
      <h1>Vous devez vous connecter pour accéder à cette page</h1>
      <br><br><br>
      <h2>Vous allez être redirigé vers la page d'accueil.</h2>
    </div>
  </body>

  <?php
  }
}
