<?php

require_once dirname(__FILE__) . '/Event.php';

class Database extends Event
{
	private $_host = 'localhost';
	private $_database = 'ad2orlando';
	private $_username = 'root';
	private $_password = 'test1234';

	public function select($id = null) 
	{
		$con = mysql_connect($this->_host, $this->_username, $this->_password);
		if (!$con) {
		  die('Could not connect: ' . mysql_error());
		}

		mysql_select_db($this->_database, $con);

		if(!empty($id)) {
			$sql = "SELECT * FROM events WHERE id = $id";
		} else {
			$sql = "SELECT * FROM events ORDER BY date DESC";
		}

		//Submit INSERT query
		$result = mysql_query($sql);

		//Close database connection
		mysql_close($con);

		return $result;
	}

	public function insert($values) 
	{
		//Connect to the database and select the table
		$con = mysql_connect($this->_host, $this->_username, $this->_password);
		if (!$con) {
		  die('Could not connect: ' . mysql_error());
		}

		mysql_select_db($this->_database, $con);

		//Begin the insert sql string
		$sql = "INSERT INTO events ";

		//Loop through values array
		foreach($values as $key => $value) {
			if(!empty($value)) {
				$escaped = mysql_real_escape_string($value);
				//Create two seperate strings for columns and values
				$sql_column .= "$key, ";
				$sql_value  .= "'$escaped', ";
			}
		}

		//Remove extra comma and space from the end of the column/value strings
		$sql_column = preg_replace('/, $/', '', $sql_column);
		$sql_value = preg_replace('/, $/', '', $sql_value);

		//Add column/values to the final sql string
		$sql .= "($sql_column) VALUES ($sql_value)";

		//Submit INSERT query
		mysql_query($sql);

		//Close database connection
		mysql_close($con);		
	}

	public function update($values, $id) 
	{
		//Connect to the database and select the table
		$con = mysql_connect($this->_host, $this->_username, $this->_password);
		if (!$con) {
		  die('Could not connect: ' . mysql_error());
		}

		mysql_select_db($this->_database, $con);

		//Begin the insert sql string
		$sql = "UPDATE events SET ";

		//Loop through values array
		foreach($values as $key => $value) {
			if(!is_null($value)) {
				$escaped = mysql_real_escape_string($value);
				//Create two seperate strings for columns and values
				$sql_set .= "$key='$escaped', ";
			}
		}

		//Remove extra comma and space from the end of the column/value strings
		$sql_set = preg_replace('/, $/', '', $sql_set);

		//Add column/values to the final sql string
		$sql .= "$sql_set WHERE id=$id";

		//Submit INSERT query
		mysql_query($sql);

		//Close database connection
		mysql_close($con);		
	}

	public function delete($id) 
	{
		$con = mysql_connect($this->_host, $this->_username, $this->_password);
		if (!$con) {
		  die('Could not connect: ' . mysql_error());
		}

		mysql_select_db($this->_database, $con);

		$sql = "DELETE FROM events WHERE id = $id";

		//Submit INSERT query
		mysql_query($sql);

		//Close database connection
		mysql_close($con);
	}
}
