<?php
// vvv DO NOT MODIFY/REMOVE vvv

// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define(VERSION_MESSAGE, "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (file_exists($init_sql_filename)) {
      $db_init_sql = file_get_contents($init_sql_filename);
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        // If we had an error, then the DB did not initialize properly,
        // so let's delete it!
        unlink($db_filename);
        throw $exception;
      }
    } else {
      unlink($db_filename);
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return null;
}

function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}
// ^^^ DO NOT MODIFY/REMOVE ^^^

// You may place any of your code here.

// ---------------------------------------- OUR CODE ------------------------------------- //


$db = open_or_init_sqlite_db('secure/gallery.sqlite', 'secure/init.sql');
const COOKIE_EXPIRATION_SEC = 3600; // 1 hour
const HASH = 'SHA3-512';


// Boolean determining login status.
function logged_in() {
    global $current_user;
    return ($current_user != NULL);
}

// Displays feedback/messages in array
function display_messages($messages, $success) {
  if($success) {
    foreach ($messages as $message) {
      echo "<div class='success'><p>" . $message . "</p></div>";
    }
  } else {
    foreach ($messages as $message) {
      echo "<div class='error'><p>" . $message . "</p></div>";
    }
  }
}

// Checks wether the parameter isset and is not an empty string
function set($param) {
  return isset($param) && $param != "";
}

function find_price_range ($records, $price) {
  foreach ($records as $record) {
      $low = (int)$record['low'];
      $high = (int)$record['high'];

      if($low <= $price && $price < $high) {
          return $record['id'];
      }

      //var_dump($low, $high, $low < $price && $price < $high);
  }
  return 0;
}

// This code handles re-logging in user when they access each page, if they have valid cookie.
if (isset($_COOKIE["session"])) {
  $session = $_COOKIE["session"];

  $query = $db->prepare('SELECT * FROM users WHERE session = :a;');
  $p = array(':a' => $session);
  $query->execute($p);
  if ($query) $user_record = $query->fetchAll()[0]; // Should only be a single record!

  if (isset($user_record)) {
  $current_user = $user_record;
    setcookie("session", $session, time() + COOKIE_EXPIRATION_SEC);

  } else {
      $current_user = NULL;
      setcookie("session", $session, time() - 1);
      // Expires cookie that shouldn't be there. The code should never make it to this part unless hacking...
  }
}
else $current_user = NULL;
