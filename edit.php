<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

function display_product_type_options ($records, $pt_id) {
    foreach ($records as $record) {
        $type = $record['type'];
        $id = $record['id'];
        if($pt_id == $id) {
            echo '<option selected value="'.$id.'">'.$type.'</option>';
        } else {
            echo '<option value='.$id .'>' .$type .'</option>';
        }
    }
}

if (!logged_in()) {
    // First thing, user shouldn't be able to access this page if not logged in! So redirect to home page.
    header("Location: index.php");
    exit();
}

$success = true;
$update_success = array();
$update_feedback = array();

$product_id = (int)filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


$sql_product = "SELECT photos_tags_types.photo_id, photos_tags_types.prod_type_id, photos.ext, photos.name, photos.price, photos.description, product_types.type, product_types.type_low FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE photos.id = :id;";
    $params_product = array(
        ':id' => $product_id
    );
    $result_product = exec_sql_query($db, $sql_product, $params_product);
    if ($result_product) {
        // The query was successful, let's get the record.
        $records_product = $result_product->fetchAll();
        if ( count($records_product) > 0 ) {
            // We have a record to edit (id is UNIQUE)
            $record_product = $records_product[0];
            $update_info = NULL;
            $update_error = NULL;
            $update_ext = $record_product['ext'];
            $update_product_type = (int) $record_product["prod_type_id"];
            $update_product_type_cap = $record_product["type"];
            $update_product_type_low = $record_product["type_low"];
            $update_product_name = $record_product['name'];
            $update_price_text = $record_product['price'];
            $update_price = trim($update_price_text, '$');
            $update_description = $record_product['description'];

            $og_path = "uploads/photos/" .$product_id . $update_ext;
            //var_dump($update_product_type, $update_product_name, $update_price, $update_description);
        } else {
            // No results found
            $messages[] = "No matching products found for this search.";
        }
    }

if(isset($_POST["update_product_type"])) {
    $update_product_type = (int)$_POST["update_product_type"];
    if(!set($update_product_type)) {
        $update_feedback[] = "Please select a product type.";
    } else {
        $prod_type_sql = "SELECT * FROM product_types WHERE id = :product;";
        $prod_type_params = array(':product' => $update_product_type);
        $prod_type_records = exec_sql_query($db, $prod_type_sql, $prod_type_params)->fetchAll();
        $update_product_type_cap = $prod_type_records[0]['type'];
        $update_product_type_low = $prod_type_records[0]['type_low'];
    }
}
if(isset($_POST["update_product_name"])) {
    $update_product_name = $_POST["update_product_name"];
    if(!set($update_product_name)) {
        $update_feedback[] = "Please enter a product name.";
    }
}
if(isset($_POST["update_price"])) {
    $update_price = (int)$_POST["update_price"];
    $update_price_text = "$" .$update_price;
    if(!set($update_price)) {
        $update_feedback[] = "Please enter a price.";
    }
}
if(isset($_POST["update_description"])) {
    $update_description = $_POST["update_description"];
    if(!set($update_description)) {
        $update_feedback[] = "Please enter a description for this product.";
    }
}

// Maximum file size for uploaded files.
const MAX_FILE_SIZE = 1000000;
// Users can only upload files when logged in
if (isset($_POST["submit_update"]) && set($update_product_type) && set($update_product_name) && set($update_price) && set($update_description)) {
    $db->beginTransaction();
    // var_dump($update_product_type);
    $update_info = $_FILES["update_image"];
    $update_error = $update_info["error"];
    if($update_error == UPLOAD_ERR_OK || $update_error == UPLOAD_ERR_NO_FILE) {
        if($update_error == UPLOAD_ERR_OK) {
            unlink("uploads/photos/" .$product_id ."." .$record_product['ext']);
            $update_ext = strtolower(pathinfo(basename($update_info["name"]), PATHINFO_EXTENSION));
            $new_path = "uploads/photos/" .$product_id . "." .$update_ext;
            move_uploaded_file( $update_info["tmp_name"], $new_path);
        } else {
            $new_path = "uploads/photos/" . $product_id . "." .$update_ext;
            rename( $og_path, $new_path);
        }

            $sql = "UPDATE photos SET ext=:file_ext, name=:name, price=:price, description=:description WHERE id=:id";
            $params = array(
            ':file_ext' => $update_ext,
            ':name' => $update_product_name,
            ':price' => $update_price_text,
            ':description' => $update_description,
            ':id' => $product_id);
            $result = exec_sql_query($db, $sql, $params);
            if($result) {
                //$update_success[] = "Update Successful.";
            } else {
                $update_feedback[] = "File Error occured while inserting into the database. Please try updating the image again.";
            }

            // Price Range/Tag
            $tags_sql = "SELECT * FROM tags;";
            $tags_params = array();
            $tags_result = exec_sql_query($db, $tags_sql, $tags_params);
            if ($tags_result) {
                $tags_records = $tags_result->fetchAll();
                if ( count($tags_records) > 0 ) {
                    $tag_id = find_price_range($tags_records, $update_price);
                    if($tag_id == 0) {
                        $update_feedback[] = "No price range matches product price";
                    }
                } else {
                    $update_feedback[] = "No price ranges";
                }
            }

            // Photo_tags_types Table
            $tags_types_sql = "UPDATE photos_tags_types SET tag_id=:tag, prod_type_id=:product WHERE photo_id=:photo";
            $tags_types_params = array(
                ':photo' => $product_id,
                ':tag' => $tag_id,
                ':product' => $update_product_type);
            $tags_types_result = exec_sql_query($db, $tags_types_sql, $tags_types_params);
            if($tags_types_result) {
                $update_success[] = "Product Edit Successful.";
            } else {
                $update_feedback[] = "Tag and Product Type not applied successfully. Please try uploading the image again.";
            }

    } else if ($update_error == UPLOAD_ERR_FORM_SIZE) {
        $update_feedback[] = "File Error occured because the size of the file exceeds the maximum file size. Please update a file that is less than or equal to 1 MB.";
    } else if ($update_error == UPLOAD_ERR_NO_FILE) {

    } else {
        $update_feedback[] = "File Error occured while updating.";
    }
        // var_dump($update_error, $file_name, $update_ext, $source_credit, $source_link);




    $db->commit();
}

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Edit Product | Jewelry By Mamta</title>

    <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

    </head>

    <body>

    <?php include("includes/nav.php");
    $prod_id = $product_id ?>

    <div class="main-container">
    <div class = 'back_button'><a class = 'button' href='product.php?id=<?php echo $prod_id; ?>'>← Back</a></div>
    <h1>Edit Product</h1>


    <?php


    $prod_type_ext = exec_sql_query($db, "SELECT product_type_id, ext FROM photos WHERE photos.id = '$prod_id';")->fetchAll(PDO::FETCH_ASSOC);
    $prod_type_ext_first = $prod_type_ext[0];

    $type_id = $prod_type_ext_first['product_type_id'];

    $type = exec_sql_query($db, "SELECT type_low FROM product_types WHERE product_types.id = '$type_id';")->fetchAll(PDO::FETCH_COLUMN);
    $type_name = $type[0];

    $ext = $prod_type_ext_first['ext'];

    display_messages($update_feedback, !$success);
    display_messages($update_success, $success);
    ?>

    <main>


    <form id="update-form" method="post" action=<?php echo '"edit.php?' .http_build_query( array('id' => $product_id) ) .'"'; ?> enctype="multipart/form-data">
    <div class="prod-image">
        <div>
        <p class="small italic centered"> Current image shown below </p>
        <!-- Source: Credit Mamta Harris (client) -->
        <img src="uploads/photos/<?php echo $product_id . "." . $update_ext ?>"  alt="Current Product" />
        </div>
        <div class="form-entry">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
        <label for="update_image">Product Image: </label>
        <input type="file" id="update_image" name="update_image"/>
        </div>
    </div>
    <div class="form-entry">
            <label for="update_product_type">Product Type*: </label>
            <select id="update_product_type" name="update_product_type" form="update-form">
                <option value="" label="Select Type"></option>
                <?php
                    $sql_product_types = "SELECT * FROM product_types;";
                    $params_product_types = array();
                    $result_product_types = exec_sql_query($db, $sql_product_types, $params_product_types);
                    if ($result_product_types) {
                        $records_product_types = $result_product_types->fetchAll();
                        if ( count($records_product_types) > 0 ) {
                            display_product_type_options($records_product_types, $update_product_type);
                        } else {
                            $messages[] = "No product types currently listed";
                        }
                    }
                ?>
            </select>
        </div>
    <div class="form-entry">
    <label for="update_product_name">Product Name*: </label>
    <input type="text" id="update_product_name" name="update_product_name" placeholder="Product Name" value="<?php echo htmlspecialchars($update_product_name); ?>" />
    </div>
    <div class="form-entry">
    <label for="update_price">Price*: </label>
    <input type="number" id="update_price" name="update_price" placeholder="Price" value="<?php echo htmlspecialchars($update_price); ?>" />
    </div>
    <div class="form-entry">
    <label for="update_description">Description*: </label>
    <textarea id="update_description" name="update_description" placeholder="Description"><?php echo htmlspecialchars($update_description); ?></textarea>
    </div>
    <div class="form-button">
    <button type="submit" name="submit_update" class="submit">Edit Product</button>
    </div>
    </form>
    </main>

    </div>
    <?php include("includes/footer.php"); ?>



    </body>
    </html>
