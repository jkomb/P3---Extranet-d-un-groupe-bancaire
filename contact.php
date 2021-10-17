<?php

include('functions.php');

session_start();

if ( isConnected() )
{
  $page = 'modification';
}
else
{
  $page = 'creation';
}

include('header.php');
?>

<div id="titre_connexion">
  <h1>Ceci est la page de contact.</h1>
</div>
