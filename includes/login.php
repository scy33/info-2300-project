<?php

/*
    This is login.php include file, written by Chris O'Brian.
	The code below is loosely based on my (Chris O'Brian's) Project 3. It was heavily modified to work for this project!
*/

$error_codes = array(
    "No Error",    // This element should never be accessed.
    "<strong>Incorrect Username:</strong> The username you entered was not recognized. Please try logging in again.",
    "<strong>Incorrect Password:</strong> The password you entered was not recognized. Please try logging in again.",
    "<strong>Blank Field:</strong> Please make sure that you enter a valid username and a valid password.",
    "<strong>Database Access Error:</strong> There appears to have been a server error while attempting to access your account.
    Try logging in again, but, if this problem persists, please contact the website administrator for assistance."
);

if (isset($_GET['login_error']) && preg_match('/^[0-3]$/', $_GET['login_error'])) {
    $error = $error_codes[(int)$_GET['login_error']];
}

function show_login() {
	// This function displays all necessary HTML for the login form.
    global $error;
    $refresh_url = $_SERVER['REQUEST_URI']; ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>User Login | Jewelry By Mamta</title>

        <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />
        <link rel="stylesheet" type="text/css" href="style/form.css" media="all" />
    </head>
    <body>
        <?php include("includes/nav.php"); ?>
        <div class="main-container"><main>
            <h1>Administrator Login</h1>

            <?php if (isset($error)) {echo '<div class="error"><p>' . $error . '</p></div>'; } ?>

            <p class="centered"><strong>To continue, please log in by supplying your username and password.</strong></p>

            <form id="sign-in-form" method="post" action="<?php echo $refresh_url; ?>">

                <div class="form-entry"> <!-- Ask for username -->
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" />
                </div>

                <div class="form-entry"> <!-- Ask for password -->
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" />
                </div>

                <div class="form-button">
                    <input class="button" id="submit" type="submit" name="login_sub" value="Login" />
                </div>

                <p>Not an admin? Browse through our inventory <a href="shop.php">Here!</a></p>

            </form>

        </main></div>
        <?php include("includes/footer.php"); ?>
    </body>
    </html>
<?php }

function login() {
    // This function both validates a new user and then logs them in to the site.
    global $db;

    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        refresh();
    }

    $username = trim($_POST['username']); // Trim whitespace off username + password
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') refresh(3);
    // Don't want blank username or password!

    $query = $db->prepare('SELECT * FROM users WHERE username = :a;');
    $p = array(':a' => $username);
    $query->execute($p);

    if ($query) $usr_ar = $query->fetchAll();
    else refresh(4);

    if (empty($usr_ar)) refresh(1);
    else $usr = $usr_ar[0]; // There should only be a single element!

    $password_hash = hash(HASH, $usr['salt'] . $password); // HASH = 'SHA3-512'

    if ($password_hash == $usr['password']) {
        $session = session_create_id();

        $update = $db->prepare("UPDATE users SET session = :a WHERE username = :b;");
        $p = array(':a' => $session, ':b' => $username);
        $update->execute($p);
    }
    else refresh(2);

    if ($query) {
        setcookie("session", $session, time() + COOKIE_EXPIRATION_SEC);
        refresh();
    }
    else refresh(4);
}

function logout() {
    // This function logs out the current user.
    global $current_user;

    if (isset($_COOKIE['session']) && $current_user != NULL) {
        $ses = $_COOKIE['session'];
        setcookie('session', $ses, time() - 1);
    }
    $current_user = NULL;
}

function refresh($e=0) {
    // Function redirects upon completing the login form. If login was successful, then there are no errors
    // and $e = 0, so refresh the current page without any login parameters set. Otherwise, reload current URI
    // with login_error codes.

    $refresh_url = $_SERVER['REQUEST_URI'];
    $refresh_url = preg_replace('/&login_error=[0-9]*/', '', $refresh_url);
    $refresh_url = preg_replace('/\?login_error=[0-9]*/', '', $refresh_url);

    if ($e != 0) {
        // This means there has been an error with logging the user in. Refresh login form.

		header('Location: ' . $refresh_url . '&login_error=' . (string)$e);
        exit();
	}
    else {
        // Otherwise refresh page (and clean up the URL a bit!)
        $refresh_url = preg_replace('/&action_logout=/', '', $refresh_url);
        $refresh_url = preg_replace('/\?action_logout=/', '', $refresh_url);

        $refresh_url = preg_replace('/&action_login=/', '', $refresh_url);
        $refresh_url = preg_replace('/\?action_login=/', '', $refresh_url);

        header('Location: ' . $refresh_url);
        exit();
    }
}

// ----------------------- CODE EXECUTED WHEN PAGE INCLUDES THIS: -------------------------------------------

if (isset($_POST['login_sub'])) {
    // If user has just completed the login form, attempt to validate credentials and log them in! Then refresh current page.
    login();
    refresh();
}

else if (isset($_GET['action_logout'])) {
    // If user wants to log out, then let them! Then refresh current page.
    logout();
    refresh();
}

else if ($current_user == NULL && isset($_GET['action_login'])) {
    // If the user has just clicked the login button in the footer, then we want to render the login form and
    // NOT the rest of the current page!
    show_login();
    exit();
}
// Else, if we don't want to do anything involving login/logout, just continue to render the current page.

?>
