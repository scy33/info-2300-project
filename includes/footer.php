<?php

// This is my kinda clever way of preserving whatever page you're on when you log in/out.
$refresh_url = $_SERVER['REQUEST_URI'];
$logout_url = (strpos($refresh_url, '?') === FALSE) ? $refresh_url . '?action_logout=' : $refresh_url . '&action_logout=';
$login_url = (strpos($refresh_url, '?') === FALSE) ? $refresh_url . '?action_login=' : $refresh_url . '&action_login=';

 ?>
<footer>
    <div class="credit">Credit: All images are provided by Mamta Harris</div>
    <div class="links">
        <?php if(!logged_in()) { ?>
            <a href="<?php echo $login_url; ?>">Log In</a>
        <?php } else { ?>
            <table><tr><td>
                <a href="admin.php">Admin</a>
                &#8226;
                <a href="<?php echo $logout_url; ?>">Log Out</a>
                <?php echo '<p id="log_as">Logged in as: ' . htmlspecialchars($current_user['name']) . '</p>'; ?>
            </td></tr><tr><td id="row2">
                <a href="add_remove_user.php?action=add">Add New User Account</a>
                &#8226;
                <a href="add_remove_user.php?action=delete">Delete Your Account</a>
                &#8226;
                <a href="change_password.php">Change Your Password</a>
            </td></tr></table>
        <?php } ?>
    </div>
</footer>
