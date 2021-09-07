<?php

session_start();
$_SESSION['page'] = 'presentation_acteur';

include('header.php');

unset($_SESSION['page']);

if ( isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_GET['acteur']) )
{
  $id_acteur_choisi = htmlspecialchars($_GET['acteur']);

  $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
             array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

  $request = $bdd->prepare('SELECT * FROM acteurs WHERE id_acteur=?');
  $request -> execute(array($id_acteur_choisi));
  $info = $request -> fetch();

  if ( !empty($info) )
  {

?>

<section class="presentation">

  <article>

    <div class="titre_logo_presentation">
      <br>
      <img src="images/<?php echo $info['acteur']; ?>.png" alt="Logo <?php echo $info['acteur']; ?>"/ class="logo"/>
      <br>
    </div>

    <?php echo nl2br( $info['description'] ) ; ?>

  </article>

</section>
<?php
  }

  else
  {
     ?>

     <div id="titre_connexion">

       <h1>Cette page n'existe pas !</h1>
       <br><br><br>
       <h2>Vous allez être redirigé vers la page principale.</h2>

     </div>

 <?php
     header('refresh:3;url=main.php');
  }

$request ->closeCursor();

}

else
{
?>

<div id="titre_connexion">

  <h1>Vous devez être connecté pour accéder à cette page</h1>
  <br><br><br>
  <h2>Vous allez être redirigé vers la page d'accueil.</h2>

</div>

<?php

header('refresh:3;url=index.php');
 }

include('footer.php');

?>
