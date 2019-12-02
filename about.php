<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

$success = true;
$about_success = array();
$about_feedback = array();

$about_sql = "SELECT * FROM about;";
$about_params = array();
$about_records = exec_sql_query($db, $about_sql, $about_params)->fetchAll();
$about = $about_records[0];

$about_ext = $about['ext'];
$about_name = $about['name'];
$about_opening = $about['opening'];
$about_description = $about['blurb'];

if(isset($_POST["about_name"])) {
  $about_name = $_POST["about_name"];
  if(!set($about_name)) {
      $about_feedback[] = "Please enter a name.";
  }
}

if(isset($_POST["about_opening"])) {
  $about_opening = $_POST["about_opening"];
  if(!set($about_opening)) {
      $about_feedback[] = "Please enter a greeting.";
  }
}

if(isset($_POST["about_description"])) {
  $about_description = $_POST["about_description"];
  if(!set($about_description)) {
      $about_feedback[] = "Please enter a description.";
  }
}

if (isset($_POST["submit_about"]) && set($about_name) && set($about_opening) && set($about_description)) {
  $db->beginTransaction();
  $about_info = $_FILES["about_image"];
  $about_error = $about_info["error"];
  if($about_error == UPLOAD_ERR_OK || $about_error == UPLOAD_ERR_NO_FILE) {
      if($about_error == UPLOAD_ERR_OK) {
          unlink("uploads/about/1." .$about_ext);
          $about_ext = strtolower(pathinfo(basename($about_info["name"]), PATHINFO_EXTENSION));
          $new_path = "uploads/about/1." .$about_ext;
          move_uploaded_file( $about_info["tmp_name"], $new_path);
      }
          $sql = "UPDATE about SET ext=:file_ext, name=:name, opening=:opening, blurb=:description WHERE id=1";
          $params = array(
          ':file_ext' => $about_ext,
          ':name' => $about_name,
          ':opening' => $about_opening,
          ':description' => $about_description);
          $result = exec_sql_query($db, $sql, $params);
          if($result) {
              $about_success[] = "Update Successful.";
          } else {
              $about_feedback[] = "File Error occured while inserting into the database. Please try updating the about again.";
          }

  } else if ($about_error == UPLOAD_ERR_FORM_SIZE) {
      $about_feedback[] = "File Error occured because the size of the file exceeds the maximum file size. Please update a file that is less than or equal to 1 MB.";
  } else if ($about_error == UPLOAD_ERR_NO_FILE) {

  } else {
      $about_feedback[] = "File Error occured while updating.";
  }

  $db->commit();
}


const MAX_FILE_SIZE = 1000000;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>About | Jewelry By Mamta</title>

  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

</head>

<body>
<?php include("includes/nav.php");?>
<div class="main-container">
<main>
  <h1 class="decrease_margin">About Me</h1>
  <div class="about">
    <!-- Source: Credit Mamta Harris (client) -->
    <img class="about-picture" src=<?php echo '"uploads/about/1.' .$about_ext .'"' ?> alt= <?php echo '"Picture of ' .$about_name .'"'; ?>>
    <div class="about-intro">
      <h2 class="left-align"><?php echo $about_opening; ?></h2>
      <p><?php echo $about_description; ?></p>
    </div>
  </div>

  <?php if(!logged_in()) { ?>
  <p class="centered_about_button"><a class="button" href="contact.php">Contact Me!</a></p>
  <?php
  }

    if(logged_in()) {
      display_messages($about_feedback, !$success);
      display_messages($about_success, $success);
      ?>
      <h2>Edit My About</h2>

      <form id="about-form" method="post" action="about.php" enctype="multipart/form-data">
        <div class="form-entry">
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
          <label for="about_image">About Image: </label>
          <input type="file" id="about_image" name="about_image" />
        </div>

        <div class="form-entry">
            <label for="about_name">Name* </label>
            <input type="text" id="about_name" name="about_name" placeholder="Name" value="<?php echo htmlspecialchars($about_name); ?>" />
        </div>

        <div class="form-entry">
            <label for="about_opening">Greeting* </label>
            <input type="text" id="about_opening" name="about_opening" placeholder="Greeting" value="<?php echo htmlspecialchars($about_opening); ?>" />
        </div>

        <div class="form-entry">
            <label for="about_description">Description*: </label>
            <textarea id="about_description" name="about_description" placeholder="Description"><?php echo htmlspecialchars($about_description); ?></textarea>
        </div>

        <div class="form-button">
            <button type="submit" name="submit_about" class="button">Edit About</button>
        </div>
    </form>



    <?php }
  ?>
</main></div>

  <?php include("includes/footer.php"); ?>

</body>
</html>
