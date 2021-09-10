<?php

session_start();

$_SESSION['page'] = 'modification';

include('header.php');
include('Admin/admin.php');

unset($_SESSION['page']);

$bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
              array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

if( isset($_SESSION['nom']) && isset($_SESSION['prenom']) )
{

  /*

  Si l'on envoie un formulaire (on modifie nos infos personnelles), alors on met
  à jour les données du profil dans la base de donneés.

  If a form is sent (we modify the personals infos), then we update the user
  profile in the database.

  */


  if ( !empty($_POST) )
  {
      $user_datas = array();

      foreach ( $_POST as $cle => $valeur )
      {
        if( !empty( $valeur ) )
        {
          $user_datas[$cle] = htmlspecialchars($valeur);
        }
      }

      /*
      echo '</pre>';
      print_r(array_keys($user_datas)[0]);
      echo '</pre>';
      */

      foreach ($user_datas as $cle => $valeur )
      {
        $sql_request = sprintf('UPDATE accounts SET %s=? WHERE id_user=?', $cle);
        if ($cle === 'password' || $cle === 'reponse' )
        {
          $valeur = password_hash($valeur, PASSWORD_DEFAULT);
          $modification = $bdd -> prepare($sql_request);
          $modification -> execute( array( $valeur, $_SESSION['id_user']) );
        }

        else
        {
          $modification = $bdd -> prepare($sql_request);
          $modification -> execute( array( $valeur, $_SESSION['id_user'] ) );
        }
      }

      $modification -> closeCursor();
      unset($user_datas);
  ?>
<body>
      <div id="titre_connexion">

        <h2>Vos informations ont bien été enregistrées.</h2>

      </div>
</body>
  <?php

      header("refresh:2;url=my_account.php");
      exit();

  }

  else
  {
    $request_infos = $bdd->prepare('SELECT nom, prenom, username, question FROM accounts WHERE id_user=?' );
    $request_infos -> execute( array( $_SESSION['id_user']  ) );
    $infos_user = $request_infos -> fetch();

  ?>
<body>
    <div id="titre_connexion">

      <h1>Vos informations personnelles</h1>
      <br>
      <h3>Si vous le souhaitez,vous pouvez saisir les nouvelles informations à prendre en compte ci-dessous:</h3>

    </div>

    <div id="page_connexion">

      <form  method="post" action="my_account.php">

          <div class="champs_connexion">
              <label><strong>Nom</strong></label>
              <input type=text name=nom
              placeholder="<?php echo $infos_user['nom']; ?>"/>
          </div>

          <div class="champs_connexion">
            <label><strong>Prénom</strong></label>
            <input type=text name=prenom
            placeholder="<?php echo $infos_user['prenom']; ?>"/>
          </div>

          <div class="champs_connexion">
            <label><strong>Nom d'utilisateur</strong></label>
            <input type=text name=username
            placeholder="<?php echo $infos_user['username']; ?>"/>
          </div>

          <div class="champs_connexion">
            <label><strong>Mot de passe</strong></label>
            <input type=password name=password />
          </div>

          <div class="champs_connexion">
            <label><strong>Question secrète</strong></label>
            <input type=text name=question
            placeholder="<?php echo $infos_user['question']; ?>"/>
          </div>

          <div class="champs_connexion">
            <label><strong>Réponse à la réponse secrète</strong></label>
            <input type=password name=reponse />
          </div>

          <div class="champs_connexion">
            <input type=submit placeholder="Valider"/>
          </div>

        </form>

    </div>
</body>
  <?php
  }
}

else
{
?>
<body>
  <div id="titre_connexion">

    <h1>Vous devez vous connecter pour accéder à cette page</h1>
    <br><br><br>
    <h2>Vous allez être redirigé vers la page d'accueil.</h2>

  </div>
</body>
<?php
    header('refresh:3;url=index.php');
}

include('footer.php');

?>
