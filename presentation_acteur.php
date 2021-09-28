<?php

include_once('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'presentation_acteur';

$bdd = connexionBDD();

if ( isset($_GET['acteur']) )
{
  $id_acteur_choisi = htmlspecialchars($_GET['acteur']);

  if ( isset($_POST['post']) && !empty($_POST['post']) )
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

    header('Location: presentation_acteur.php?acteur='.$id_acteur_choisi);
    exit;
  }

  $acteur_request = $bdd -> prepare('SELECT acteur, description FROM acteurs WHERE id_acteur=:id_acteur LIMIT 0,1');
  $acteur_request -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
  $info_acteur = $acteur_request -> fetch();

  if ( !empty($info_acteur) )
  {
    $exists_actor= true;

    $posts_request = <<<SQL
    SELECT accounts.username username, posts.post commentaire, posts.date_add date_com, posts.id_post
      FROM posts
        INNER JOIN accounts
          ON accounts.id_user = posts.id_user
            WHERE posts.id_acteur = :id_acteur
              ORDER BY date_com
                DESC LIMIT 0, 5
    SQL;
    $table_posts = $bdd -> prepare( $posts_request );
    $table_posts -> execute( array( 'id_acteur' => $id_acteur_choisi ) );

    $unique_post_request = <<<SQL
    SELECT id_user
      FROM accounts
        WHERE EXISTS (
          SELECT TRUE
            FROM posts
              WHERE accounts.id_user = posts.id_user)
                LIMIT 0, 1
    SQL;

    $verify_unique_post = $bdd -> query( $unique_post_request );
    $info = $verify_unique_post -> fetch();

    if ( !in_array( $_SESSION['id_user'], $info ) )
    {
      $affichage_commentaire = true;
    }
    else
    {
      $affichage_commentaire = false;
    }
  }
  else
  {
    $exists_actor = false;
  }
}

/*
Affichage de la page
Display of the page
*/

if ( $exists_actor === true )
{
  include('header.php');
?>
  <body>
    <section class="presentation">

      <article>

        <div class="titre_logo_presentation">
          <br>
          <img src="images/<?php echo $info_acteur['acteur']; ?>.png" alt="Logo <?php echo $info_acteur['acteur']; ?>"/ class="logo"/>
          <br>
        </div>

        <?php echo nl2br( $info_acteur['description'] ); ?>

      </article>

    </section>

<?php
  if ( $affichage_commentaire )
  {
?>
  <section class="saisie_com">
    <form  method="post" action="presentation_acteur.php?acteur=<?php echo $id_acteur_choisi; ?>">

      <div>
        <label><strong>Ajouter un commentaire </strong>(vous ne pouvez donner votre avis qu'une seule fois!)</label>
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
<?php
  }
  else
  {
?>
  <section class="saisie_com">
      <div>
        <label><strong>Vous avez déjà donné votre avis ici.</strong></label>
      </div>
  </section>
<?php
  }
?>
  <section class="section_commentaires">
<?php
  if ( $_SESSION['admin'] === 1 )
  {
?>
  <form method='POST' action='presentation_acteur.php'>
<?php
    while ( !empty( ( $posts = $table_posts -> fetch() ) ) )
    {
?>
      <div class="commentaire">

        <div>
          <h4><?php echo $posts['username'];?></h4>
          <p><?php echo $posts['date_com']; ?></p>
          <h5><?php echo $posts['commentaire']; ?></h5>
        </div>
        <div>
          <input type="checkbox" name="delete[]" value=<?php echo $posts['id_post']; ?>/>
        </div>

      </div>
<?php
    }
?>
      <input type="submit" value="Valider">
  </form>
<?php
  }

  else
  {
    while ( !empty( ( $posts = $table_posts -> fetch() ) ) )
    {
?>
      <article class="commentaire">

          <h4><?php echo $posts['username'];?></h4>
          <p><?php echo $posts['date_com']; ?></p>
          <h4><?php echo $posts['commentaire']; ?></h4>

      </article>
<?php
    }
  }
?>
    </section>
  </body>
<?php
}

else
{
  redirectMainIfConnected();
}

include('footer.php');
