<?php
	// load global app logic
	include_once('/app/global-logic.php');
	
	
    // check login    
	
	// load the MEOLoadViews class
	include_once('/app/load-views.php');
	
	// create an instance of the app object and set the view to load
    if (!isset($_GET['load'])) {
    	$view = new MEOLoadViews('');
    }
	else {
		$view = new MEOLoadViews($_GET['load']);
	}
	
	// load the view
	$view->loadView();
?>