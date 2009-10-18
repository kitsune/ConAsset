<?php
/* 
 * Copyright © 2009 Dylan Enloe
 * ALL RIGHTS RESERVED
 * 
 * This program is distributed under the terms of
 * version 3 of the GNU Lesser General Public License.
 * Please see the file LICENSE in this distribution
 * for licensing information.
 *
 * Location.php
 * Handles the entries for the location table, including loading from 
 * the database and inserting/updating the database.
 * */

class location {
	
	private $connection;
	private $index = 0;
	private $name = '';
	private $location = '';
	
	function __construct($connection){
		$this->connection = $connection;
	}
	
	public function getIndex() {
		return $this->index;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getlocation() {
		return $this->location;
	}
	
	public function setIndex($index) {
		$this->index = $index;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function setlocation($location) {
		$this->location = $location;
	}
	
	/*
	private function init($row) {
		$this->index = $row['l_index'];
		$this->name = $row['l_name'];
		$this->location = $row['l_location'];
	} */
	
	private function init($row) {
		$this->index = $row[0];
		$this->name = $row[1];
		$this->location = $row[2];
	}
	
	public function loadFromPage() {
		if(isset($_POST['name'])) {
			$this->name = $this->connection->validate_string($_POST['name']);
		}
		if(isset($_POST['location'])) {
			$this->location = $this->connection->validate_string($_POST['location']);
		}
	}
	
	public function printForm($action) {
		echo "<center>
		<form action=\"index.php?action=$action&type=location\" method=\"post\" enctype=\"multipart/form-data\">
		Name: <br>
		<input type=\"text\" name=\"name\" value=\"$this->name\"> <br>
		Location: <br>
		<textarea name=\"location\" rows=\"10\" cols=\"60\">$this->location</textarea> <br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</center>";
	}
	
	public function insert() {
		$query = "
		INSERT INTO locations (l_name, l_location)
		VALUES ('$this->name', '$this->location');
		";
		$this->connection->query($query);
		$index = $this->connection->get_insert_ID();
		//set the new location to be the location we are working with
		$_SESSION['location'] = $index[0];
	}
	
	public function update() {
		$query = "
		UPDATE locations 
		SET l_name = '$this->name' , l_location = '$this->location'
		WHERE l_index = $this->index;
		";
		$this->connection->query($query);
	}
	
	public function loadEntry($index) {
		$query = "
		SELECT l_index, l_name, l_location
		FROM locations
		WHERE l_index = $index";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}
}

?>
