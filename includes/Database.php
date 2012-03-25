<?php

class Database
{
	protected $_con;

	protected function connect()
	{
		$this->_con = mysql_connect("localhost","root","eagles7");
		if (!$con) {
		  die('Could not connect: ' . mysql_error());
		}
	}

	protected function close()
	{
		mysql_close($this->_con);
	}

}
