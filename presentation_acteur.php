<?php

  session_start();

  include('header.php');

  if(isset($_SESSION['nom'])&&isset($_SESSION['prenom'])&&isset($_GET))
  {
    $_GET['acteur']=htmlspecialchars($_GET['acteur']);

    $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
               array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    $nom_acteurs = $bdd->query('SELECT acteur FROM acteurs');


    //print_r($nom_acteurs->fetch()[0]);
    echo $_GET['acteur'];

    if($_GET['acteur']==$nom_acteurs->fetch()[0])
    {
      $acteur_choisi = $bdd->prepare('SELECT * FROM acteurs WHERE acteur=?');
      $acteur_choisi->execute(array($_GET['acteur']));
  ?>

  <section class="section_acteurs">

    <article class="article_acteur">
      <a href="presentation_acteur.php?acteur=<?php echo $acteur_choisi['acteur']?>">
        <img src="images/<?php echo $acteur_choisi['acteur']?>.png" alt="Logo <?php echo $acteur_choisi['acteur']?>"
             class ="logo_acteurs" />
      </a>
      <div>
        <h2><?php echo $acteur_choisi['acteur']?></h2>
        <p><?php echo $acteur_choisi['description']?></p>
      </div>
    </article>
  <?php
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

include('footer.php');

 ?>
