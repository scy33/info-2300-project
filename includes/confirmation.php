<?php

/* This module consists of a single function called by other pages to display a confirmation message.
    Used in the password change form as well as the user add/remove form.
*/

function show_confirmation($a, $var=array()) {
    // $a represents a code that corresponds to an entry in each of the following tables as to what to display. $var
    //  is a place holder for any variables that need to be passed along into the confirmation message.

    global $current_user;

    $titles = array(
        "Password Changed",
        "Account Created",
        "Account Deleted"
    );

    $bodies = array (
        "Your password was successfully changed. Click below to go back to the admin page.",
        "A new user account was successfully created with username: <strong>" . $var['username'] . "</strong> and temporary password: <strong>"
            . $var['password'] . "</strong></p><p class=\"centered snug\">The new user should use this password to log in and immediately change their password to something more secure.</p><p class=\"centered warning\"><strong>Warning:</strong> Take note of the temporary password as it will not be retrievable once you leave this page!</p>",
        "You have been logged out, and the user account was successfully deleted."
    );

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Confirmation | Jewelry By Mamta</title>

        <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
    </head>

    <body><div class="main-container">

        <?php include("includes/nav.php"); ?>
        <main>
            <h1><?php echo $titles[$a]; ?></h1>

            <p class="snug top-break centered"><?php echo $bodies[$a] ?></p>

            <p class="centered">
                <?php if ($a == 0) { ?>
                    <a class="button standalone" href="admin.php">Admin Page</a>
                <?php } else if ($a == 1) { ?>
                    <a class="button standalone" href="admin.php">Admin Page</a>
                    <a class="button standalone" href="add_remove_user.php?action=add">Add Another User</a>
                <?php } else { ?>
                    <a class="button standalone" href="index.php">Home</a>
                <?php } ?>
            </p>

        </main>
        <?php include("includes/footer.php"); ?>

    </div></body>
    </html>

<?php } ?>
