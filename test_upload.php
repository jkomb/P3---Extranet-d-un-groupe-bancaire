<?php
session_start();
$max_file_size_bytes = 7000000;
var_dump($_FILES,$_POST,$_GET,$_SESSION,$_ENV);


if ( !empty($_FILES['image']) )
{
  if ( is_uploaded_file($_FILES['image']['tmp_name'] ) === false )
  {
    echo '1:Error on upload: Invalid file definition';
    exit;
  }

  if ( $_FILES['image']['size'] > $max_file_size_bytes )
  {
    echo '2:Exceeded file size limit.';
    exit;
  }

  $file_mimetype = mime_content_type($_FILES['image']['tmp_name']);
  $authorized_mime_types = array( 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');

  $verify_mime_type = array_search( $file_mimetype, $authorized_mime_types );


  if ( $verify_mime_type === false )
  {
      echo '3:Invalid file format.';
      exit;
  }

  $uploadName = $_FILES['image']['name'];
  $extension = strtolower(substr($uploadName, strripos($uploadName, '.')+1));
  $filename = hash_file('sha256', $_FILES['image']['tmp_name']) . '.' . $extension;

  if ( !move_uploaded_file( $_FILES['image']['tmp_name'], './uploads/'.$filename ) )
  {
    //TODO checker toutes les erreurs que renvoie move_uploaded_file
  }

  $user_entries['avatar'] = $filename;
  $_SESSION['avatar'] = $filename;

}

?>

<form action="test_upload.php" method="post" enctype="multipart/form-data">

  <div class="champs_connexion">
      <label><strong>Insérer votre avatar :</strong></label><br/>
      <input type="file" name="image"/><br/>
      <input type="submit" value="Importer" />
  </div>

</form>
