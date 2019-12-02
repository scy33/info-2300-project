<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

// for error messages
$s_success= true;
$slide_feedback= array();
$slide_success= array();

function changeSliderImg($db, $slide_image, $id) {

  if (isset ($_FILES[$slide_image])){
    $id = (int)$id;
    global $slide_feedback;
    global $slide_success;

    $slide_upload_info=$_FILES[$slide_image];
    $slide_error=$slide_upload_info["error"];

    $slide_path = basename($slide_upload_info["name"]);
    $slide_upload_ext = strtolower(pathinfo($slide_path, PATHINFO_EXTENSION));


    if($slide_error == UPLOAD_ERR_OK){

         // remove old gallery image
        if(isset ($slide_path) && $slide_error == UPLOAD_ERR_OK){
          $sql_f = exec_sql_query($db, "SELECT file_ext FROM home_imgs WHERE id = :a;", array(':a' => $id));
          $sql_file= $sql_f->fetchAll();

          unlink("uploads/home_imgs/" . $id . "." . $sql_file[0]['file_ext']);

          //update db with new file
            if($sql_file){
            $img_replace_params = array(
              ":a" => $slide_upload_ext,
              ":b" => $id
            );

            $img_replace_sql= "UPDATE home_imgs SET file_ext = :a WHERE id = :b;";
            $replaced_image=exec_sql_query($db, $img_replace_sql, $img_replace_params);

              if($replaced_image){
                // move file path
                $new_path = "uploads/home_imgs/" . $id . "." . $slide_upload_ext;
                move_uploaded_file($_FILES[$slide_image]["tmp_name"], $new_path);
                $slide_success[]="The file was successfully added to the slideshow!";
              };
            };
          };
    } else if ($slide_error == UPLOAD_ERR_NO_FILE) {
      $slide_feedback[] = "Please upload a file.";
    } else if ($slide_error == UPLOAD_ERR_INI_SIZE){
      $slide_feedback[] = "The file is too large. Please upload an image that is less than 1MB.";
    } else{
      $slide_feedback[] = "There was an error in your file upload. Please try again with a different image.";
    };
  };
};

// Check the input is null first
$slide_image=NULL;
$slide_path=NULL;
$slide_upload_info=NULL;
$sql_f=NULL;
$replaced_image=NULL;

if ((isset($_POST["submit_slider1"])) ){
  changeSliderImg($db, "slide_image1", "1");
}

if ((isset($_POST["submit_slider2"])) ){
  changeSliderImg($db, "slide_image2", "2");
}

if ((isset($_POST["submit_slider3"])) ){
  changeSliderImg($db, "slide_image3", "3");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Home | Jewelry By Mamta</title>

  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />

</head>

<body>

<?php include("includes/nav.php");?>
<div class="main-container">


  <main>

<div class="big-block">
<div class="image-slideshow">
  <div class="slide-wrapper">
    <?php
    $params= array(
      ":slider_id1" => $slider_id1,
      ":slider_id2" => $slider_id2,
      ":slider_id3" => $slider_id3
    );

    $sql_slider="SELECT * FROM home_imgs;";
    $records=exec_sql_query($db, $sql_slider);
    $records = $records->fetchAll();

    foreach($records as $r){ ?>
      <!-- Source: Credit Mamta Harris (client) -->
      <div class="images-1">
        <img class="thumbnail_img slide-img" src="uploads/home_imgs/<?php echo $r['id'] . '.' . $r['file_ext']; ?>" alt="<?php echo $r['alt_text']; ?>" />
      </div>
    <?php }; ?>

  </div>
</div>
</div>

<?php
  const MAX_FILE_SIZE = 2000000;
  if (logged_in()){
?>

<h2 class="centered"> Change slider photos </h2>

<?php

display_messages($slide_feedback, !$s_success);

display_messages($slide_success, $s_success);

?>


<div class="row">
<?php
    $records=exec_sql_query($db, $sql_slider);
    foreach($records as $record){
 ?>
  <form class="slider-form" method="post" action="index.php" enctype="multipart/form-data" >
  <div class="column">
    <div class="imagesform">
        <div>
          <h3> Image <?php echo $record['id']; ?> </h3>
          <p class="small italic centered"> Current image shown below </p>
          <div class=cntr_img>
           <img src="uploads/home_imgs/<?php echo $record['id'] . "." . $record['file_ext'] ?>"  alt="<?php echo $record['alt_txt'] ?>" />
          </div>
        </div>

        <div class="form-entry">
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
          <label for="slide_image<?php echo $record['id'] ?>">Replace Image <?php echo $record['id'] ?>: </label>
          <input type="file" id="slide_image<?php echo $record['id'] ?>" name="slide_image<?php echo $record['id']?>"  />
        </div>
      </div>
      <div class="form-button slide-button">
            <button type="submit" name="submit_slider<?php echo $record['id']; ?>" class="button">Update Image <?php echo $record['id']; ?></button>
      </div></div>
  </form>
  <?php }; ?>
  </div>
<?php
  }
  if(!logged_in()) {
?>

<h1> Jewlery by Mamta </h1>
<p class="hometext centered">This website showcases some of my jewelry, made out of my home in Redwood, California. You can look at all of my items and browse and contact me if you are interested in any of them.
<?php echo $slide_file_name1; ?>

</p>

<p class="centered" > <a class="button" href="shop.php"> Shop Now! </a></p>

<?php } ?>

</main></div>

<?php include("includes/footer.php");?>




</body>
</html>
