<?php

  session_start();

  include('header.php');

  if(isset($_SESSION['nom'])&&isset($_SESSION['prenom']))
  {
?>

  <div id="banniere_image">
    <!--
      <div id="banniere_description">
          Ensemble pour aller plus loin
      </div>
    -->
  </div>

  <section class="presentation">
    <article>
        <img src="images/GBAF.png" alt="Logo GBAF" class="logo" />
        <h1>Votre partenaire dans le temps</h1></br>
        Le Groupement Banque Assurance Français (<strong>GBAF</strong>) est une fédération représentant
        les 6 grands groupes français :
        <ul class="default">
            <li>BNP Paribas</li>
            <li>BPCE</li>
            <li>Crédit Agricole</li>
            <li>Crédit Mutuel-CIC</li>
            <li>Société Générale</li>
            <li>La Banque Postale</br></li>
        </ul>
        Même s’il existe une forte concurrence entre ces entités, elles vont toutes
        travailler de la même façon pour gérer près de 80 millions de comptes sur
        territoire national. Le <strong>GBAF</strong> est le représentant de la profession bancaire et des assureurs sur
        tous les axes de la réglementation financière française. Sa mission est de promouvoir l'activité bancaire à l’échelle nationale. C’est aussi un interlocuteur
        privilégié des pouvoirs publics.</br>
    </article>

  </section>

  <section class="section_acteurs">

    <?php

      $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
                 array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

      $table_acteurs = $bdd->query('SELECT * FROM acteurs');

      while($acteur = $table_acteurs->fetch())
      {
    ?>
          <section class="section_acteurs">

            <article class="article_acteur">

              <div class="logo_titre_acteur">
                <div>
                  <a href="presentation_acteur.php?acteur=<?php echo $acteur['acteur']?>">
                    <img src="images/<?php echo $acteur['acteur']?>.png" alt="Logo <?php echo $acteur['acteur']?>"
                         class ="logo_acteurs" />
                  </a>
                </div>
                <div>
                  <h2><?php echo $acteur['acteur']?></h2>
                  <div class="overflow">
                    <?php echo $acteur['description_courte']?>
                  </div>
                </div>
              </div>

              <a href="presentation_acteur.php?acteur=<?php echo $acteur['acteur']?>">
                Lire la suite
              </a>

            </article>

          </section>

      <?php
      }
    }
    else
    {
      echo'
      <div id="titre_connexion">

        <h1>Vous devez vous connecter pour accéder à cette page</h1>
        <br><br><br>
        <h2>Vous allez être redirigé vers la page d\'accueil.</h2>

      </div>';

      header('refresh:3;url=accueil.php');
    }
      ?>

    </section>

<?php include('footer.php'); ?>


    </body>
