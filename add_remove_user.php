<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/confirmation.php");
include("includes/login.php");

/*
    This add_remove_user.php page was written by Chris O'Brian.
    This is the form for adding a new user, or for removing your own user account for any reason.
*/

if (!logged_in()) {
    // There's no reason for someone to be on this page if they're not logged in!
    header('Location: index.php');
    exit();
}

$error_codes = array(
    "No Error",    // This element should never be accessed.
    "<strong>Root User:</strong> The 'admin' user cannot be deleted for security reasons.",
    "<strong>Username Required:</strong> The username field is required and cannot be left blank.",
    "<strong>Username Repeat:</strong> The username you have selected already exists. Please choose another.",
    "<strong>Database Query Error:</strong> There appears to have been a server error while attempting to access the accounts table in the database.
    If this problem persists, please contact the website administrator for assistance."
);

if (isset($_GET['error']) && preg_match('/^[0-4]$/', $_GET['error'])) {
    $error = $error_codes[(int)$_GET['error']];
}

function show_add_form() {
    // This function displays all necessary HTML for the for to create a new user account.

    global $error, $current_user; ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Add User | Jewelry By Mamta</title>

        <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
    </head>

    <body><?php include("includes/nav.php"); ?>
        <div class="main-container">
        <main>
            <h1>New User Account</h1>

            <?php if (isset($error)) {echo '<div class="error"><p>' . $error . '</p></div>'; } ?>

            <p class="centered snug">Please create a username for the new user and give their full name in the fields below.</p>
            <p class="centered snug">A temporary password will be generated for the new account when it's created.</p>

            <form id="sign-in-form" class="top-break" method="post" action="add_remove_user.php">

                <div class="form-entry"> <!-- Ask for a new username -->
                    <label for="username">Username:</label>
                    <input id="username" type="text" name="username" />
                </div>

                <div class="form-entry"> <!-- Ask for a new full name -->
                    <label for="name">Full Name:</label>
                    <input id="name" type="text" name="name" />
                </div>

                <div class="form-button-1">
                    <input class="button" id="au_sub" type="submit" name="au_sub" value="Add Account" />
                </div>
            </form>
        </main>
    </div>
    <?php include("includes/footer.php"); ?>
    </body></html>
<?php }

function make_user($uname, $name) {
    // Function verifies info and creates a new user account and adds to table (based on values passed in argument for username, name).
    // Generates new password and salt. Returns the temporary password.
    global $db;

    if ($uname == '') redirect(2); // If username is left blank, throw an error. Name can be blank.

    $query = $db->prepare('SELECT username FROM users;');
    $p = array();
    $query->execute($p);
    if ($query) $usr_ar = $query->fetchAll();
    else redirect(4); //DB Access Error

    foreach($usr_ar as $usr) {
        if ($usr['username'] == $uname) redirect(3); //Username already exists error!
    }

    // We need to generate salt and temporary password. generate_string() makes a random string.
    $salt = generate_string(15);
    $temp_password = generate_string(6);
    $hash = hash(HASH, $salt . $temp_password);

    // Now we can actually add the new user!
    $query = $db->prepare("INSERT INTO 'users' (name, username, salt, password) VALUES (:a, :b, :c, :d);");
    $p = array(':a' => $name, ':b' => $uname, ':c' => $salt, ':d' => $hash);
    if (!$query->execute($p)) redirect(4); //DB Statement failed

    return $temp_password;
}

function generate_string($l) {
    // Function generates a random string of numbers and letters of length $l. Called to generate salt + temp password.

    $ab = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $result = '';

    for($i = 0; $i < $l; $i++) {
        $ind = rand(0, 61); // 26 uppercase letters, 26 lowercase, 10 numbers. 26 + 26 + 10 = 62
        $result = $result . substr($ab, $ind, 1);
    }
    return $result;
}

function show_delete_confirm() {
    global $error, $current_user;?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Delete Account | Jewelry By Mamta</title>

        <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
    </head>

    <body><div class="main-container">
        <?php include("includes/nav.php"); ?>
        <main>
            <h1>Delete Your Account</h1>

            <?php if (isset($error)) {echo '<div class="error"><p>' . $error . '</p></div>'; } ?>

            <?php if (!isset($error)) { ?>
                <div class='top-break'>
                    <h2 class="subhead">Are you sure you want to delete your account?</h2>
                    <p class="centered warning"><strong>Warning:</strong> This action is irreversible.</p>
                </div>
            <?php } ?>

            <form class="sign-in-form centered" method="post" action="add_remove_user.php">
                <p class="centered">
                    <?php if(!isset($error)) { ?>
                        <input class="button standalone" id="sub_delete" type="submit" name="sub_delete" value="Delete Account" />
                    <?php } ?>
                    <input class="button standalone" id="back" type="submit" name="back" value="Back To Admin Page" />
                </p>
            </form>
        </main>
    </div>
    <?php include("includes/footer.php"); ?>
</body>

    </html>
<?php }

function delete_user() {
    // Function deletes the user corresponding to info stored in $current_user.
    global $db, $current_user;

    $usr = $current_user['username'];
    if($usr == 'admin') redirect(1); // Can't delete admin account!

    // Now we can actually add the new user!
    $query = $db->prepare("DELETE FROM users WHERE username = :a");
    $p = array(':a' => $usr);
    if (!$query->execute($p)) redirect(4); //DB Statement failed

    logout();
}

function redirect($w=0) {
    // Function handles redirecting user upon completion of the form or upon error. $w indicates code for where to direct.
    // Similar to function in change_password.php but not identical. admin.php is default

    switch($w) {
        case 1:
            // Error: If user tries to delete admin account.
            header('Location: add_remove_user.php?action=delete&error=1');
            break;
        case 2:
            // Error: Username was left blank on add form
            header('Location: add_remove_user.php?action=add&error=2');
            break;
        case 3:
            // Error: Username already exists
            header('Location: add_remove_user.php?action=add&error=3');
            break;
        case 4:
            // Error: DB query error
            header('Location: add_remove_user.php?action=add&error=4');
            break;
        default:
            // Error: Any other reason go to admin home.
            header('Location: admin.php');
            break;
    }
    exit();
}

// ---------------------------------------------- CODE EXECUTED WHEN PAGE LOADED: -------------------------------------------


if (isset($_POST['au_sub']) && isset($_POST['username']) && isset($_POST['name'])) {
    // If user has just completed the form to make new user, attempt to do so:

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    $temp_pwd = make_user($username, $name);
    show_confirmation(1, array('username' => $username, 'password' => $temp_pwd)); // On success, show confirmation!
}

else if (isset($_POST['sub_delete'])) {
    // If user has just confirmed that they want to delete their account, try to do so:
    // (Deletes $current_user account)
    delete_user();
    show_confirmation(2); // On success, show confirmation!
}

else if (isset($_GET['action']) && $_GET['action'] == 'add') {
    // If user has just come to his page to add new account, show the form.
    show_add_form();
}

else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    // If user has just come to his page to delete account, show the delete confirmation:
    show_delete_confirm();
}

else {
    // If user got here by accident, send them to admin page:
    redirect();
}

?>
