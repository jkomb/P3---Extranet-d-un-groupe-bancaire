<!DOCTYPE html>
<?php

include_once('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'presentation_acteur';

$bdd = connexionBDD();

if ( isConnected() === true )
{
  if ( isset($_GET['acteur']) )
  {
    $id_acteur_choisi = htmlspecialchars($_GET['acteur']);

    if ( isset($_POST['post']) && !empty($_POST['post']) && $_SESSION['post'] )
    {
      $post = htmlspecialchars( $_POST['post'] );

      $inscription_com = $bdd -> prepare('INSERT INTO posts(id_user,id_acteur, post) VALUES (:id_user,:id_acteur,:post)');
      $inscription_com -> execute(
                                    array(
                                          'id_user' => $_SESSION['id_user'],
                                          'id_acteur' => $id_acteur_choisi,
                                          'post' => $post
                                        )
                                  );

      $inscription_com -> closeCursor();

      $_SESSION['post'] = false;
    }

    $request = $bdd -> prepare('SELECT acteur, description FROM acteurs WHERE id_acteur=:id_acteur LIMIT 0,1');
    $request -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    $info = $request -> fetch();

    if ( !empty($info) )
    {
      $exists_actor= true;
      $sql_request = <<<SQL
      SELECT accounts.username username, posts.post commentaire, posts.date_add date_com
        FROM posts
          INNER JOIN accounts
            ON accounts.id_user = posts.id_user
              WHERE posts.id_acteur = :id_acteur
                ORDER BY date_com
                  DESC LIMIT 0, 5
      SQL;
      $table_posts = $bdd -> prepare($sql_request);
      $table_posts -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    }

    else
    {
      $exists_actor = false;
    }

  }
}

else
{
  $_SESSION['connexion'] = false;
}

//Affichage de la page

if ( $exists_actor === true )
{
  include('header.php');
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
  while ( !empty( ( $posts = $table_posts -> fetch() ) ) )
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
  }
?>
  </section>

</body>
<?php
}

else
{
  sleep(3);
  header('Location: main.php');
  include('header.php');
?>
  <body>
       <div id="titre_connexion">

         <h1>Cette page n'existe pas !</h1>
         <br><br><br>
         <h2>Vous allez être redirigé vers la page principale.</h2>

       </div>
  </body>
 <?php
}

include('footer.php');
