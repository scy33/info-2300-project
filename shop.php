<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");


$empty = FALSE;
$no_match = FALSE;
$order_feedback = array();

// Checking if the add album button is pressed
if (isset($_POST['submit_album'])) {

  $album_name = filter_input(INPUT_POST, 'album_name', FILTER_SANITIZE_STRING);

  if (empty($album_name)) {
    $empty = TRUE;
    $order_feedback[] = "Please enter an album name.";
  } else {

  $upload_info = $_FILES["upload_image"];

  // Check if there is an error in the upload process
  if ($upload_info['error'] == UPLOAD_ERR_OK){

   $name = basename($upload_info["name"]);
   $upload_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

   $sql = "INSERT INTO product_types (type, type_low, ext) VALUES (:type, :type_lower, :ext);";
   $params = array(
     ':type' => ucfirst(strtolower($album_name)),
     ':type_lower' => strtolower($album_name),
     ':ext' => $upload_ext
     );

   $result = exec_sql_query($db, $sql, $params);
   if ($result) {
     $curr_id = $db -> lastInsertId("id");
     $new_path = 'uploads/album_imgs/default_' . strtolower($album_name) . '.' . $upload_ext;
     move_uploaded_file($upload_info["tmp_name"], $new_path);
     $create_success = TRUE;
   }
   }
 }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Shop | Jewelry By Mamta</title>

  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/gallery.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />

</head>

<body>
<?php include("includes/nav.php"); ?>

<div class="main-container"><main>

  <!-- Albums -->
  <h1 class = 'no_pad'>Explore by Jewelry Type</h1>
  <?php if ($create_success) { ?>
    <div class="success"><p><strong>Success!</strong> Your new album was successfully created. It will not display here until products are added to it, however.</p></div>
  <?php } ?>
  <div class = "albums">


<?php
// Gets count of each item
    $get = exec_sql_query($db, "SELECT DISTINCT product_type_id FROM photos;")->fetchAll(PDO::FETCH_COLUMN);

    echo '<ul>';
    foreach ($get as $gets) {
      $get_stuff = exec_sql_query($db, "SELECT photos_tags_types.photo_id, photos_tags_types.prod_type_id, photos.ext, photos.name, photos.price, photos.description, product_types.type, product_types.type_low FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE product_types.id = :a;", array(':a' => $gets))->fetchAll();
      $get_stuf = $get_stuff[0];
      $get_stuf_id = $get_stuf['photo_id'];
      $get_stuf_prod_id = $get_stuf['prod_type_id'];

      $distinct = exec_sql_query($db, "SELECT COUNT(*) FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE product_type_id = :a;", array(':a' => $get_stuf_prod_id))->fetchAll(PDO::FETCH_COLUMN);
      $count = $distinct[0];

      // Source: Credit Mamta Harris (client)
      echo(
        "<li>
            <a class='remove-link-dec' href='gallery.php?type=" . $get_stuf['type'] . "'><img class='album_img' alt=".$get_stuf['type']." src=\"uploads/photos/" . $get_stuf_id . '.' . $get_stuf['ext'] . "\" />
            <div class = 'labels'>"
               . $get_stuf['type'] . "" . " | <span class='smaller'>" . $count . " items </span></div></a>
        </li>");

    } ?>
      </ul>
      </div>


    <?php if (logged_in()) { ?>
      <h2 class='subheading pad_top'>Add Album</h2>
      <form id="upload-form" method="post" action="shop.php" enctype="multipart/form-data">
          <div class="form-entry">
              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
              <label for="upload_image">Album Image*: </label>
              <input id="upload_image" type="file" name="upload_image" required />
          </div>
          <div class="form-entry">
            <label for="album_name">Album Name*: </label>
            <input id="album_name" type="text" name="album_name" placeholder="Album Name" required />
        </div>
          <div class="form-button">
            <button type="submit" name="submit_album" class="submit">Add Album</button>
          </div>
        </form>
      <?php } ?>

</main></div>
<?php include("includes/footer.php"); ?>


</body>
</html>
