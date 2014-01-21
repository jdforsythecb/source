<?php	

	// this file contains all the logic for loading views


	// ENUMS (simulated via classes with constants)
	
	// the different views we can load
	final class Views {
		const HOME = "/views/home.php";
		const SETTINGS = "/views/settings.php";
		const USERS = "/views/users.php";
		const REPORTS = "/views/reports.php";
		const DONATE = "/views/donate.php";
		
		const FOUROHFOUR = "/views/404.php";
	}
	
	// the different parts of the template
	// note: top and bottom refer to the part of the html document,
	// not the top/bottom of the rendered view
	final class Templates {
		const TEMPLATE_TOP = "/template/template-top.php";
		const TEMPLATE_BOTTOM = "/template/template-bottom.php";
	}
	
	
	// MAIN VIEW CLASS
	class MEOLoadViews {
		
		// PUBLIC PROPERTIES
		
		// PRIVATE PROPERTIES
		private $view;
		
		
		// CONSTRUCTOR
		function __construct($v) {
			$this->view = $this->getViewURI($v);
		}
		
		// PRIVATE HELPER METHODS
						
		// echos an html file with replaced html special characters to prevent html injection
		private function safeEcho($htmlFile) {
			htmlspecialchars(include($htmlFile));
		}
		
		// gets the URI of the view from the "load" GET parameter
		private function getViewURI($urlParam) {
			switch ($urlParam) {
				// if there is no passed parameter, assume home
				case "home":
				case "":
					return Views::HOME;
					break;
				case "settings":
					return Views::SETTINGS;
					break;
				case "users":
					return Views::USERS;
					break;
				case "reports":
					return Views::REPORTS;
					break;
				case "donate":
					return Views::DONATE;
					break;
				default:
					// if a proper view isn't selected, for instance if someone is trying to monkey with sending GET parameters they shouldn't
					// then return the 404 page
					return Views::FOUROHFOUR;
					break;				
			}
		}
		
		
		
		// PUBLIC METHODS
				
		// provides an easy way to load a view
		// loads the template files and loads the file at the provided URI in the <div id="content"> section
		// uses $this->safeEcho() to prevent html injection
		public function loadView() {
			if (!isset($this->view)) {
				// ERROR HANDLING
				die("Page not set");
			}
			else {
				// load the top part of the template html
				$this->safeEcho(Templates::TEMPLATE_TOP);
				// load the content html
				$this->safeEcho($this->view);;
				// load the bottom part of the template html
				$this->safeEcho(Templates::TEMPLATE_BOTTOM);
			}
		}
		
		
	} // end class MEOLoadViews




?>