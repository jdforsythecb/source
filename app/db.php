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
	 * - create the sql query with ? placeholders
	 *     $query = "INSERT INTO `churches` VALUES (?,?,?)";
	 * - set the parameterized query
	 *     $db->setParameterizedQuery($query);
	 * - set the parameter values
	 *     $db->addParamValue(DBParamsDataTypes::INTEGER, 2);
	 *     $db->addParamValue(DBParamsDataTypes::STRING, "abc");
	 *     $db->addParamValue(DBParamsDataTypes::DOUBLE, 2.25);
	 * - run the insert query, which returns the number of affected rows
	 *     $affectedRows = $db->insert();
	 * - close connection
	 *     $db->closeConnection();
	 * 
	 * 
	 * 
	 */
	 
	 
	/* ENUMS
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
    
	final class DBQueryTypes {
		const INSERT = "INSERT";
		const DELETE = "DELETE";
		const UPDATE = "UPDATE";
		const SELECT = "SELECT";
	}
	
    class MEODB {
    	/* PRIVATE CONNECTION CONSTANTS
		 * 
		 * used to hold the server, database, and login information
		 * 
		 */
    	private $dbServer = "localhost";
		private $dbName = "myeoffering";
		// read-only db user credentials (select only)
		private $dbUserRead = "meo_read";
		private $dbPassRead = "meo_read";
		// write-only db user credentials (insert, delete, update only)
		private $dbUserWrite = "meo_write";
		private $dbPassWrite = "meo_write";
		
		/* PRIVATE PROPERTIES
		 * 
		 */
		// the privileges (read/write) for the current query
		private $privileges;
		// holds the db connection
		private $connection;
		// holds the prepared statement
		private $statement;
		// holds an array of parameter values
		private $paramValues;
		// holds a string of the data types of the parameters
		private $paramDataTypesString;
		
		/* PUBLIC PROPERTIES
		 * 
		 */
		
		
		/* CONSTRUCTOR
		 * 
		 * requires setting the privilege level when creating an instance
		 * also automatically creates a connection
		 * 
		 */
		function __construct($priv) {
			$this->privileges = $priv;
			$this->startConnection();
		}
		
		
		/* PRIVATE METHODS
		 * 
		 * helpers for the class
		 * 
		 */
		 
		// creates a db connection with the selected privileges/login and handles any errors related to the connection
		private function startConnection() {
			if (!($this->privileges == DBPrivileges::NONE)) {
				if ($this->privileges == DBPrivileges::READ) {
					$this->connection = new mysqli($this->dbServer, $this->dbUserRead, $this->dbPassRead, $this->dbName);
				}
				else if ($this->privileges == DBPrivileges::WRITE) {
					$this->connection = new mysqli($this->dbServer, $this->dbUserWrite, $this->dbPassWrite, $this->dbName);
				}
				else {
					HandleErrors::consoleLog("Unknown Database Permissions");
				}
			
				if (mysqli_connect_errno()) {
					HandleErrors::consoleLog("Failed to connect to database: " . mysqli_connect_error());
				}
			}
			else {
				HandleErrors::consoleLog("No database privileges");	
			}
		}
		
		// turns the parameter values into an array of references and binds them to the prepared statement
		// called when executing a prepared query
		private function doBind() {
			// make an array of references from the array of values
			$refs = array();
			$arr = array_merge(array($this->paramDataTypesString), $this->paramValues);
			foreach($arr as $key => $value) {
				$refs[$key] = &$arr[$key];
			}
			// call bind_param() to bind to the prepared statement
			return call_user_func_array(array($this->statement, 'bind_param'), $refs);
		}
		
		// executes a write query (insert, delete, update) that is using a prepared statement
		// $queryType is of enum DBQueryTypes::INSERT, ::DELETE, or ::UPDATE
		private function executeWriteQuery($queryType) {
			// if there's no statement prepared...
			if (!isset($this->statement)) {
				HandleErrors::consoleLog("Tried to execute " . $queryType . " query without a prepared statement");
			}
			
			// if there are no parameters set...
			else if (!isset($this->paramValues) || ($this->paramValues == array()) ||
			    !isset($this->paramDataTypesString) || ($this->paramDataTypesString == "")) {
			    	HandleErrors::consoleLog("Tried to execute " . $queryType . " query without any parameters");
			}
				
			// if there's no open connection
			else if (!isset($this->connection)) {
				HandleErrors::consoleLog("Tried to execute " . $queryType . " query without a connection.");
			}
			
			// otherwise we can try binding
			else {
				// bind the parameters
				$this->doBind() or HandleErrors::consoleLog("Failed to bind parameters to the " . $queryType . " query");
				
				// execute the prepared statement
				$this->statement->execute();

				// if there was an error executing...
				if (!($this->statement->errno == 0)) {
					HandleErrors::consoleLog('Failed to execute the ' . $queryType . ' query. Error: ' . $this->statement->error);
				}
				
				// otherwise return the number of rows affected
				else {
					return $this->statement->affected_rows;
				}
			}
		}
		
		
		
		/* PUBLIC METHODS
		 * 
		 */
		
		// close the db connection
		public function closeConnection() {
			// clean up the statment if using prepared statement (parameterized query)
			if (isset($this->statement)) {
				$this->statement->close();
			}
			// close the connection if it exists
			if (isset($this->connection)) {
				$this->connection->close();
			}
		}
		
		
		// creates a prepared statement with the supplied query
		//
		//     $query = "INSERT INTO table(col1, col2, ... coln) VALUES (?, ?, ... ?)";
		public function setParameterizedQuery($query) {
			// if no query is set...
			if (!isset($query)) {
				HandleErrors::consoleLog("Tried to set an empty parameterized query");
			}
			
			// if there's no open connection...
			else if (!isset($this->connection)) {
				HandleErrors::consoleLog("Tried to set a parameterized query without a connection");
			}
			
			// otherwise we can prepare the statement
			else {
				// if preparing the statement fails...
				if (!($this->statement = $this->connection->prepare($query))) {
					HandleErrors::consoleLog("Failed to set parameterized query: " . $this->connection->errno . ": " . $this->connection->error);
				}					
			}
		}
		
		// allows adding parameter values one-at-a-time instead of in one fell swoop
		// first setParameterizedQuery() then addParamValue() for each parameter in the query
		// $dataType is of enum DBParamsDataTypes::INTEGER, ::STRING, ::DOUBLE, or ::BLOB
		public function addParamValue($dataType, $value) {
			// if we haven't yet added a parameter, initialize the array and string
			if (!isset($this->paramValues) || !isset($this->paramDataTypesString)) {
				$this->paramValues = array();
				$this->paramDataTypesString = '';
			}
			
			// add the value to the array ([] implies [len+1])
			$this->paramValues[] = $value;
			// append the data type character to the string
			$this->paramDataTypesString .= $dataType;
		}
		
		
		
		
    
		
		/* QUERY METHODS
		 * 
		 * allow you to run different types of queries
		 * 
		 */
		 
		// in general, we do not want to run prepared statement queries for select because
		// it always takes n+1 round-trips for a prepared statement compared to n for regular selects
		
		// so how to do this safely? only allow non-prepared selects for queries that are hard-coded
		// and don't take any user input, i.e. selects we know are safe from injection - and require
		// prepared selects (with their extra bandwidth and memory cost) for any queries including
		// user input
		
		// for selects that are hard-coded and thus safe from injection attacks (sets result to $this->result)
		public function safeSelect($query) {
			// if no query is passed...
			if (!isset($query)) {
				HandleErrors::consoleLog("Tried to run safeSelect() without a query");
			}
			
			// if there's no open connection...
			else if (!isset($this->connection)) {
				HandleErrors::consoleLog("Tried to run safeSelect() without a connection");
			}
			
			// otherwise we can try the query
			else {
				$this->result = $this->connection->query(DBQueryTypes::SELECT . " " . $query) or
					HandleErrors::consoleLog("ERROR: safeSelect(): query: " . DBQueryTypes::SELECT . " " . $query . "\nError: " . $this->connection->error.__LINE__);
			}
		}
		
		// abstracts $this->executeWriteQuery() for INSERT
		// returns number of rows affected
		public function insert() {
			return $this->executeWriteQuery(DBQueryTypes::INSERT);
		}
		
		// abstracts $this->executeWriteQuery() for DELETE
		// returns number of rows affected
		public function delete() {
			return $this->executeWriteQuery(DBQueryTypes::DELETE);
		}
		
		// abstracts $this->executeWriteQuery() for INSERT
		// returns number of rows affected
		public function update() {
			return $this->executeWriteQuery(DBQueryTypes::UPDATE);
		}
		
		
		
		
	}

	
	
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