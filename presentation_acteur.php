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
    echo __LINE__;
    if ( isset($_POST['like']) )
    {
      echo __LINE__;
      $vote = 1;
      $stat_vote_request = 'UPDATE stat_votes SET likes = likes + 1 WHERE id_acteur= :id_acteur';
    }
    if ( isset($_POST['dislike']) )
    {
      echo __LINE__;
      $vote = -1;
      $stat_vote_request = 'UPDATE stat_votes SET dislikes = dislikes + 1 WHERE id_acteur= :id_acteur';
    }
    echo __LINE__;
    $inscription_vote = $bdd -> prepare('INSERT INTO vote(id_user,id_acteur, vote) VALUES (:id_user,:id_acteur,:vote)');

    $inscription_vote -> execute(
                                  [
                                    'id_user' => $_SESSION['id_user'],
                                    'id_acteur' => $id_acteur_choisi,
                                    'vote' => $vote
                                  ]
                                );
    try
    {
      $bdd -> beginTransaction();
      $update_stat_votes = $bdd -> prepare($stat_vote_request);
      $update_stat_votes -> execute ( ['id_acteur' => $id_acteur_choisi] );
      $bdd -> commit();
    }catch(Exception $e)
      {
        $pdo->rollback();

        echo $e -> getMessage();
        echo $e -> getCode();

        exit();
      }


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
    $exist_post = postAlreadyExists($bdd, $_SESSION['id_user'], $id_acteur_choisi);

    if ( !$exist_post )
    {
      $affichage_post = true;
    }
    else
    {
      $affichage_post = false;
    }

    //Récupération des avis dans la base de données
    $votes_request = $bdd -> prepare('SELECT likes, dislikes FROM stat_votes WHERE id_acteur= :id_acteur');
    $votes_request -> execute( ['id_acteur' => $id_acteur_choisi ] );
    $stat_votes = $votes_request -> fetch();

    $number_likes = $stat_votes[0];
    $number_dislikes = $stat_votes[1];

    //Vérification de l'existence d'un avis de l'utilisateur
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
          <label>Likes: <strong><?php echo $number_likes;?></strong></label> /
          <label>Dislikes: <strong><?php echo $number_dislikes;?></strong></label>
        </div>
    </section>

<?php
  if ( $affichage_post )
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
        <label><strong>Vous avez déjà écrit un commentaire.</strong></label>
      </div>
  </section>
<?php
  }

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
