<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

if (isset($_POST["delete"])) {
  $pid = filter_input(INPUT_GET, 'pid', FILTER_SANITIZE_STRING);
  $ext = exec_sql_query($db, "SELECT ext FROM photos WHERE id  = :a;", array(':a' => $pid))->fetchAll();
  exec_sql_query($db, "DELETE FROM photos WHERE id = :a;", array(':a' => $pid));
  exec_sql_query($db, "DELETE FROM photos_tags_types WHERE photo_id = :a;", array(':a' => $pid));
  unlink("uploads/photos/" . $pid . "." . $ext[0]['ext']);
}

if (!isset($_GET['type'])) {
  header('Location: shop.php');
  exit();
}

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

$empty = FALSE;
$no_match = FALSE;
$order_feedback = array();

if (isset($_POST['search'])) {

  $search_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  $hidden_input = filter_input(INPUT_POST, 'hidden', FILTER_SANITIZE_STRING);

  if (empty($search_name)) {
    $empty = TRUE;
    $order_feedback[] = "You did not enter a product name. Please enter one in the search bar.";
  } else {

    $product_id = exec_sql_query($db, "SELECT product_type_id FROM photos WHERE name LIKE '%' || :search || '%';", array(':search' => $search_name))->fetchAll(PDO::FETCH_COLUMN);
    $product_idd = $product_id[0];

    $get_hidden_input_info = exec_sql_query($db, "SELECT id FROM product_types WHERE type = :a;", array(':a' => $hidden_input))->fetchAll(PDO::FETCH_COLUMN);
    $g_h_i_i = $get_hidden_input_info[0];

    if ($product_idd != $g_h_i_i) {
      $no_match = TRUE;
      $order_feedback[] = "No matches found for '" . $search_name . "' in the '" . $hidden_input . "' category. Please try another search.";
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

    <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/gallery.css" media="all" />

</head>

<body>
    <?php include("includes/nav.php");?>
    <main> <?php

    if (!($empty == FALSE && $no_match == FALSE && isset($_POST['search']))) { ?>
      <div class='back_button'><a class="button" href='shop.php'>← Back</a></div>
      <?php echo("<h1>" . $type . "</h1>"); ?>
      <h4 class = 'adjust_margin_sp'>Note: All prices are negotiable</h4>
    <?php } else { ?>
      <div class='back_button'><a class='button' href='gallery.php?type=<?php echo $type; ?>'>← Back</a></div>
      </div>
    <?php } ?>

    <?php
    if ($empty == TRUE) {
      display_messages($order_feedback, $no_match);
    }

    if ($no_match == TRUE) {
      display_messages($order_feedback, $empty);
    }

    if (($empty == TRUE || $no_match == TRUE && isset($_POST['search'])) ||
      ($empty == FALSE && $no_match == FALSE && !(isset($_POST['search'])))) {
    ?>
      <!-- Search box and price range tags -->
      <div class = "gallery2">
        <ul>
          <li>
            <form method="post">
              <h2 class = 'adjust_margin'>Product Name:</h2>
                <p>
                  <input type="text" placeholder="Enter Product Name" name="name"/>
                  <?php echo("<input type='hidden' name='hidden' value='" . $type . "'/>");?>
                  <button class="button" name="search" type="submit">Search</button>
                </p>
              </form>
            <!-- Source: (Original Work) Kyle Harms, Adapted from Lab 6 -->
          </li>
          <li>
          <h2 class = 'adjust_margin'>Price:</h2>
              <div class='tag_gallery'>
              <?php
              $price_range = exec_sql_query($db, "SELECT * FROM tags;")->fetchAll();

              foreach($price_range as $price_rang) {
                echo "<form class = 'adjust_margin' action='tag.php?tid=" . $price_rang['id'] . "&type=" .  $type . "' method='post'>
                <button name='tag_img_display' type='submit'>" . $price_rang['price_range'] . "</button></form>";
              }
              ?>
            </div>
          </li>
        </ul>
      </div>
            <?php } ?>

      <?php if ($empty == FALSE && $no_match == FALSE && isset($_POST['search'])) {
      echo("
      <h1>Search Results</h1>
        <div class = 'gallery'>
          <ul>");

          $search_type = strtolower($_GET['type']);
          $search_type_id = exec_sql_query($db, "SELECT id FROM product_types WHERE type_low = '$search_type';")->fetchAll(PDO::FETCH_COLUMN);
          $search_type_idd = $search_type_id[0];

          $product_name = exec_sql_query($db, "SELECT id, name, ext, price, product_type_id FROM photos
          WHERE product_type_id = '$search_type_idd'
          AND LOWER(REPLACE(name, ' ', ''))
          LIKE LOWER(REPLACE('%$search_name%', ' ', '')
          )
          ;")->fetchAll(PDO::FETCH_ASSOC);
          // $product_namee = $product_name[0];

            foreach ($product_name as $product_names) { ?>
              <?php
              $product_type = $product_names['product_type_id'];

              $prod_ty = exec_sql_query($db, "SELECT type_low FROM product_types WHERE id = '$product_type';")->fetchAll();
              $prod_typ = $prod_ty[0];

              echo "
              <li><a class = 'img_style' href='product.php?id=" . $product_names['id'] . "'><img alt='image' src=\"uploads/photos/" . $product_names['id'] . "." . $product_names['ext'] . "\" />";

              echo "<div class = 'desc'>
                <h2>" . $product_names['name'] . "</h2>
                <h2>" . $product_names['price'] . "</h2>
              </div>
            </a>
          </li>";
          } ?>
        </ul>
      </div>

    <?php } else { ?>
    <!-- Display Gallery -->
    <div class = "gallery">
      <ul>
        <?php
          $records = exec_sql_query($db, "SELECT photos.id, photos.ext, photos.price, photos.name, product_types.type_low FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE product_types.type = :a;", array(':a' => $type))->fetchAll(PDO::FETCH_ASSOC);

          if (!(empty($records))) {
            foreach($records as $record){
              // Source: Credit Mamta Harris (client)
              echo "<li><a class='img_style' href='product.php?id=" . $record['id'] . "'>
                <img alt='image' src=\"uploads/photos/" . $record['id'] . "." . $record['ext'] . "\" />";

                echo "<div class = 'desc'>
                  <h2 class = 'desc'>" . $record['name'] . "</h2>
                  <h2 class='price'>" . $record['price'] . "</h2>
                </div>
              </a></li>";
            }
        } else {
          echo("<h2 class = 'unfortunate_msg'>We're sorry. There are no " . $type . ".</h2>");
        }
        ?>
        </ul>
      </div>
        <?php } ?>
    </main>
  <?php include('includes/footer.php') ?>

</body>
</html>
