<!DOCTYPE html>
<?php

include_once('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'principale';

$bdd = connexionBDD();

include('header.php');

if( isConnected() === true )
{
  $table_acteurs = $bdd -> query('SELECT id_acteur, acteur, description_courte FROM acteurs LIMIT 0,4');

?>
  <div id="banniere_image"></div>

  <body>
    <section class="presentation">
      <article>
          <div class="titre_logo_presentation">
            <img src="images/GBAF.png" alt="Logo GBAF"/ class="logo_GBAF"/>
            <h1>Votre partenaire dans le temps</h1>
          </div>
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
          territoire national.</br></br>
          Le <strong>GBAF</strong> est le représentant de la profession bancaire et des assureurs sur
          tous les axes de la réglementation financière française.</br></br>
          Sa mission est de promouvoir l'activité bancaire à l’échelle nationale.</br></br>
          C’est aussi un interlocuteur privilégié des pouvoirs publics.
      </article>
    </section>

  <section class="section_acteurs">

<?php
  while( !empty( $acteur = $table_acteurs -> fetch() ) )
  {
?>
      <section class="section_acteurs">

        <article class="article_acteur">

          <div class="logo_titre_pres_acteur">
              <a href="presentation_acteur.php?acteur=<?php echo $acteur['id_acteur']?>">
                <img src="images/<?php echo $acteur['acteur']?>.png" alt="Logo <?php echo $acteur['acteur']?>"
                     class ="logo_acteurs" />
              </a>
          </div>

          <div class="logo_titre_pres_acteur">
              <div clas="titre_pres_acteur">
                <h2><?php echo $acteur['acteur']?></h2>
                <?php echo $acteur['description_courte']?>
            </div>
          </div>

          <div class="logo_titre_pres_acteur">
              <a href="presentation_acteur.php?acteur=<?php echo $acteur['id_acteur']?>">
                Lire la suite
              </a>
          </div>

        </article>

      </section>
    <?php
  }
?>
    </section>
  </body>
<?php
}

include('footer.php');
