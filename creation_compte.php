<?php

  session_start();
  $_SESSION['page']='creation';

  include('header.php');

  unset($_SESSION['page']);


/*
2.

Lorsque l'utilisateur a oublié son mot de passe, on lui demande de saisir son
nom d'utilisateur.

When the user forgot his password, we ask him to type its user name.
*/
  if(isset($_GET['mdp']))
  {
    $_GET['mdp']=htmlspecialchars($_GET['mdp']);

    if($_GET['mdp']=='oublie')
    {
      echo'

      <div id="titre_connexion">

        <h1>Veuillez renseigner votre nom d\'utilisateur</h1>
        <br>

      </div>

      <div id="page_connexion">

          <form  method="post" action="creation_compte.php">

            <div class="champs_connexion">
              <label><strong>Merci de saisir le nom d\'utilisateur défini lors de la création de votre compte</strong></label></br>
              <input type=text name=user_name autofocus required/>
            </div>

            <div class="champs_connexion">
              <input type=submit value="Valider"/>
            </div>
          </form>

      </div>';
      }
  }

/*
3.

On demande ensuite à ce qu'il réponde à sa question secrète.

We then ask him to answer his secret question.
*/
  elseif(isset($_POST['user_name'])&&!isset($_POST['name']))
  {
    $POST['user_name']=htmlspecialchars($_POST['user_name']);

    /*
    tester si le nom d'utilisateur est bien connu sinon renvoyer en arrière
    et préciser que le nom de l"utilisateur est inconnu

    récupérer la question secète dans la variable $_SESSION['secret_question']
    récupérer la réponse secrète dans la variable $_SESSION['secret_answer']
    */

    echo'

    <div id="titre_connexion">

      <h1>Veuillez répondre à votre question secrète</h1>
      <br>

    </div>

    <div id="page_connexion">

        <form  method="post" action="creation_compte.php">

          <div class="champs_connexion">
            <label><strong>"'./*.$_SESSION['secret_question'].*/'"</strong></label></br>
            <input type=text name=secret_answer  autofocus required/>
          </div>

          <div class="champs_connexion">
              <input type=submit value="Valider"/>
          </div>
        </form>

    </div>';
  }

/*
4.

On demande enfin à l'utilisateur de saisir un nouveau mot de passe, 2 fois.

Finally, we ask the user to type a new password, twice.
*/
  elseif (isset($_POST['secret_answer'])&&!isset($_POST['name']))
  {
    $POST['secret_answer']=htmlspecialchars($_POST['secret_answer']);

    /*

    if($POST['secret_answer']==$_SESSION['secret_question'])
    {

    */
    echo'

    <div id="titre_connexion">

      <h1>Veuillez saisir votre nouveau mot de passe</h1>
      <br>

    </div>

    <div id="page_connexion">

        <form  method="post" action="creation_compte.php">

          <div class="champs_connexion">
            <label><strong>Nouveau mot de passe</strong></label></br>
            <input type=password name=password autofocus required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Veuillez saisir votre nouveau </br> mot de passe à nouveau</strong></label></br>
            <input type=password name=passwordbis required/>
          </div>

          <div class="champs_connexion">
              <input type=submit value="Valider"/>
          </div>
        </form>

    </div>';
    //}

  }

/*
5.

Que les 2 saisies de mot de passe soient identiques (le nouveau mot de passe
est enregistré) ou non (reprise du processus à zéro), l'utilisateur est dans toutes
les cas renvoyé à la page d'accueil.

Wether the two typings are identical (the new password is saved) or not (starting
the whole process from scratch), the user is sent back to the welcome page.
*/
  elseif (isset($_POST['password'])&&isset($_POST['passwordbis']))
  {
    $POST['password']=htmlspecialchars($_POST['password']);
    $POST['passwordbis']=htmlspecialchars($_POST['passwordbis']);

    /*
    Mettre à jour le mot de passe dans la BDD
    */
    if($POST['password']==$POST['passwordbis'])
    {
      echo'

      <div id="titre_connexion">

        <h2>Vos modifications ont bien été prises en compte</h2>
        <br><br>
        <p>Vous allez être redirigé vers la page d\'accueil</p>

      </div>';

      header("refresh:2;url=accueil.php");
    }

    if($POST['password']!=$POST['passwordbis'])
    {
      echo'

      <div id="titre_connexion">

        <h2>Vos modifications n\'ont PAS été prises en compte!</h2>
        <br><br>
        <p><strong>Les 2 saisies de votre nouveau mot de passe ne sont pas identiques!</strong></p>
        <p>Vous allez être redirigé vers la page d\'accueil</p>

      </div>';

      header("refresh:5;url=accueil.php");
    }

  }

/*
6.

Lorsque la saisie des informations personnelles du compte a été un succès, on
les enregistre dans la base de données et on redirige l'utilisateur
vers l'accueil.

When the user has successfully typed all his personal informations, we save them
in the database and we redirect him to the welcome page.
*/
  elseif (isset($_POST['nom']))
  {

      $bdd = new PDO('mysql:host=localhost;dbname=extranet;charset=utf8', 'root', '',
               array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

      foreach($_POST as $element)
      {
        $element = htmlspecialchars($element);
      }

      $inscription = $bdd->prepare('INSERT INTO accounts(nom,prenom,username,password,question,reponse)
                     VALUES(UPPER(:nom),:prenom,:username,:password,:question,:reponse)');
      $inscription->execute($_POST);

      //$nom_majuscule = $bdd->prepare('UPDATE accounts SET nom=UPPER(nom) WHERE')

      $inscription->closeCursor();

      echo'
      <div id="titre_connexion">

        <h2>Vos informations ont bien été enregistrées.</h2>
        <br><br>
        <p>Vous allez être redirigé vers la page d\'accueil</p>

      </div>';

      header("refresh:2;url=accueil.php");

  }


/*
1.

Lorsqu'un nouvel utilisateur souhaite créer un compte, on lui demande toutes
les informations requises.

When a new user wants to create a account, we ask him all the needed
informations.
*/
  else
  {
    echo'

    <div id="titre_connexion">

      <h1>Veuillez renseigner vos données personnelles</h1>
      <br>

    </div>

    <div id="page_connexion">

        <form  method="post" action="creation_compte.php">

          <div class="champs_connexion">
            <label><strong>Nom</strong></label>
            <input type=text name=nom autofocus required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Prénom</strong></label>
            <input type=text name=prenom required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Nom d\'utilisateur</strong></label>
            <input type=text name=username required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Mot de passe</strong></label>
            <input type=password name=password required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Question secrète</strong></label>
            <input type=text name=question required/>
          </div>

          <div class="champs_connexion">
            <label><strong>Réponse à la réponse secrète</strong></label>
            <input type=password name=reponse required/>
          </div>

          <div class="champs_connexion">
            <input type=submit value="Valider"/>
          </div>
        </form>

    </div>';
  }

  include('footer.php');
 ?>
