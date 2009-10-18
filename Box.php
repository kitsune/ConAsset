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
 * Box.php
 * Handles the entries for the box table, including loading from the database
 * and inserting/updating the database.
 * */

class Box  {

	private $barcode = '';
	private $description = '';
	private $location = 0;
	
	function __construct($connection){
		$this->connection = $connection;
		if (isset($_SESSION['location'])) {
			$this->location = $_SESSION['location'];
		}
	}
	
	public function getBarcode() {
		return $this->barcode;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getLocation() {
		return $this->location;
	}
	
	public function setBarcode($barcode) {
		$this->barcode = $barcode;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function setLocation($location) {
		$this->location = $location;
	}
	
	private function init($row) {
		$this->barcode = $row['b_barcode'];
		$this->description = $row['b_description'];
		$this->location = $row['b_location'];
	}
	
	public function loadFromPage() {
		if(isset($_POST['barcode'])) {
			$this->barcode = $_POST['barcode'];
		}
		if(isset($_POST['description'])) {
			$this->description = $_POST['description'];
		}
		if(isset($_POST['location'])) {
			$this->location = $_POST['location'];
		}
	}
	
	public function printForm($action) {
		$query = "
		SELECT l_index, l_name
		FROM locations;";
		$this->connection->query($query);
		echo "<center>
		<form action=\"index.php?action=$action&type=box\" method=\"post\" enctype=\"multipart/form-data\">
		Barcode: <br>
		<input type=\"text\" name=\"barcode\" value=\"$this->barcode\"> <br>
		Description: <br>
		<textarea name=\"description\" rows=\"10\" cols=\"60\">$this->description</textarea> <br>
		Location: <br>
		<select name=\"location\"";
		//list conditions
		while($row = $this->connection->fetch_row())
		{
			$index = $row[0];
			$name = $row[1];
			if($index == $this->location)
			{
				echo "<option value=\"$index\" selected=\"yes\">$name</option>";
			} else {
				echo "<option value=\"$index\">$name</option>";
			}
		}
		echo "</select> <br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</center>";
	}
	
	public function insert() {
		$query = "
		INSERT INTO boxes (b_barcode, b_description, b_location)
		VALUES ('$this->barcode', '$this->description', $this->location);
		";
		$this->connection->query($query);
		//set the new box to be the box that we are working with
		$_SESSION['box'] = $this->barcode;
		
		//do more adaptive learning magic
		if(!isset($_SESSION['location'])) {
			$_SESSION['location'] = $this->location;
		} else {
			//threshhold of 2
			if(isset($_SESSION['lastlocation'])) {
				if($this->location == $_SESSION['lastlocation']) {
					$_SESSION['location'] = $this->location;
				} else {
					$_SESSION['lastlocation'] = $this->location;
				}
			} else {
				$_SESSION['lastlocation'] = $this->location;
			}
		}
	}
	
	public function update() {
		$query = "
		UPDATE boxes 
		SET b_barcode = $this->barcode, b_description = $this->description , b_location = $this->location
		WHERE b_barcode = $this->barcode;
		";
		$this->connection->query($query);
	}
	
	public function loadEntry($barcode) {
		$query = "
		SELECT b_barcode, b_description, b_location
		FROM boxes
		WHERE b_barcode = $barcode";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}
}
 
?>
