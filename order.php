<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

$id = $_GET['id'];

// SELECT all Product Names
// Add function that will echo "<option value="item">Item</option>" for each product

$success = true;
$order_success = array();
$order_feedback = array();
$id_okay = false;

function display_product_options ($records, $id) {
    foreach ($records as $record) {
        $name = $record['name'];
        $p_id = $record['id'];
        if($p_id == $id) {
            echo '<option selected value="'.$p_id.'">'.$name.'</option>';
        } else {
            echo '<option value="'.$p_id.'">'.$name.'</option>';
        }
    }
}

// Checking if all required fields are set
$contact_first_name = NULL;
$contact_last_name = NULL;
$contact_email = NULL;
$contact_phone = NULL;
$contact_subject = NULL;
$contact_comments = NULL;
$contact_price = "$0";


$product = NULL;
$product_type_low = NULL;
$product_ext = NULL;
$path = NULL;


$sql_product = "SELECT photos_tags_types.photo_id, photos_tags_types.prod_type_id, photos.ext, photos.name, photos.price, photos.description, product_types.type, product_types.type_low FROM photos INNER JOIN photos_tags_types ON photos.id = photos_tags_types.photo_id INNER JOIN product_types ON photos_tags_types.prod_type_id = product_types.id WHERE photos.id = :id;";
$params_product = array(':id' => $id);
$result_product = exec_sql_query($db, $sql_product, $params_product);
if ($result_product) {
    $records_product = $result_product->fetchAll();
    if ( count($records_product) > 0 ) {
        $product = $records_product[0];
        $product_type_low = $product["type_low"];
        $product_type = $product['type'];
        $product_ext = $product['ext'];
        $path = "uploads/photos/" .$id ."." . $product_ext;
        //var_dump($path);
        $contact_subject = $product['name'];
        $contact_price = $product['price'];
        $id_okay = true;
    } else {
        $order_feedback[] = "Product does not exist";
    }
}


if(isset($_POST["contact_first_name"])) {
    $contact_first_name = $_POST["contact_first_name"];
    if(!set($contact_first_name)) {
        $order_feedback[] = "Please enter your first name.";
    }
}
if(isset($_POST["contact_last_name"])) {
    $contact_last_name = $_POST["contact_last_name"];
    if(!set($contact_last_name)) {
        $order_feedback[] = "Please enter your last name.";
    }
}
if(isset($_POST["contact_email"])) {
    $contact_email = $_POST["contact_email"];
    if(!set($contact_email)) {
        $order_feedback[] = "Please enter a valid email address (email@domain).";
    }
}
if(isset($_POST["contact_comments"])) {
    $contact_comments = $_POST["contact_comments"];
    if(!set($contact_comments)) {
        $order_feedback[] = "Please enter a message.";
    }
}
if(isset($_POST["contact_phone"])) {
    $contact_phone = $_POST["contact_phone"];
}


if (isset($_POST["submit_contact"]) && set($contact_first_name) && set($contact_last_name) && set($contact_email) && set($contact_subject) && set($contact_comments)) {
    $db->beginTransaction();
    $time=time();
    $ymd_time = date("n/j/Y", $time);
    $hm_time = date("g:i A", $time);
    if(isset($_POST["contact_phone"])) {
        $contact_phone = $_POST["contact_phone"];
    } else {
        $contact_phone = "";
    }

    $sql_add_contact = "INSERT INTO messages (date, time, sender_first, sender_last, sender_email, sender_phone, subject, message) VALUES (:date, :time, :fn, :ln, :email, :phone, :subject, :comments);";
    $params_add_contact = array(
        ':date' => $ymd_time,
        ':time' => $hm_time,
        ':fn' => $contact_first_name,
        ':ln' => $contact_last_name,
        ':email' => $contact_email,
        ':phone' => $contact_phone,
        ':subject' => $contact_subject,
        ':comments' => $contact_comments);
    $result_add_contact = exec_sql_query($db, $sql_add_contact, $params_add_contact);
    if($result_add_contact) {
        $order_success[] = "Message sent. Please allow 1-3 business days for a response via email.";
    } else {
        $order_feedback[] = "Message failed to send. Please try again.";
    }
    $db->commit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<title>Order | Jewelry By Mamta</title>

<link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

</head>

<body>

<?php include("includes/nav.php"); ?>

<div class="main-container"><main>
<div class="back_button"><a class="button" href="product.php?id=<?php echo $id; ?>">← Back</a></div>
<h1><?php echo htmlspecialchars($contact_subject); ?> Order Form</h1>

<?php
display_messages($order_feedback, !$success);
display_messages($order_success, $success);

if($id_okay) {
?>

    <form id="contact-form" method="post" action=<?php "order.php?" .http_build_query( array('id' => $id)) ;?>>
        <div class="prod-image">
            <div>
                <p class="small italic centered"> <?php echo htmlspecialchars($contact_subject); ?> shown below </p>
                <!-- Source: Credit Mamta Harris (client) -->
                <img src="<?php echo $path ?>"  alt="<?php echo htmlspecialchars($contact_subject); ?>" />
                <p class="centered"> Price: <?php echo htmlspecialchars($contact_price); ?> </p>
            </div>
        </div>
        <div class="go-down"></div>
        <div class="fn_ln_outer">
            <div class="form-entry fn_ln">
                <label for="contact_first_name">First Name* </label>
                <input type="text" id="contact_first_name" name="contact_first_name" placeholder="First Name" value="<?php echo htmlspecialchars($contact_first_name); ?>" />
            </div>
            <div class="form-entry fn_ln">
                <label for="contact_last_name">Last Name* </label>
                <input type="text" id="contact_last_name" name="contact_last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($contact_last_name); ?>" />
            </div>
        </div>
        <div class="form-entry">
            <label for="contact_email">Email*: </label>
            <input type="email" id="contact_email" name="contact_email" placeholder="email@domain" value="<?php echo htmlspecialchars($contact_email); ?>" />
        </div>
        <div class="form-entry">
            <label for="contact_phone">Phone Number: </label>
            <input type="text" id="contact_phone" name="contact_phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($contact_phone); ?>"/>
        </div>
        <!-- <div class="form-entry">
            <label for="contact_subject">Products*: </label>
            <select id="contact_subject" name="contact_subject" form="contact-form" require>
                <?php
                    // $sql_products = "SELECT id, name FROM photos;";
                    // $params_products = array();
                    // $result_products = exec_sql_query($db, $sql_products, $params_products);
                    // if ($result_products) {
                    //     $records_products = $result_products->fetchAll();
                    //     if ( count($records_products) > 0 ) {
                    //         display_product_options($records_products, $id);
                    //     } else {
                    //         $messages[] = "No products currently listed";
                    //     }
                    // }
                ?>
            </select>
        </div> -->
        <div class="form-entry">
            <label for="contact_comments">Message*: </label>
            <textarea id="contact_comments" name="contact_comments" placeholder="Message" require><?php echo htmlspecialchars($contact_comments); ?></textarea>
        </div>
        <div class="form-button">
            <button type="submit" name="submit_contact" class="button">Order Product</button>
        </div>
    </form>
</main>


<?php
}
include("includes/footer.php"); ?>

</div>


</body>
</html>
