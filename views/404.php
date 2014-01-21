<!-- always start the content pages by loading any page-specific stylesheets -->
<link rel="stylesheet" href="/views/404.css">

<ul>
	<li>SERVER_ADDR: <?php echo $_SERVER['SERVER_ADDR']; ?></li>
	<li>REQUEST_METHOD: <?php echo $_SERVER['REQUEST_METHOD']; ?></li>
	<li>QUERY_STRING: <?php echo $_SERVER['QUERY_STRING']; ?></li>
	<li>REMOTE_ADDR: <?php echo $_SERVER['REMOTE_ADDR']; ?></li>
	<li>REQUEST_URI: <?php echo $_SERVER['REQUEST_URI']; ?></li>
</ul>