<?php
	// this is a set of logic that we (may) need globally available for use in the app
	
	
	// ERROR HANDLING CLASS
	final class HandleErrors {
		static public function showError($msg) {
			echo('<script type="text/javascript">console.log("' . $msg . '");</script>') or die($msg);
			exit();
		}
		
		static public function consoleLog($msg) {
			echo '<script type="text/javascript">console.log("' . $msg . '");</script>';
		}
	}

?>