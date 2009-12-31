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

require_once('LogEntry.php');
require_once('User.php');

class Box {

	private $connection;
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
	
	/*
	private function init($row) {
		$this->barcode = $row['b_barcode'];
		$this->description = $row['b_description'];
		$this->location = $row['b_location'];
	}*/
	
	private function init($row) {
		$this->barcode = $row[0];
		$this->description = $row[1];
		$this->location = $row[2];
	}
	
	public function loadFromPage() {
		if(isset($_POST['barcode'])) {
			$this->barcode = $this->connection->validate_string($_POST['barcode']);
		}
		if(isset($_POST['description'])) {
			$this->description = $this->connection->validate_string($_POST['description']);
		}
		if(isset($_POST['location'])) {
			$this->location = $this->connection->validate_string($_POST['location']);
		}
	}
	
	public function printForm($action) {
		if($action == 'checkout'){
			return $this->printCOForm();
		}else if($action == 'view'){
			return $this->printViewForm();
		}else if($action == 'batchmove'){
			return $this->printBatchMoveForm();
		}
		$query = "
		SELECT l_index, l_name
		FROM locations;";
		$this->connection->query($query);
		echo "<center>
		<form action=\"index.php?action=$action&type=box\" method=\"post\" enctype=\"multipart/form-data\">
		Barcode: <br>";
		if($action == 'add'){
			echo "<input type=\"text\" name=\"barcode\" value=\"$this->barcode\"> <br>";
		}else {
			echo "<input type=\"hidden\" name=\"barcode\" value=\"$this->barcode\"> <br>";
			echo "$this->barcode<br>";
		}
		echo "Description: <br>
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
	//Print Checkout Form
	public function printCOForm(){
		echo "<center>
		Checkout Box<br>
		<form action=\"index.php?action=checkout&type=box\" method=\"post\" enctype=\"multipart/form-data\">
		Box Barcode: <br>
		<input type=\"text\" name=\"barcode\"> <br>
		Checkout to(Person Barcode): <br>
		<input type=\"text\" name=\"checkoutTo\"> <br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</form>
		</center> ";
	}
	public function printBatchMoveForm(){
		$checkoutTo = isset($_POST['checkoutTo'])?$_POST['checkoutTo']:'';
		$query = "
		SELECT l_index, l_name
		FROM locations;";
		$this->connection->query($query);
		echo '
			<script type="text/javascript">
			//<!--
			function addBarcode(){
				var opt = document.forms[0]["barcodes[]"].options;
				var txt = prompt("barcode","");
				if(txt != ""){
					opt[opt.length] = new Option(txt,txt);
				}
				return false;
			}
			function removeSelectedBarcode(){
				var sel = document.forms[0]["barcodes[]"];
				var i = sel.selectedIndex;
				sel.options[i] = null;
				//sel.options = sel.options.slice(0,i-1).concat(sel.options.slice(i+1));
				return false;
			}
			function subClicked(){
				var opt = document.forms[0]["barcodes[]"].options;
				for(var i = 0; i < opt.length; i+=1){
					opt[i].selected = true;
				}
			}
			//-->
			</script>
			';
		echo "<center>
		Move boxes<br>
		<form action=\"index.php?action=batchmove&type=box\" method=\"post\" enctype=\"multipart/form-data\">
		<select name=\"location\"";
		//list conditions
		while($row = $this->connection->fetch_row())
		{
			$index = $row[0];
			$name = $row[1];
			echo "<option value=\"$index\">$name</option>";
		}
		echo "</select> <br><br>
		boxes: <br>
		<select name=\"barcodes[]\" multiple=\"true\" style=\"width: 150px\" size=\"10\"></select><br>
		<button  onclick=\"return addBarcode()\">Add</button><br>
		<button  onclick=\"return removeSelectedBarcode()\">Remove</button><br>
		<input type=\"submit\" name=\"submit\" onclick=\"subClicked()\" value=\"Finished\">
		</form>
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
		$this->connection->query("begin;");
		$this->logUpdate();
		//b_barcode = '$this->barcode',
		$query = "
		UPDATE boxes 
		SET b_description = '$this->description' , b_location = $this->location
		WHERE b_barcode = $this->barcode;
		";
		$this->connection->query($query);
		$this->connection->query("commit;");
	}
	
	private function createLogEntry($type, $oldValue, $newValue) {
		$user = new User();
		$username = $user->get_Username();

		$logEntry = new LogEntry($this->connection);
		$logEntry->setBarcode($this->barcode);
		$logEntry->setPerson($username);
		$logEntry->setType($type);
		$logEntry->setOldValue($oldValue);
		$logEntry->setNewValue($newValue);
		$logEntry->insert();
	}
	
	private function logUpdate() {
		$query = "
		SELECT b_description, b_location
		FROM boxes
		WHERE b_barcode = '$this->barcode'";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		$desc = $this->connection->validate_string($row[0]);
		if($this->description != $desc) {
			$this->createLogEntry("Box Description Changed", 
				$desc, $this->description);
		}
		$loc = $this->connection->validate_string($row[1]);
		if($this->location != $loc) {
			$this->createLogEntry("Box Location Changed", 
				$loc, $this->location);
		}
	}
	
	public function loadEntry($barcode) {
		$query = "
		SELECT b_barcode, b_description, b_location
		FROM boxes
		WHERE b_barcode = '$barcode'";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}
	
	public function findBox($barcode) {
		$this->loadEntry($barcode);
		if($this->barcode == '') {
			echo "<br>Could not find item: $barcode<br><br>";
			return;
		}
		echo "Box barcode: $this->barcode ";
		echo "<a href=\"index.php?action=edit&type=box&barcode=$this->barcode\">edit</a> ";
		echo "<a href=\"index.php?action=edit&type=location&index=$this->location\">view location</a> ";
		echo "<a href=\"index.php?action=delete&type=box&barcode=$this->barcode\">delete</a> ";
	}
	
	public function deleteBox($barcode) {
		$this->connection->query("begin;");
		$query = "
		SELECT COUNT(a_barcode)
		FROM assets
		WHERE a_box = $barcode;";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		if($row[0] != 0) {
			echo "Cannot delete an Assest Type that is currently being used by assets<br><br>";
		} else {
			$query = "DELETE FROM boxes WHERE b_barcode = $barcode;";
			$this->connection->query($query);
			//log away
			$user = new User();
			$logEntry = new LogEntry($this->connection);
			$logEntry->setBarcode($barcode);
			$logEntry->setPerson($user->get_Username());
			$logEntry->setType("Box Deleted");
			$logEntry->insert();
		}
		
		$this->connection->query("commit;");
	}
	
	public function printFindForm() {
		echo "
		<form action=\"index.php?action=find&type=box\" method=\"post\" enctype=\"multipart/form-data\">
		Barcode to look for:
		<input type=\"text\" name=\"barcode\"><br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">";
	}

	public function listBoxes() {
		$query =  "
			SELECT b_barcode, b_description, l_name
			FROM boxes
			LEFT JOIN locations ON l_index = b_location
			ORDER BY `boxes`.`b_barcode` ASC
			";
		$this->connection->query($query);
		echo "<table>
		<caption>Listing All Boxes</caption>
		<tr><th>Barcode</th><th>Description</th><th>Location</th><td></td></tr>";
		while($row = $this->connection->fetch_row()){
			echo "<tr><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td><a href=\"index.php?action=view&type=box&barcode={$row[0]}\">view</a></td></tr>";
		}
		echo "</table>";
	}

	public function printViewForm(){
		$query = "
		SELECT l_name
		FROM locations WHERE l_index = $this->location;";
		$this->connection->query($query);
		$row=$this->connection->fetch_row();
		$location_name = $row[0];
			
		$query = "
			SELECT a_name, a_barcode, coalesce(p_name,'Nobody')
			FROM assets
			LEFT JOIN people on p_barcode = a_checkout_to
			WHERE a_box = '{$this->barcode}'";
		$this->connection->query($query);

		echo "<center>
		<a href=\"index.php?action=edit&type=box&barcode=$this->barcode\">Edit</a><br>
		Barcode: $this->barcode<br>
		Description:<br>
		$this->description<br>
		Location: $location_name<br>
		<table>
		<captions>Assets:</captions>
		<tr><th>Asset Name</th><th>Barcode</th><th>Checked out to</th><td></td></tr> ";
		while($row = $this->connection->fetch_row()){
			echo "
			<tr>
				<td>{$row[0]}</td><td>{$row[1]}</td>
				<td>{$row[2]}</td>
				<td><a href=\"index.php?action=edit&type=asset&barcode={$row[1]}\">edit</a></td>
			</tr>";
		}
		echo "
		</table>
		</center>";
	}
}
?>
