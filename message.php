<?php
include("includes/init.php");
include("includes/login.php");

if (!logged_in()){
  header("Location: contact.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>View Message | Jewelry By Mamta</title>
  <link rel="stylesheet" type="text/css" href="style/nav.css" media="all" />
  <link rel="stylesheet" type="text/css" href="style/all.css" media="all" />

</head>

<body>
<?php include("includes/nav.php"); ?>
<div class="main-container">
  <main>

<?php

$get_message = $_GET['id'];

  ?>

  <a class='button standalone' href="contact.php">← Back to messages</a>

  <div class = "single_message">
    <?php
    $sender_nam = exec_sql_query($db, "SELECT sender_first FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
    $sender_name = $sender_nam[0];
    $sender_nam2 = exec_sql_query($db, "SELECT sender_last FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
    $sender_name2 = $sender_nam2[0];

   $sender_dat = exec_sql_query($db, "SELECT date FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $sender_date = $sender_dat[0];

   $sender_tim = exec_sql_query($db, "SELECT time FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $sender_time = $sender_tim[0];

   $sender_emai = exec_sql_query($db, "SELECT sender_email FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $sender_email = $sender_emai[0];

   $sender_phon = exec_sql_query($db, "SELECT sender_phone FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $sender_phone = $sender_phon[0];

   $subjec = exec_sql_query($db, "SELECT subject FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $subject = $subjec[0];

   $sender_messag = exec_sql_query($db, "SELECT message FROM messages WHERE messages.id  = '$get_message';")->fetchAll(PDO::FETCH_COLUMN);
   $sender_message = $sender_messag[0];


   echo "<h1>Subject: " . $subject . "</h1>";
   echo "<h2>From: " . $sender_name . " " . $sender_name2 . "  </h2><h2>  " . $sender_time . "    " . $sender_date . "</h2>";
   echo "<h3>Email: " . $sender_email ;

   if(empty($sender_phone)){
     echo "</h3>";
   }else{
   echo " | Phone: " . $sender_phone . "</h3>";
   };
   echo "<p class='centered'>" . $sender_message . "</p>";

    ?>
    </div>
    </main>
  </div>

    <?php include("includes/footer.php"); ?>

  </body>
  </html>
