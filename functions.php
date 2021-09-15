<?php

include('Admin/admin.php');

function connexionBDD()
{
  global $login, $pwd;
  return(
    new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', $login, $pwd,
             array( PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION ))
           );
}
