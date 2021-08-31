<?php

session_start();

//vérifier les données utilisateurs reccueillies lors de la connexion et
//initialiser $_SESSION avec les infos dans $_POST

if(isset($_POST))
{
  /*
  $_SESSION['user_name']=htmlspecialchars($_POST["user_name"]);
  */
}


?>

<?php include('header.php');?>

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

  <article class="article_acteur">
    <a href="presentation_acteur.php?acteur=CDE" class="logo acteur">
      <img src="images/CDE.png" alt="CDE" class ="logo_acteurs" />
    </a>
    <div>
      <h2>CDE</h2>
      <p>Description de l'acteur/partenaire.</p>
    </div>
    <div<
      <button id="lireplus">Lire la suite</button>
    </div>
  </article>

  <article class="article_acteur">
    <a href="presentation_acteur.php?acteur=CDE" class="logo acteur">
      <img src="images/CDE.png" alt="CDE" class ="logo_acteurs" />
    </a>
    <div>
      <h2>CDE</h2>
      <p>Description de l'acteur/partenaire.</p>
    </div>
    <div<
      <button id="lireplus">Lire la suite</button>
    </div>
  </article>

  <article class="article_acteur">
    <a href="presentation_acteur.php?acteur=CDE" class="logo acteur">
      <img src="images/CDE.png" alt="CDE" class ="logo_acteurs" />
    </a>
    <div>
      <h2>CDE</h2>
      <p>Description de l'acteur/partenaire.</p>
    </div>
    <div<
      <button id="lireplus">Lire la suite</button>
    </div>
  </article>

  <article class="article_acteur">
    <a href="presentation_acteur.php?acteur=CDE" class="logo acteur">
      <img src="images/CDE.png" alt="CDE" class ="logo_acteurs" />
    </a>
    <div>
      <h2>CDE</h2>
      <p>Description de l'acteur/partenaire.</p>
    </div>
    <div<
      <button id="lireplus">Lire la suite</button>
    </div>
  </article>

</section>

            <footer>
                <div id="tweet">
                    <h1>Mon dernier tweet</h1>
                    <p>Hii haaaaaan !</p>
                    <p>le 12 mai à 23h12</p>
                </div>
                <div id="mes_photos">
                    <h1>Mes photos</h1>
                    <p><img src="images/photo1.jpg" alt="Photographie" /><img src="images/photo2.jpg" alt="Photographie" /><img src="images/photo3.jpg" alt="Photographie" /><img src="images/photo4.jpg" alt="Photographie" /></p>
                </div>
                <div id="mes_amis">
                    <h1>Mes amis</h1>
                    <div id="listes_amis">
                        <ul>
                            <li><a href="#">Pupi le lapin</a></li>
                            <li><a href="#">Mr Baobab</a></li>
                            <li><a href="#">Kaiwaii</a></li>
                            <li><a href="#">Perceval.eu</a></li>
                        </ul>
                        <ul>
                            <li><a href="#">Belette</a></li>
                            <li><a href="#">Le concombre masqué</a></li>
                            <li><a href="#">Ptit prince</a></li>
                            <li><a href="#">Mr Fan</a></li>
                        </ul>
                    </div>
                </div>
            </footer>
        </div>
    </body>
