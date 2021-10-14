<?php

include('functions.php');

session_start();

redirectIndexIfNotConnected();

$page = 'presentation_acteur';

$admin = false;

$bdd = connexionBDD();

if ( isset($_GET['acteur']) )
{
  $id_acteur_choisi = htmlspecialchars($_GET['acteur']);

  //Récuépration des infos sur l'acteur
  $acteur_request = <<<SQL
  SELECT nom_acteur, description, total_likes, total_dislikes, total_posts
    FROM acteurs
      WHERE id_acteur=:id_acteur
        LIMIT 0,1
  SQL;
  $table_acteur = $bdd -> prepare($acteur_request);
  $table_acteur -> execute( array( 'id_acteur' => $id_acteur_choisi ) );
  $info_acteur = $table_acteur -> fetch();

  if ( !empty($info_acteur) )
  {
    $exists_actor= true;

    //Récupération des commentaires sur l'acteur
    $posts_request = <<<SQL
    SELECT accounts.username username, posts.post commentaire, posts.date_add date_com, posts.id_post id_post
      FROM posts
        INNER JOIN accounts
          ON accounts.id_user = posts.id_user
            WHERE posts.id_acteur = :id_acteur
              ORDER BY date_com
                DESC LIMIT 0, 5
    SQL;
    $table_posts = $bdd -> prepare( $posts_request );
    $table_posts -> execute( array( 'id_acteur' => $id_acteur_choisi ) );

    //Récupération du nombre de commentaires sur l'acteur
    $number_posts = intval( $info_acteur['total_posts'] );

    //Vérification de l'existence d'un commentaire de l'utilisateur connecté
    $exist_post = postAlreadyExists($bdd, $_SESSION['id_user'], $id_acteur_choisi);

    if ( !$exist_post )
    {
      $affichage_post = true;
    }
    else
    {
      $affichage_post = false;
    }

    //Vérification de la qualité d'administrateur de l'utilisateur connecté
    $admin = isAdmin($bdd, $_SESSION['id_user']);

    //Récupération du nombre de votes dans la base de données
    $number_likes = intval( $info_acteur['total_likes'] );
    $number_dislikes = intval( $info_acteur['total_dislikes'] );

    //Vérification de l'existence d'un avis de l'utilisateur connecté
    $exist_vote = voteAlreadyExists($bdd, $_SESSION['id_user'], $id_acteur_choisi);

    if ( !$exist_vote )
    {
      $affichage_vote = true;
    }
    else
    {
      $affichage_vote = false;
    }

  }

  else
  {
    $exists_actor = false;
  }

  //Enregitrement d'un nouveau commentaire
  if ( isset($_POST['post']) && !empty($_POST['post']) )
  {
    $post = htmlspecialchars( $_POST['post'] );

    try
    {
      $bdd -> beginTransaction();
      $update_stat_posts = $bdd -> prepare('UPDATE acteurs SET total_posts = total_posts + 1 WHERE id_acteur= :id_acteur');
      $update_stat_posts -> execute ( ['id_acteur' => $id_acteur_choisi] );
      $inscription_post = $bdd -> prepare('INSERT INTO posts(id_user,id_acteur, post) VALUES (:id_user,:id_acteur,:post)');
      $inscription_post -> execute(
                                    array(
                                          'id_user' => $_SESSION['id_user'],
                                          'id_acteur' => $id_acteur_choisi,
                                          'post' => $post
                                        )
                                  );
      $bdd -> commit();
    }catch(Exception $e)
      {
        $bdd ->rollback();

        // émettre un message d'erreur à l'utilisateur lui disant que son vote n'a
        // pas pu être pris en compte et qu'il devra réessayer ultérieurement
        echo $e -> getMessage();
        echo $e -> getCode();

        exit();
      }

    $update_stat_posts -> closeCursor();
    $inscription_post -> closeCursor();

    header('Location: presentation_acteur.php?acteur='.$id_acteur_choisi);
    exit;
  }

  //Enregistrement d'un nouvel avis
  if ( isset($_POST['like']) || isset($_POST['dislike']) )
  {
    if ( isset($_POST['like']) )
    {
      $vote = 1;
      $stat_vote_request = 'UPDATE acteurs SET total_likes = total_likes + 1 WHERE id_acteur= :id_acteur';
    }
    if ( isset($_POST['dislike']) )
    {
      $vote = -1;
      $stat_vote_request = 'UPDATE acteurs SET total_dislikes = total_dislikes + 1 WHERE id_acteur= :id_acteur';
    }

    try
    {
      $bdd -> beginTransaction();
      $update_stat_votes = $bdd -> prepare($stat_vote_request);
      $update_stat_votes -> execute ( ['id_acteur' => $id_acteur_choisi] );
      $inscription_vote = $bdd -> prepare('INSERT INTO vote(id_user,id_acteur, vote) VALUES (:id_user,:id_acteur,:vote)');
      $inscription_vote -> execute(
                                    [
                                      'id_user' => $_SESSION['id_user'],
                                      'id_acteur' => $id_acteur_choisi,
                                      'vote' => $vote
                                    ]
                                  );
      $bdd -> commit();
    }catch(Exception $e)
      {
        $bdd ->rollback();

        // émettre un message d'erreur à l'utilisateur lui disant que son vote n'a
        // pas pu être pris en compte et qu'il devra réessayer ultérieurement
        echo $e -> getMessage();
        echo $e -> getCode();

        exit();
      }

    $update_stat_votes -> closeCursor();
    $inscription_vote -> closeCursor();

    header('Location: presentation_acteur.php?acteur='.$id_acteur_choisi);
    exit;
  }

  if( isset($_POST['delete']) && !empty($_POST['delete']) )
  {
    $delete_request = 'DELETE from posts WHERE ';
    $list_posts_delete = [];
    foreach($_POST['delete'] as $val)
    {
      array_push( $list_posts_delete, sprintf( 'id_post=%s', $val ) );
    }
    $delete_request .= implode( " AND ", $list_posts_delete );

    $number_posts_delete = count($list_posts_delete);
    $update_stat_posts_request = sprintf( 'UPDATE acteurs SET total_posts = total_posts - %s WHERE id_acteur= :id_acteur',
                                          $number_posts_delete );

    try
    {
      $bdd -> beginTransaction();
      $delete_posts = $bdd -> exec($delete_request);
      $update_stat_posts = $bdd -> prepare($update_stat_posts_request);
      $update_stat_posts -> execute ( ['id_acteur' => $id_acteur_choisi] );
      $bdd -> commit();
    }catch(Exception $e)
      {
        $bdd ->rollback();

        // émettre un message d'erreur à l'utilisateur lui disant que son vote n'a
        // pas pu être pris en compte et qu'il devra réessayer ultérieurement
        echo $e -> getMessage();
        echo $e -> getCode();

        exit();
      }


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
        <img src="images/<?php echo $info_acteur['nom_acteur']; ?>.png" alt="Logo <?php echo $info_acteur['nom_acteur']; ?>"/ class="logo"/>
        <br>
      </div>

      <?php echo nl2br( $info_acteur['description'] ); ?>

    </article>

  </section>

  <section class="interaction_utilisateur">

    <section class="saisie_com">
        <div>
          <label><strong><?php echo $number_posts;?></strong> Commentaires</label>
        </div>
    </section>

<?php
  if ( $affichage_post )
  {
?>
    <div class="saisie_com">
      <form  method="post" action="presentation_acteur.php?acteur=<?php echo $id_acteur_choisi; ?>">

        <div>
          <label><strong>Ajouter un commentaire </strong>(vous ne pouvez mettre un commentaire qu'une seule fois!)</label>
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
        <label><strong>Vous avez déjà écrit un commentaire.</strong></label>
      </div>
  </section>
<?php
  }

?>

  <section class="saisie_com">
      <div>
        <label>Likes: <strong><?php echo $number_likes;?></strong></label> /
        <label>Dislikes: <strong><?php echo $number_dislikes;?></strong></label>
      </div>
  </section>

<?php
  if ( $affichage_vote )
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
          <label><strong>Vous avez déjà donné voté ici.</strong></label>
        </div>
    </section>
<?php
  }

?>
  </section>

  <section class="section_commentaires">
<?php
  if ( $admin && ($number_posts != 0) )
  {
?>
  <form method="POST" action="presentation_acteur.php?acteur=<?php echo $id_acteur_choisi; ?>">
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
          <input type="checkbox" name="delete[]" value=<?php echo intval($posts['id_post']);?> />
        </div>

      </div>
      <br>
<?php
    }
?>
      <input type="submit" value="Supprimer les commentaires sélectionnés">
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
