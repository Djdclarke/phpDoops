<?php

	class DB {

		protected $connection; 
		
		/* Connect to the DB */ 
		function Connect($username = 'root',$password = '',$db = 'testdb',$host = 'localhost') {
			$this->connection = new mysqli($host, $username, $password, $db );
			if (mysqli_connect_errno()) {
			  $this->Error('Connect failed: '. mysqli_connect_error());
			}
		}
		
		/* Handle MySQL Errors */
		function Error($error) {
			echo "<PRE><strong>MYSQL ERROR: </strong>" . $error ."</PRE>"; 
		}
		
		/* Function to Insert Values to a DB */ 
		function Insert($data,$table,$debug = false) { 
			
			// Clean up the SQL to make it a bit more safe! 
			foreach($data as $key => $value) { 
				$data[$key] = mysql_real_escape_string($value); 
			}
			// Then emplode the array on a sprinf. 
			$sql = sprintf('INSERT INTO '.$table.' (%s) VALUES ("%s")', implode(',',array_keys($data)), implode('","',array_values($data)));
			$result = $this->Query($sql,false);
			// Return the Insert_ID
			return $this->connection->insert_id; 
		}
	
		
		/* Delete from ROW */ 
		function Delete($key,$uid,$table) {
			$sql = "DELETE FROM ".$table." WHERE  ".$key." = '".$uid."'"; 
			$result = $this->Query($sql,false); 
		}
		
		/* Changed to use Global Query Var */
		function Update($data,$where,$whereval,$table) { 

			$update = array(); 
			// Create the Update String
			foreach($data as $key => $value) { 
				$value = mysql_real_escape_string($value); 
				$update[$key] = "$key = '$value'"; 
			}
			$sql = implode(", ",$update); 
			$sql = "UPDATE ".$table." SET ".$sql." WHERE ".$where." = '".$whereval."'"; 
			$result = $this->Query($sql,false);
			// Return affected rows. 
			return $result; 
		}
		
		/* 
		Return row count for last DB Query
		 - Converted to MySQLi 17/06/2012
		 - Changed to use the Query function in this class
	    */
		function Rowcount() { 
			$Result = $this->Query("SELECT FOUND_ROWS() as Row;"); 
			return $Result['0']['Row']; 
		}
		
		/* Run and return a Query Var - Converted to MySQLi 17/06/2012 */ 
		function Query($Query,$Return = true) {
			// Set some return vars
			$output; 
			// Run the query using the connection from before
			if ($result = $this->connection->query($Query)) {
				// Ensure some data has returned
				if (mysqli_affected_rows($this->connection) > 0 && $Return != false) { 
					// Loop through the DB results as we have some stored.
					while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC) ) {
						$output[] = $row; 	
					}
					return $output; 
					$result->close();
				} else { 
					// Return affected rows for non-selects; 
					return $this->connection->affected_rows;
				}
				/* free result set */
				
			} else { 
				$this->Error("MYSQL ERROR: ". mysqli_error($this->connection));
			}
		}
			
	}
	
	/* Example Usage 
		$DB = new DB_Management(); // Start the class up
		$DB->Connect('root','','testdb','localhost');  // Connect to the DB
		$DB->Query("SELECT * FROM permissions"); // Run a query (returns result)
		$DB->Rowcount(); // Get the DB total row count (returns int) 
		
		$data = array('T1' => 'Test1', 'T2' => 'Test2','T3' => 'Test3'); // Data for insert in associated array
		$DB->Insert($data,"Test"); // Insert with Data and table to insert to
		
		$DB->Update(DATA,'WHERE','VALUE','TABLE'); // T1 
	*/ 

?>