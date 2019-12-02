<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/login.php");

if (isset($_GET['api'])) {
    // To re-sort table without re-loading the entire page.
    generateTable();
    exit();
}

if (!logged_in()) {

$success = true;
$contact_success = array();
$contact_feedback = array();

// Checking if all required fields are set
$contact_first_name = NULL;
$contact_last_name = NULL;
$contact_email = NULL;
$contact_phone = NULL;
$contact_subject = NULL;
$contact_comments = NULL;

if(isset($_POST["contact_first_name"])) {
    $contact_first_name = $_POST["contact_first_name"];
    if(!set($contact_first_name)) {
        $contact_feedback[] = "Please enter your first name.";
    }
}
if(isset($_POST["contact_last_name"])) {
    $contact_last_name = $_POST["contact_last_name"];
    if(!set($contact_last_name)) {
        $contact_feedback[] = "Please enter your last name.";
    }
}
if(isset($_POST["contact_email"])) {
    $contact_email = $_POST["contact_email"];
    if(!set($contact_email)) {
        $contact_feedback[] = "Please enter a valid email address (email@domain).";
    }
}
if(isset($_POST["contact_subject"])) {
    $contact_subject = $_POST["contact_subject"];
    if(!set($contact_subject)) {
        $contact_feedback[] = "Please enter a subject for this message.";
    }
}
if(isset($_POST["contact_comments"])) {
    $contact_comments = $_POST["contact_comments"];
    if(!set($contact_comments)) {
        $contact_feedback[] = "Please enter a message.";
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
        $contact_success[] = "Message sent. Please allow 1-3 business days for a response via email.";
    } else {
        $contact_feedback[] = "Message failed to send. Please try again.";
    }
    $db->commit();
}
}

function generateTable() {
    global $db;

    $sql = "SELECT * FROM messages";

    if ($_GET['sort'] == 'date')
    {
        $sql .= " ORDER BY date;";
    }
    elseif ($_GET['sort'] == 'sender_first')
    {
        $sql .= " ORDER BY sender_first;";
    }
    elseif ($_GET['sort'] == 'subject')
    {
        $sql .= " ORDER BY subject;";
    }
    elseif($_GET['sort'] == 'added')
    {
        $sql .= " ORDER BY DateAdded;";
    }
    $messages_table = exec_sql_query($db, $sql, array()); ?>


    <div class="messages_div">
    <table id="messages_table" class="searchable sortable">
    <thead><tr>
        <th><a href="javascript:refreshTable('date')">Date:</a></th>
        <th><a href="javascript:refreshTable('sender_first')">Name:</a></th>
        <th><a href="javascript:refreshTable('subject')">Subject:</a></th>
    </tr></thead>
    <tbody>
    <?php
    foreach ($messages_table as $messages_row) {
    ?>
    <tr>
        <?php //echo "<a href='message.php?id=" . $messages_row['id'] . "'><li>"; ?>
            <td>
            <?php
            echo "<a href='message.php?id=" . $messages_row['id'] . "'>";
            echo $messages_row['date'];
            echo "</a>";?>
        </td>
        <td>
            <?php
            echo "<a href='message.php?id=" . $messages_row['id'] . "'>";
            echo $messages_row['sender_first']; echo " "; echo $messages_row['sender_last'];
            echo "</a>"; ?>
        </td>
        <td>
            <?php echo "<a href='message.php?id=" . $messages_row['id'] . "'>";
            echo $messages_row['subject'];
            echo "</a>"; ?>
        </td>
    </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
</div>

<?php }

if (!logged_in()) { ?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<title>Contact | Jewelry By Mamta</title>

<link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
<link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

</head>

<body>
<?php include("includes/nav.php"); ?>
<div class="main-container">
<main>
    <h1>Contact Me</h1>
<?php
display_messages($contact_feedback, !$success);
display_messages($contact_success, $success);
?>


    <form id="contact-form" method="post" action="contact.php">
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
            <label for="contact_email">Email* </label>
            <input type="email" id="contact_email" name="contact_email" placeholder="email@domain" value="<?php echo htmlspecialchars($contact_email); ?>" />
        </div>

        <div class="form-entry">
            <label for="contact_phone">Phone Number </label>
            <input type="text" id="contact_phone" name="contact_phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($contact_phone); ?>" />
        </div>

        <div class="form-entry">
            <label for="contact_subject">Subject* </label>
            <input type="text" id="contact_subject" name="contact_subject" placeholder="Subject" value="<?php echo htmlspecialchars($contact_subject); ?>" />
        </div>

        <div class="form-entry">
            <label for="contact_comments">Message* </label>
            <textarea id="contact_comments" name="contact_comments" placeholder="Message"><?php echo htmlspecialchars($contact_comments); ?></textarea>
        </div>

        <div class="form-button">
            <button type="submit" name="submit_contact" class="button">Send Message</button>
        </div>
    </form>
</main>


<?php include("includes/footer.php"); ?>

</div>


</body>
</html>
<?php } else { ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Messages | Jewelry By Mamta</title>

    <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
    <link rel="stylesheet" type="text/css" href="style/messages.css" media="all" />

</head>

<body>
<?php include("includes/nav.php"); ?>
    <div class="main-container" ><main>
        <h1>Here are your messages</h1>
        <p class="centered top-break">Click to sort by:</p>

        <?php  //Table generator function
            generateTable();
        ?>


</main></div>


    <?php include("includes/footer.php"); ?>

    <!-- Load JavaScript at end of the body.-->
    <script src="scripts/jquery-3.4.0.min.js"></script>
    <script src="scripts/messages.js"></script>

</body>
</html>
<?php } ?>
