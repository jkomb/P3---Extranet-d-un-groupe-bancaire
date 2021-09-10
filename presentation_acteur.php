<?php

session_start();

$_SESSION['page'] = 'presentation_acteur';

include('header.php');
include('Admin/admin.php');

unset( $_SESSION['page'] );

if ( isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_GET['acteur']) )
{
  $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', $login, $pwd,
             array( PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION ));

  $id_acteur_choisi = htmlspecialchars($_GET['acteur']);

  if ( isset($_POST['post']) && !empty($_POST['post']) && $_SERVER['REQUEST_METHOD'] == 'POST' )
  {
    $post = htmlspecialchars( $_POST['post'] );

    $inscription_com = $bdd -> prepare('INSERT INTO posts(id_user,id_acteur, post) VALUES (?,?,?)');
    $inscription_com -> execute(
                                  array(
                                        $_SESSION['id_user'],
                                        $id_acteur_choisi,
                                        $post
                                      )
                                );

    $inscription_com -> closeCursor();

    unset($_POST['post']);
  }

  $request = $bdd -> prepare('SELECT acteur, description FROM acteurs WHERE id_acteur=?');
  $request -> execute( array( $id_acteur_choisi ) );
  $info = $request -> fetch();

  if ( !empty($info) )
  {
?>

<body>
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

<section class="saisie_com">
  <form  method="post" action="presentation_acteur.php?acteur=<?php echo $id_acteur_choisi; ?>">

    <div>
      <label><strong>Ajouter un commentaire</strong></label>
    </div>
    <br>
    <div>
      <textarea class="zone_commentaire" name="post" placeholder ="Votre commentaire"></textarea>
    </div>
    <br>
    <div>
      <input type="submit" value="Valide le commentaire"/>
    </div>

  </form>
</section>

<section class="section_commentaires">
<?php

  $table_posts = $bdd -> prepare('SELECT accounts.username username, posts.post commentaire, posts.date_add date_com
                                  FROM posts
                                    INNER JOIN accounts
                                      ON accounts.id_user = posts.id_user
                                        WHERE posts.id_acteur = ?
                                          ORDER BY date_com
                                            DESC LIMIT 0, 5');
  $table_posts -> execute( array( $id_acteur_choisi ) );
  $posts = $table_posts -> fetch();

  while ( !empty($posts) )
  {
  ?>

      <article class="commentaire">

        <div>
          <h4><?php echo $posts['username']; ?></h4>
          <p><?php echo $posts['date_com']; ?></p>
        </div>
        <br>
        <div>
          <h4><?php echo $posts['commentaire']; ?></h4>
        </div>

      </article>

<?php
  $posts = $table_posts -> fetch();
  }
?>
</section>

</body>

<?php
}

else
{
?>
<body>
     <div id="titre_connexion">

       <h1>Cette page n'existe pas !</h1>
       <br><br><br>
       <h2>Vous allez être redirigé vers la page principale.</h2>

     </div>
</body>
 <?php
     header('refresh:3;url=main.php');
  }

$request ->closeCursor();

}

else
{
?>
<body>
  <div id="titre_connexion">

    <h1>Vous devez être connecté pour accéder à cette page</h1>
    <br><br><br>
    <h2>Vous allez être redirigé vers la page d'accueil.</h2>

  </div>

</body>
<?php

header('refresh:3;url=index.php');
 }

include('footer.php');

?>
