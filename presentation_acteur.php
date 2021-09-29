<?php

include_once('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'presentation_acteur';

$bdd = connexionBDD();

if ( isset($_GET['acteur']) )
{
  $id_acteur_choisi = htmlspecialchars($_GET['acteur']);


  //Enregitrement d'un nouveau commentaire
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

  //Enregistrement d'un nouvel avis
  if ( isset($_POST['like']) || isset($_POST['dislike']) )
  {
    if ( isset($_POST['like']) )
    {
      $vote = 1;
    }
    if ( isset($_POST['dislike']) )
    {
      $vote = -1;
    }
    $inscription_vote = $bdd -> prepare('INSERT INTO vote(id_user,id_acteur, vote) VALUES (:id_user,:id_acteur,:vote)');

    $inscription_vote -> execute(
                                  array(
                                        'id_user' => $_SESSION['id_user'],
                                        'id_acteur' => $id_acteur_choisi,
                                        'vote' => $vote
                                      )
                                );

    $inscription_vote -> closeCursor();

    header('Location: presentation_acteur.php?acteur='.$id_acteur_choisi);
    exit;
  }

  //Récuépration des infos sur l'acteur
  $acteur_request = $bdd -> prepare('SELECT acteur, description FROM acteurs WHERE id_acteur=:id_acteur LIMIT 0,1');
  $acteur_request -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
  $info_acteur = $acteur_request -> fetch();

  if ( !empty($info_acteur) )
  {
    $exists_actor= true;

    //Récupération des commentaires sur l'acteur présents dans la base de données
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

    //Vérification de l'existence d'un commentaire de l'utilisateur
    $unique_post_request = <<<SQL
    SELECT id_user
      FROM accounts
        WHERE EXISTS (
          SELECT TRUE
            FROM posts
              WHERE accounts.id_user = posts.id_user AND posts.id_acteur = :id_acteur)
    SQL;

    $verify_unique_post = $bdd -> prepare( $unique_post_request );
    $verify_unique_post -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    $info_post = $verify_unique_post -> fetch();

    //var_dump($info_post);

    if ( !in_array( $_SESSION['id_user'], $info_post ) )
    {
      $affichage_commentaire = true;
    }
    else
    {
      $affichage_commentaire = false;
    }

    //Récupération des avis dans la base de données
    $like_request = $bdd -> prepare('SELECT COUNT(*) FROM vote WHERE id_acteur=:id_acteur AND vote=1');
    $like_request -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    $nombre_likes = $like_request -> fetch();

    $dislike_request = $bdd -> prepare('SELECT COUNT(*) FROM vote WHERE id_acteur=:id_acteur AND vote=-1');
    $dislike_request -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    $nombre_dislikes = $dislike_request -> fetch();

    //Vérification de l'existence d'un avis de l'utilisateur
    $unique_vote_request = <<<SQL
    SELECT id_user
      FROM accounts
        WHERE EXISTS (
          SELECT TRUE
            FROM vote
              WHERE accounts.id_user = vote.id_user AND vote.id_acteur = :id_acteur)
    SQL;

    $verify_unique_vote = $bdd -> prepare( $unique_vote_request );
    $verify_unique_vote -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
    $info_vote = $verify_unique_vote -> fetch();

    //var_dump($info_vote);

    if ( !in_array( $_SESSION['id_user'], $info_vote ) )
    {
      $affichage_like = true;
    }
    else
    {
      $affichage_like = false;
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

  <section class="interaction_utilisateur">
    <section class="saisie_com">
        <div>
          <label>Likes: <strong><?php echo $nombre_likes[0];?></strong></label> /
          <label>Dislikes: <strong><?php echo $nombre_dislikes[0];?></strong></label>
        </div>
    </section>

<?php
  if ( $affichage_commentaire )
  {
?>
    <div class="saisie_com">
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
    </div>
<?php
  }
  else
  {
?>
  <section class="saisie_com">
      <div>
        <label><strong>Vous avez déjà donné écrit un commentaire.</strong></label>
      </div>
  </section>
<?php
  }

  if ( $affichage_like )
  {
?>
    <div class="saisie_com">
      <form  method="post" action="presentation_acteur.php?acteur=<?php echo $id_acteur_choisi; ?>">

        <button type='submit' name='like'>J'aime</button>
        <button type='submit' name='dislike'>Je n'aime pas</button>

      </form>
    </div>
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
  </section>

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
