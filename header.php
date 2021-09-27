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


if ( isset($page) )
{
  switch ($page)
  {
    case "index":
    $affichage_header = 'creation';
    break;

    case "creation":
    $affichage_header = 'retour_accueil';
    break;

    case "modification":
      $affichage_header = 'modification';
      break;

    case "presentation_acteur":
      $affichage_header = 'acteur';
      break;

    default:
      $affichage_header = 'principale';
    }
}

/*
Affichage de la page
Display of the page
*/

if ( $affichage_header === 'creation' )
{
?>
  <div id="connexion_head">
    <p>S'il s'agit de votre <strong>première visite,</strong></p>
    <p>cliquez <a href=creation_compte.php><strong>ICI</strong></a></p>
    <p>pour créer votre compte.</p>
  </div>
<?php
}

if ( $affichage_header === 'retour_accueil' )
{
?>
  <div id="creation_head">
    <p>Retourner à l'<a href=index.php><strong>ACCUEIL</strong></a></p>
  </div>
<?php
}

if ( $affichage_header === 'modification' )
{
?>
  <div id="navigation">

      <div>
        <a href="deconnexion.php"<button>Se Déconnecter</button></a></br>
        <?php displayAvatar( $_SESSION['avatar'], $_SESSION['id_user'] ); ?>
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

if ( $affichage_header === 'acteur' )
{
?>
  <div id="navigation">

      <div>
        <a href="deconnexion.php"<button>Se Déconnecter</button></a></br>
        <?php displayAvatar( $_SESSION['avatar'], $_SESSION['id_user'] ); ?>
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

if ( $affichage_header === 'principale' )
{
  unset( $affichage_header );
?>
  <div id="navigation">

    <div>
      <a href="deconnexion.php"<button>Se Déconnecter</button></a></br>
      <?php displayAvatar( $_SESSION['avatar'], $_SESSION['id_user'] ); ?>
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
?>
</header>
