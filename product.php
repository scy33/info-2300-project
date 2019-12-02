<?php
include("includes/init.php");
include("includes/login.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$prod = exec_sql_query($db, "SELECT * FROM photos LEFT OUTER JOIN product_types ON photos.product_type_id = product_types.id WHERE photos.id  = :a;", array(':a' => $id));
$prod = $prod->fetchAll();
$prod = $prod[0]; // Should only return a single entry!

$img_ext = $prod['ext'];
$price = $prod['price'];
$des = $prod['description'];
$name = $prod['name'];
$type = $prod['type'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Product | Jewelry By Mamta</title>
  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/gallery.css" media="all" />

</head>

<body>
  <?php include("includes/nav.php"); ?>
  <div class="main-container"><main>
  <div class='back_button'><a class='button' href='gallery.php?type=<?php echo $type; ?>'>← Back</a></div>


  <!-- Display Single Image -->
  <div class="single_img">
    <?php

    echo "<h1>" . $name . "</h1>";
    // Source: Credit Mamta Harris (client)
    echo "<div class = 'product_sing_img'><img alt='image' src=\"uploads/photos/" . $id . "." . $img_ext ."\" /></div>";
    echo "<h1>" . $price . "</h1>";
    echo "<h2 class='padd'>" . $des . "</h2>";


    // Buy Now/Delete/Edit Button Display
    if (logged_in()) { ?>
        <div class = 'button_display'>
          <ul>
            <li><form action='gallery.php?pid=<?php echo $id; ?>' method='post'>
              <button name='delete' type='submit'>Delete Product</button></form></li>
            <li><form action='edit.php?id=<?php echo $id; ?>' method='post'>
              <button type='submit'>Edit Product</button></form></li>
          </ul>
        </div>
    <?php } else { ?>
        <a class='button' href='order.php?id=<?php echo $id; ?>'>Buy Now!</a>
        <p class='buy_msg'>**If you click buy now, we will redirect you to an order form where you can negotiate the price!**</p>
    <?php } ?>
  </div>
  </main></div>
  <?php include("includes/footer.php"); ?>

</body>
</html>
