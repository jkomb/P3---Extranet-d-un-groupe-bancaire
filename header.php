<!DOCTYPE html>

<head>

<title>Groupement Banque Assurance Français</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<link rel="stylesheet" href="main.css" />
<link rel="icon" type="image/png" sizes="16x16" href="images\GBAF.png"/>

</head>

<header>

<div id="logo">
    <img src="images/GBAF.png" alt="Logo GBAF" />
    <div>
      <h2>Votre partenaire dans le temps</h2>
    </div>
</div>

<?php

/*
  Affichage de la navigation dans la page d'accueil pour créer
  un nouveau compte.

  Diplay of navigation menu in the welcome page to createa new account.
*/

if ( isset($_SESSION['page']) && $_SESSION['page'] === 'index' )
{
?>
  <div id="connexion_head">
    <p>S'il s'agit de votre <strong>première visite,</strong></p>
    <p>cliquez <a href=creation_compte.php><strong>ICI</strong></a></p>
    <p>pour créer votre compte.</p>
  </div>

<?php
}

elseif ( isset($_SESSION['page']) && $_SESSION['page'] === 'creation' )
{
?>

  <div id="creation_head">
    <p>Retourner à l'<a href=index.php><strong>ACCUEIL</strong></a></p>
  </div>

<?php
}

elseif ( isset($_SESSION['nom']) && isset($_SESSION['prenom']) )
{


  if( isset($_SESSION['page']) && $_SESSION['page'] === 'modification' )
  {
?>

    <div id="navigation">

        <div>
          <a href="index.php?log=off"<button>Se Déconnecter</button></a></br>
        </div>

        <div>
          Vous êtes connecté en tant que <?php echo $_SESSION['prenom'];?>
          <strong> <?php echo $_SESSION['nom'];?></strong>
        </div>

          <div class = "nav_pres_acteur">
            <a href="main.php">Page principale</a>
          </div>

    </div>

<?php
  }

  elseif( isset($_SESSION['page']) && $_SESSION['page'] === 'presentation_acteur' )
  {
?>

    <div id="navigation">

        <div>
          <a href="index.php?log=off"<button>Se Déconnecter</button></a></br>
        </div>

        <div>
          Vous êtes connecté en tant que <?php echo $_SESSION['prenom'];?>
          <strong> <?php echo $_SESSION['nom'];?></strong>
        </div>

        <div class = "nav_pres_acteur">
          <div class = "nav_pres_acteur">
            <a href="main.php">Page principale</a>
          </div>
          <div class = "nav_pres_acteur">
            <a href="my_account.php">Mon Compte</a>
          </div>
        </div>

    </div>

<?php
  }

  else
  {
?>

  <div id="navigation">

      <div>
        <a href="index.php?log=off"<button>Se Déconnecter</button></a></br>
      </div>

      <div>
        Vous êtes connecté en tant que <?php echo $_SESSION['prenom'];?>
        <strong> <?php echo $_SESSION['nom'];?></strong>
      </div>

      <div class = "nav_pres_acteur">
        <a href="my_account.php">Mon Compte</a>
      </div>

  </div>

<?php
  }
}
?>

</header>
