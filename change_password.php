<?php
// INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");
include("includes/confirmation.php");
include("includes/login.php");

/*
    This change_password.php page was written by Chris O'Brian.
    This is the form for changing your password. This page should only be reached if an admin chooses to in the footer.
*/

if (!logged_in()) {
    // There's no reason for someone to be on this page if they're not logged in!
    header('Location: index.php');
    exit();
}

$error_codes = array(
    "No Error",    // This element should never be accessed.
    "<strong>Password Not Strong Enough:</strong> Please make sure your new password contains at least one letter and one number.",
    "<strong>Same Password:</strong> Your new password cannot be the same as your old one. Please, try again.",
    "<strong>Confirm Mismatch:</strong> Please ensure that you enter the same new password in the last two fields.",
    "<strong>Incorrect Password:</strong> Your old password entry did not match our records. Please, try again.",
    "<strong>Database Query Error:</strong> There appears to have been a server error while attempting to access your account.
    If this problem persists, please contact the website administrator for assistance."
);

if (isset($_GET['error']) && preg_match('/^[0-4]$/', $_GET['error'])) {
    $error = $error_codes[(int)$_GET['error']];
}

function show_form() {
    // This function displays all necessary HTML for the password change form

    global $error, $current_user; ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Change Password | Jewelry By Mamta</title>

        <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
    </head>

    <body><?php include("includes/nav.php"); ?>
        <div class="main-container">
        <main>
            <h1>Change Your Password</h1>

            <?php if (isset($error)) {echo '<div class="error"><p>' . $error . '</p></div>'; } ?>

            <p class="centered snug">Please fill out the following information to change your password.</p>
            <p class="centered snug">Your password must contain at least one letter (upper or lower case) and one number.</p>

            <form id="sign-in-form" method="post" action="change_password.php">

                <p class="subhead"><strong>Username: </strong> <?php echo htmlspecialchars($current_user['username']); ?></p>

                <div class="form-entry"> <!-- Ask for old password -->
                    <label for="old_password">Old Password:</label>
                    <input id="old_password" type="password" name="old_password" />
                </div>

                <div class="form-entry"> <!-- Ask for new password -->
                    <label for="new_password">New Password:</label>
                    <input id="new_password" type="password" name="new_password" />
                </div>

                <div class="form-entry"> <!-- Ask to confirm new password -->
                    <label for="confirm">Confirm New Password:</label>
                    <input id="confirm" type="password" name="confirm" />
                </div>

                <div class="form-button-1">
                    <input class="button" id="cp_sub" type="submit" name="cp_sub" value="Change Password" />
                </div>
            </form>
        </main></div>
        <?php include("includes/footer.php"); ?>
    </body>
    </html>
<?php }

function validate($pwd) {
    // Function makes sure new password is strong enough by validating it. Returns TRUE if password is strong enough.
    // $pwd must contain at least a letter and a number.
    if (preg_match('/[0-9]/', $pwd) === 0) return FALSE;
    else if (preg_match('/[a-z]{1,}/', $pwd) === 0 && preg_match('/[A-Z]{1,}/', $pwd) === 0) return FALSE;
    return TRUE;
}

function change_password() {
    // Function attempts to validate the form info for changing user password. If successful, returns new password.
    // Will redirect otherwise.
    global $db, $current_user;

    $old_pass = filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_STRING);
    $new_pass = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirm_pass = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_STRING);

    if ($new_pass != $confirm_pass) redirect(3);
    // Confirm doesn't match new password
    else if (!validate($new_pass)) redirect(1);
    // New password isn't strong enough
    else if ($new_pass === $old_pass) redirect(2);
    // If new password is the same as the old one.

    $db->beginTransaction();

    $query = $db->prepare('SELECT salt, password FROM users WHERE username = :a;');
    $p = array(':a' => $current_user['username']);
    $query->execute($p);

    if ($query) $usr_ar = $query->fetchAll()[0];
    else redirect(5); // DB Access Error

    $old_hash = hash(HASH, $usr_ar['salt'] . $old_pass);
    $new_hash = hash(HASH, $usr_ar['salt'] . $new_pass); // HASH = 'SHA3-512'

    if ($old_hash != $usr_ar['password']) redirect(4);
    // Old password is incorrect

    // Finally, update new password!
    $query = $db->prepare('UPDATE users SET password = :a WHERE username = :b;');
    $p = array(':a' => $new_hash, ':b' => $current_user['username']);
    if (!$query->execute($p)) redirect(5); //DB Statement failed

    $db->commit();
}

function redirect($w=0) {
    // Function handles redirecting user upon completion of the form or upon error. $w indicates code for where to direct.
    switch($w) {
        case 1:
            // Error: If new password isn't strong enough
            header('Location: change_password.php?error=1');
            break;
        case 2:
            // Error: new password = old one
            header('Location: change_password.php?error=2');
            break;
        case 3:
            // Error: Password Confirm mismatch
            header('Location: change_password.php?error=3');
            break;
        case 4:
            // Error: Old password incorrect
            header('Location: change_password.php?error=4');
            break;
        case 5:
            // Error: DB Access error
            header('Location: change_password.php?error=5');
            break;
        default:
            // Error: Any other reason go home.
            header('Location: index.php');
            break;
    }
    exit();
}

// ---------------------------------------------- CODE EXECUTED WHEN PAGE LOADED: -------------------------------------------

if (isset($_POST['cp_sub'])) {
    // If user has just completed the form to enter their info, attempt to validate and change their password.
    change_password();
    show_confirmation(0); // On success, show confirmation (from confirmation.php)
}

else {
    // If user is being directed to this page after clicking the link, show the form asking for their information.
    show_form();
}

?>
