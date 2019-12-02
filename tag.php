<?php
include("includes/init.php");
include("includes/login.php");

$tag_id = filter_input(INPUT_GET, 'tid', FILTER_SANITIZE_NUMBER_INT);
$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Tags | Jewelry By Mamta</title>

  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/gallery.css" media="all" />
</head>

<body>

    <?php include("includes/nav.php"); ?>
<div class="main-container"><main>
    <div class='back_button'><a class='button' href="gallery.php?type=<?php echo $type; ?>">← Back</a></div>

    <!-- Put tag name in -->
    <?php

        $tag = exec_sql_query($db, "SELECT price_range FROM tags WHERE tags.id = :a;", array(':a' => $tag_id))->fetchAll(PDO::FETCH_COLUMN);
        $tag_name = $tag[0];
        echo"<h1>" . $type . ": ". $tag_name . "</h1>";
    ?>

    <!-- Display Gallery -->
    <div class = "gallery">
        <?php
        echo ("<ul>");
            $gpt_lower = strtolower($type);

            $records = exec_sql_query($db, "SELECT photos.id, photos.ext, photos.price, photos.name, product_types.type_low FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE photos_tags_types.tag_id = '$tag_id' AND product_types.type_low = :a;", array(':a' => $gpt_lower))->fetchAll();

            if (count($records) > 0) {
                foreach($records as $record){
                    // Source: Credit Mamta Harris (client)
                    echo "<li><div class='img_style'>
                        <a href='product.php?id=" . $record['id'] . "." . $record['ext'] . "'><img alt='Image' src=\"uploads/photos/" . $record['id'] . "." . $record['ext'] . "\" /></a>";
                    echo "<div class = 'desc'>
                        <h2 class = 'desc'>" . $record['name'] . "</h2>
                        <h2>" . $record['price'] . "</h2>
                        </div>
                    </div></li>";
                }
            } else {
                echo("<div class='expand'>
                        <h2 class = 'unfortunate_msg_no_pad'>We're sorry. There are no " . $gpt_lower . " within the " . $tag_name .  " price range.</h2>
                    </div>");
            }
        ?>
        </ul>
    </div>
    </main></div>
    <?php include("includes/footer.php"); ?>

  </body>
</html>
