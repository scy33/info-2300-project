<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

if (!logged_in()) {
    // First thing, user shouldn't be able to access this page if not logged in! So redirect to home page.
    header("Location: index.php");
    exit();
}

function display_product_type_options ($records) {
    foreach ($records as $record) {
        $type = $record['type'];
        echo '<option value='.$record['id'] .'>' .$type .'</option>';
    }
}

$success = true;
$upload_success = array();
$upload_feedback = array();

$upload_product_type = NULL;
$upload_product_name = NULL;
$upload_price_text = NULL;
$upload_price = NULL;
$upload_description = NULL;
$upload_info = NULL;
$upload_error = NULL;

if(isset($_POST["upload_product_type"])) {
    $upload_product_type = (int)$_POST["upload_product_type"];
    if(!set($upload_product_type)) {
        $upload_feedback[] = "Please select a product type.";
    }
}
if(isset($_POST["upload_product_name"])) {
    $upload_product_name = $_POST["upload_product_name"];
    if(!set($upload_product_name)) {
        $upload_feedback[] = "Please enter a product name.";
    }
}
if(isset($_POST["upload_price"])) {
    $upload_price = (int)$_POST["upload_price"];
    $upload_price_text = "$" .$upload_price;
    if(!set($upload_price)) {
        $upload_feedback[] = "Please enter a price.";
    }
}
if(isset($_POST["upload_description"])) {
    $upload_description = $_POST["upload_description"];
    if(!set($upload_description)) {
        $upload_feedback[] = "Please enter a description for this product.";
    }
}

$upload_info = $_FILES["upload_image"];
$upload_error = $upload_info["error"];

if ($upload_error == UPLOAD_ERR_NO_FILE) {
    $upload_feedback[] = "Please upload a file.";
}

// Maximum file size for uploaded files.
const MAX_FILE_SIZE = 1000000;
// Users can only upload files when logged in
if (isset($_POST["submit_upload"]) && set($upload_product_type) && set($upload_product_name) && set($upload_price) && set($upload_description) && $upload_error != UPLOAD_ERR_NO_FILE) {
    $db->beginTransaction();
    // var_dump($upload_product_type);
    if($upload_error == UPLOAD_ERR_OK) {
        $file_name = basename($upload_info["name"]);
        $upload_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $upload_sql = "INSERT INTO photos (ext, name, price, description, product_type_id) VALUES (:file_ext, :name, :price, :description, :product)";
        $upload_params = array(
            ':file_ext' => $upload_ext,
            ':name' => $upload_product_name,
            ':price' => $upload_price_text,
            ':description' => $upload_description,
            ':product' => $upload_product_type);
        $upload_result = exec_sql_query($db, $upload_sql, $upload_params);
        if($upload_result) {

            $prod_type_sql = "SELECT * FROM product_types WHERE id = :product;";
            $prod_type_params = array(':product' => $upload_product_type);
            $prod_type_records = exec_sql_query($db, $prod_type_sql, $prod_type_params)->fetchAll();
            $prod_type_lower = $prod_type_records[0]['type_low'];

            $file_id = $db->lastInsertId("id");
            $new_path = "uploads/photos/" .$file_id ."." .$upload_params[':file_ext'];
            //var_dump($prod_type_lower, $new_path);
            move_uploaded_file( $_FILES["upload_image"]["tmp_name"], $new_path);

            // Price Range/Tag
            $tags_sql = "SELECT * FROM tags;";
            $tags_params = array();
            $tags_result = exec_sql_query($db, $tags_sql, $tags_params);
            if ($tags_result) {
                $tags_records = $tags_result->fetchAll();
                if ( count($tags_records) > 0 ) {
                    $tag_id = find_price_range($tags_records, $upload_price);
                    if($tag_id == 0) {
                        $upload_feedback[] = "No price range matches product price";
                    }
                } else {
                    $upload_feedback[] = "No price ranges";
                }
            }

            // Photo_tags_types Table
            $tags_types_sql = "INSERT INTO photos_tags_types (photo_id, tag_id, prod_type_id) VALUES (:photo, :tag, :product)";
            $tags_types_params = array(
                ':photo' => $file_id,
                ':tag' => $tag_id,
                ':product' => $upload_product_type);
            $tags_types_result = exec_sql_query($db, $tags_types_sql, $tags_types_params);
            if($tags_types_result) {
                $upload_success[] = "Upload Successful.";
            } else {
                $upload_feedback[] = "Tags and Product Types not applied successfully. Please try uploading the image again.";
            }

        } else {
            $upload_feedback[] = "File Error occured while inserting into the database. Please try uploading the image again.";
        }



    } else if ($upload_error == UPLOAD_ERR_FORM_SIZE) {
        $upload_feedback[] = "File Error occured because the size of the file exceeds the maximum file size. Please upload a file that is less than or equal to 1 MB.";
    } else {
        $upload_feedback[] = "File Error occured while uploading. Please try again.";
    }
    // var_dump($upload_error, $file_name, $upload_ext, $source_credit, $source_link);

    $db->commit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<title>Admin | Jewelry By Mamta</title>

<link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

</head>

<body>
<?php include("includes/nav.php"); ?>
<div class="main-container" >
<main>
    <h1>This is our Admin Page</h1>
<?php
display_messages($upload_success, $success);
display_messages($upload_feedback, !$success);
?>

    <div class="centered">
        <a class='button' href='index.php'>Go Edit Home Page</a>
        <a class='button' href='about.php'>Go Edit About Page</a>
        <a class='button' href='shop.php'>Go Delete Products</a>
        <a class='button' href='shop.php'>Go Edit Products</a>
    </div>

    <p class="centered">To delete or edit products, please navigate through the shop to the individual product page first.</p>

    <h2 class="subheading">Add Product</h2>
    <form id="upload-form" method="post" action="admin.php" enctype="multipart/form-data">
        <div class="form-entry">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
            <label for="upload_image">Product Image*: </label>
            <input type="file" id="upload_image" name="upload_image"  />
        </div>
        <div class="form-entry">
            <label for="upload_product_type">Product Type*: </label>
            <select id="upload_product_type" name="upload_product_type" form="upload-form" >
                <option value="" label="Select Type"></option>
                <?php
                    $sql_product_types = "SELECT * FROM product_types;";
                    $params_product_types = array();
                    $result_product_types = exec_sql_query($db, $sql_product_types, $params_product_types);
                    if ($result_product_types) {
                        $records_product_types = $result_product_types->fetchAll();
                        if ( count($records_product_types) > 0 ) {
                            display_product_type_options($records_product_types);
                        } else {
                            $messages[] = "No product types currently listed";
                        }
                    }
                ?>
            </select>
        </div>
        <div class="form-entry">
            <label for="upload_product_name">Product Name*: </label>
            <input type="text" id="upload_product_name" name="upload_product_name" placeholder="Product Name" value="<?php echo htmlspecialchars($upload_product_name); ?>"  />
        </div>
        <div class="form-entry">
            <label for="upload_price">Price*: </label>
            <input type="number" id="upload_price" name="upload_price" placeholder="Price" value="<?php echo htmlspecialchars($upload_price); ?>"  />
        </div>
        <div class="form-entry">
            <label for="upload_description">Description*: </label>
            <textarea id="upload_description" name="upload_description" placeholder="Description" ><?php echo htmlspecialchars($upload_description); ?></textarea>
        </div>
        <div class="form-button">
            <button type="submit" name="submit_upload" class="submit">Add Product</button>
        </div>
    </form>



</main>



</div>
<?php include("includes/footer.php"); ?>
</body>
</html>
