<?php
    /* MEO Database class
	 * 
	 * Usage:
	 * - for a SELECT (read-only) query:
	 * - create an instance of the class and connect to the database
	 *     $db = new MEODB(DBPrivileges::READ);
	 * - execute a one-off SELECT query:
	 *     $db->select("* FROM `churches` WHERE churchname='St Luke'");
	 * - show data from the records
	 *     if($db->result->num_rows > 0) {
	 *         while($row = $db->result->fetch_assoc()) {
	 *             echo stripslashes($row['churchname']);
	 *         }
	 *     }
	 * - close connection
	 *     $db->closeConnection();
	 * 
	 * 
	 * - for an INSERT (write) query:
	 * - create an instance of the class and connect to the database
	 *     $db = new MEODB(DBPrivileges::WRITE);
	 * - create an instance of the DBParameters class to pass in the query
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 * 
	 */
    final class DBPrivileges {
    	const READ = 0;
		const WRITE = 1;
		const NONE = 2;
    }
	
	final class DBParamsDataTypes {
		const INTEGER = "i";
		const DOUBLE = "d";
		const STRING = "s";
		const BLOB = "b";
	}
	
	/* maybe need this
	 * 
	 * usage:
	 * - create an instance and use the add method to populate
	 *     $params = new BindParameters();
	 *     $params->add(DBParamsDataTypes::STRING, 'abcd');
	 *     $params->add(DBParamsDataTypes::INTEGER, 12);
	 *     $params->add(DBParamsDataTypes::DOUBLE, 1.6);
	 * 
	 * - when ready to execute, use the get method with call_user_func_array to bind
	 *     call_user_func_array( array($this->statement, 'bind_param'), $params->get());
	 * 
	 *     or?
	 *     call_user_func_array( $this->statement->bind_param, $params->get());
	 * 
	 * 
	 */
	class BindParameters {
		private $values = array();
		private $types = "";
		
		public function add($type, &$value) {
			$this->values[] = $value;
			$this->types .= $type;
		}
		
		public function get() {
			return array_merge(array($this->types), $this->values);
		}
	}
    
    class MEODB {
    	// private constants
    	private $dbServer = "localhost";
		private $dbUserRead = "meo_read";
		private $dbPassRead = "meo_read";
		private $dbUserWrite = "meo_write";
		private $dbPassWrite = "meo_write";
		private $dbName = "myeoffering";
		
		// private properties
		private $privileges;
		private $connection;
		private $statement;
		private $paramCount;
		
		// public properties
		public $result;
		
		function __construct($priv) {
			$this->privileges = $priv;
			$this->startConnection();
		}
		
		
		// public methods
		public function closeConnection() {
			// clean up the statment if using prepared statement (parameterized query)
			if (isset($this->statement)) {
				$this->statement->close();
			}
			if (isset($this->connection)) {
				$this->connection->close();
			}
		}
		
		
		// private methods
		private function startConnection() {
			if (!($this->privileges == DBPrivileges::NONE)) {
				if ($this->privileges == DBPrivileges::READ) {
					$this->connection = new mysqli($this->dbServer, $this->dbUserRead, $this->dbPassRead, $this->dbName);
				}
				else if ($this->privileges == DBPrivileges::WRITE) {
					$this->connection = new mysqli($this->dbServer, $this->dbUserWrite, $this->dbPassWrite, $this->dbName);
				}
				else {
					HandleErrors::showError("Unknown Database Permissions");
				}
			
				if (mysqli_connect_errno()) {
					HandleErrors::showError("Failed to connect to database: " . mysqli_connect_error());
				}
			}
			else {
				HandleErrors::showError("No database privileges");	
			}
		}
    
		
		// QUERY METHODS:
		
		/* single select query
		 * - a one-off select query takes only 1 round-trip server<>db whereas a
		 *   prepared select takes 2
		 * 
		 * params:
		 * query: * FROM table WHERE (conditions) ORDER BY order LIMIT limit
		 * 
		 * returns:
		 * mysql result set
		 * 
		 * using results:
		 * if ($result->num_rows > 0) {
		 *     while($row = $result->fetch_assoc()) {
		 *         echo stripslashes($row['username']);
		 *     }
		 * }
		 */
		public function select($query) {
			if (!isset($query)) {
				HandleErrors::showError("Tried to run selectOneRow() without a query");
			}
			elseif (!isset($this->connection)) {
				HandleErrors::showError("Tried to run selectOneRow() without a connection");
			}
			else {
				$this->result = $this->connection->query("SELECT " . $query) or
					HandleErrors::showError("ERROR: SELECT: " . $this->connection->error.__LINE__);
			}
		}
		
		
		
		/* creates a prepared statement with the supplied query
		 * 
		 * syntax:
		 * $query = "INSERT INTO table(col1, col2, ... coln) VALUES (?, ?, ... ?)";
		 */
		public function setParameterizedQuery($query) {
			if (!isset($query)) {
				HandleErrors::showError("Tried to set an empty parameterized query");
			}
			else if (!isset($this->connection)) {
				HandleErrors::showError("Tried to set a parameterized query without a connection");
			}
			else {
				if (!($this->statement = $this->connection->prepare($query))) {
					HandleErrors::showError("Failed to set parameterized query: " . $this->connection->errno . ": " . $this->connection->error);
				}
				else {
					$this->paramCount = substr_count($query, "?");
				}					
			}
		}
		
		
		/* binds values to the parameterized query
		 * 
		 * syntax:
		 * $db->setParameterValues(DBParamsDataTypes::STRING, "abc", DBParamsDataTypes::DOUBLE, 0.25);
		 */
		public function setParameterValues() {
			$numArgs = func_num_args();
			
			if (($numArgs == 0) && !($this->paramCount == 0)) {
				HandleErrors::showError("Tried to bind zero parameters but needed " & $this->paramCount);
			}
			// number of parameters must be divisble by two, or we aren't properly
			// setting data types for each parameter
			else if (!($numArgs %2 == 0)) {
				HandleErrors::showError("Invalid syntax when binding parameters.");
			}
			
			// number of parameters is divisble by two
			// so try binding each parameter
			else {
				$args = func_get_args();
				$dataTypesString = "";
				$params = array();
				// step through every other argument
				// $args[$i] = data type
				// $args[$i+1] = parameter
				for ($i=0; $i < $numArgs; $i+=2) {
					// is this a valid data type? should be 1 character
					if ( (strlen($args[$i]) < 1) || (strlen($args[$i]) > 1)) {
						HandleErrors::showError("Invalid syntax - not a valid data type");
					}
					else {
						//$dataTypesString = $dataTypesString . $args[$i];
						//$params[$i/2] = $args[$i+1];
						
						// try binding on the fly
						$this->statement->bind_param($args[$i], $args[$i+1]);
					}
				}
				
				// now we have a string of the data types and an array of the parameters
				// so go ahead and bind them
				
			}
		}
		
		public function insert() {
			$this->statement->execute();
			return $this->statement->affected_rows;
		}
		
	}

	/*
	// variable number of arguments
	function takeArgs() {
		// make sure there are arguments
		if (func_num_args() > 0) {
			$args = func_get_args();
			foreach ($args as $arg) {
				// do something with each argument
			}
		}
		// else there are no arguments to process
		else {
			HandleErrors::showError("No arguments");
		}
	}
*/	
	
	
	
/*  TRANSACTION TYPE		PREPARED			NON-PREPARED
 * ------------------------------------------------------------
 *  SINGLE-SELECT			2 round-trips		1 round-trip
 * 
 *  n-SELECTS				n+1 round-trips		n round-trips
 * 
 *  SINGLE-INSERT			2 round-trips		1 round-trip
 * 
 *  n-INSERTS				n+1 round-trips		n round-trips (multi-insert= 1 round-trip)
 * 
 *
 *  buffered results		mysqli_stmt_get_result()	mysqli_query()
 * 
 *  unbuffered				output binding api	mysql_reql_query()+mysql_use_result()
 * 
 * 
 */	
	
	
	
?>