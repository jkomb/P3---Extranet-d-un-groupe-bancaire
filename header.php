

<!--
  Données d'en-tête : ce sont les mêmes pour toutes les .

  Head datas : the same for every page.
-->

<head>

  <title>Groupement Banque Assurance Français</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
  <link rel="stylesheet" href="main.css" />
  <link rel="icon" type="image/png" sizes="16x16" href="images\GBAF.png"/>

</head>

<!--
  Données du header : le header est le même pour toutes les pages
  à l'exception de la page de connexion et de création de compte.

  Header datas : header datas are common to every pages excepted
  for the connexion's page and the account creation's page.
-->

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

      if(!isset($_SESSION))
      {
        echo'
        <div id="connexion_head">
          <p>S\'il s\'agit de votre <strong>première visite,</strong></p>
          <p>cliquez <a href=creation_compte.php><strong>ICI</strong></a></p>
          <p>pour créer votre compte.</p>
        </div>';
      }

      elseif(isset($_SESSION['page'])&&$_SESSION['page']=='creation')
      {
        echo'
        <div id="creation_head">
          <p>Retourner à l\'<a href=accueil.php><strong>ACCUEIL</strong></a></p>
        </div>';
      }

      elseif(isset($_SESSION['nom'])&&isset($_SESSION['prenom']))
      {
        //insérer l'avatar de l'utilsateur
   ?>
        <div id="navigation">

            <div>
              <a href="accueil.php"<button>Se Déconnecter</button></a></br>
            </div>

            <div>
              Vous êtes connecté en tant que <?php echo $_SESSION['prenom'];?>
              <strong> <?php echo $_SESSION['nom'];?></strong>
            </div>

              <nav>
                <ul>
                  <li><a href="#">Mon Compte</a></li>
                </ul>
              </nav>

        </div>

    <?php
      }
  ?>

</header>
