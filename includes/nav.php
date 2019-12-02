<!-- Navigation Bar -->
<?php
$pages = array(
	array('index.php', 'Home'),
	array('about.php', 'About'),
	array('contact.php', 'Contact'),
	array('shop.php', 'Shop')
);

if (logged_in()) {
	$pages[2][1] = 'Messages';
}

$current_file = basename($_SERVER['PHP_SELF']);

?>
<header><nav>
    <h2 class='add_pad'>Jewelry by Mamta</h2>
    <ul>
	  	<?php foreach ($pages as $page) {
			if ($current_file == $page[0]) { ?>
				<?php echo '<li id="current-page"><a class="current-page-a" href=' . $page[0] . ">" . $page[1] . "</a></li>"; ?>
			<?php } else {
				echo "<li><a href=".$page[0].">".$page[1]."</a></li>";
			}
		} ?>
  	</ul>
</nav></header>

<div class="add-space"></div>

<!-- Source: (Original Work) Justin San Antonio, from Lab 3 -->
