<?php

session_start();

$_SESSION['page'] = 'modification';

include('header.php');
include('functions.php');

unset($_SESSION['page']);

$bdd = connexionBDD();

if( isset($_SESSION['nom']) && isset($_SESSION['prenom']) )
{

  if ( empty($_POST) )
  {
    $request_infos = $bdd->prepare('SELECT nom, prenom, username, question FROM accounts WHERE id_user=:id_user LIMIT 0,1' );
    $request_infos -> execute( array( 'id_user' => $_SESSION['id_user']  ) );
    $infos_user = $request_infos -> fetch();

    $avatar_name = "user_photo_".strval ($_SESSION['id_user']);

    $_SESSION['my_account'] = 'infos';
  }

  else
  {
    $user_datas['nom'] = htmlspecialchars( $_POST['nom'] );
    $user_datas['prenom'] = htmlspecialchars( $_POST['prenom'] );
    $user_datas['username'] = htmlspecialchars( $_POST['username'] );
    $user_datas['password'] = password_hash( htmlspecialchars( $_POST['password'] ), PASSWORD_DEFAULT);
    $user_datas['question'] = htmlspecialchars( $_POST['question'] );
    $user_datas['reponse'] = password_hash( htmlspecialchars( $_POST['reponse'] ), PASSWORD_DEFAULT);

    foreach ($user_datas as $cle => $valeur )
    {
      if ( $valeur != null )
      {
        $sql_request = sprintf('UPDATE accounts SET %s=:value WHERE id_user=:id_user', $cle);
        $valeur = password_hash($valeur, PASSWORD_DEFAULT);
        $modification = $bdd -> prepare($sql_request);
        $modification -> execute( array( 'value' => $valeur, 'id_user' => $_SESSION['id_user']) );
      }
    }

    if ( isset($_FILES[$avatar_name]) AND $_FILES[$avatar_name]['error'] === 0)
    {
      if ($_FILES[$avatar_name]['size'] <= 8000000)
      {
        $infosfichier = pathinfo($_FILES[$avatar_name]['name']);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
        if (in_array($extension_upload, $extensions_autorisees))
        {
          move_uploaded_file($_FILES[$avatar_name]['tmp_name'], 'uploads/' . basename($_FILES[$avatar_name]['name']));
        }
      }
    }

    $modification -> closeCursor();
    $user_datas = array();

    $_SESSION['my_account'] = 'modifie';
  }
}

else
{
  $_SESSION['my_account'] = 'deconnecte';
}

//Affichage de la page

if ( $_SESSION['my_account'] === 'infos' )
{
  unset( $_SESSION['my_account'] );
?>
<body>

  <div id="titre_connexion">
    <h1>Vos informations personnelles</h1>
    <br>
    <h3>Si vous le souhaitez,vous pouvez saisir les nouvelles informations à prendre en compte ci-dessous:</h3>
  </div>

  <div id="page_connexion">
    <div>
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
      <br>
      <form action="my_account.php" method="post" enctype="multipart/form-data">
        <div class="champs_connexion">
            <label><strong>Insérer votre avatar :</strong></label><br/>
            <input type="file" name=<?php echo $avatar_name; ?>/><br/>
            <input type="submit" value="Uploader" />
        </div>
    </form>
    </div>
  </div>

</body>
<?php
  include('footer.php');
  exit;
}

if ( $_SESSION['my_account'] === 'modifie' )
{
?>
  <body>
        <div id="titre_connexion">

          <h2>Vos informations ont bien été enregistrées.</h2>

        </div>
  </body>
<?php
  include('footer.php');
  sleep(3);
  header('Location:my_account.php');
  exit();
}

if ( $_SESSION['my_account'] === 'deconnecte' )
{
  unset( $_SESSION['my_account'] );
?>
  <body>
    <div id="titre_connexion">

      <h1>Vous devez vous connecter pour accéder à cette page</h1>
      <br><br><br>
      <h2>Vous allez être redirigé vers la page d'accueil.</h2>

    </div>
  </body>
<?php
  include('footer.php');
  sleep(3);
  header('Location:index.php');
  exit();
}
