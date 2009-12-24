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
 * Asset.php
 * Handles the entries for of type asset, including loading from the database
 * and inserting/updating the database.
 * */

require_once('LogEntry.php');
require_once('User.php');

class Asset {

	private $connection;
	private $barcode  = '';
	private $name = '';
	private $description = '';
	private $condition = 1;
	private $checkoutTo = '';
	private $box = '';
	private $itemType = '';

	function __construct($connection){
		$this->connection = $connection;
		
		if (isset($_SESSION['itemtype'])){
			$this->itemType = $_SESSION['itemtype'];
		}
		if (isset($_SESSION['box'])) {
			$this->box = $_SESSION['box'];
		}
	}
	
	public function getBarcode(){
		return $this->barcode;
	}

	public function getName(){
		return $this->name;
	}

	public function getDescription(){
		return $this->description;
	}

	public function getCondition(){
		return $this->condition;
	}
	
	public function getCheckoutTo(){
		return $this->checkoutTo;
	}
	
	public function getBox(){
		return $this->box;
	}
	
	public function getItemType(){
		return $this->itemType;
	}
	
	public function setBarcode($barcode){
		$this->barcode = $barcode;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function setDescription($description){
		$this->description = $description;
	}
	
	public function setCondition($condition){
		$this->condition = $condition;
	}
	
	public function setCheckoutTo($checkoutTo){
		$this->checkoutTo = $checkoutTo;
	}

	public function setBox($box){
		$this->box = $box;
	}
	
	public function setItemType($itemType){
		$this->itemType = $itemType;
	}

	/**
	 * Returns the string representation of this obbject
	 * @return String repesentation ofAssets
	 */
	public function toString(){
	  $s = '';
	  $s .= 'a_barcode: '.$this->barcode;
	  $s .= 'a_name: '.$this->name;
	  $s .= 'a_description: '.$this->description;
	  $s .= 'a_condition: '.$this->condition;
	  $s .= 'a_checkout_to: '.$this->checkoutTo;
	  $s .= 'a_box: '.$this->box;
	  $s .= 'a_item_type: '.$this->itemType;
	  return $s;
	}
	
	/*
	private function init($row){
		$this->barcode = $row['a_barcode'];
		$this->name = $row['a_name'];
		$this->description = $row['a_description'];
		$this->condition = $row['a_condition'];
		$this->checkoutTo = $row['a_checkout_to'];
		$this->box = $row['a_box'];
		$this->itemType = $row['a_item_type'];
	}*/
	
	private function init($row){
		$this->barcode = $row[0];
		$this->name = $row[1];
		$this->description = $row[2];
		$this->condition = $row[3];
		$this->checkoutTo = $row[4];
		$this->box = $row[5];
		$this->itemType = $row[6];
	}

	public function loadFromPage(){
		if (isset($_POST['barcode'])){
			$this->barcode = $this->connection->validate_string($_POST['barcode']);
		}
		if (isset($_POST['name'])){
			$this->name = $this->connection->validate_string($_POST['name']);
		}
		if (isset($_POST['description'])){
			$this->description = $this->connection->validate_string($_POST['description']);
		}
		if (isset($_POST['condition'])){
			$this->condition = $this->connection->validate_string($_POST['condition']);
		}
		if (isset($_POST['checkoutTo'])){
			$this->checkoutTo = $this->connection->validate_string($_POST['checkoutTo']);
		}
		if (isset($_POST['box'])) {
			$this->box = $this->connection->validate_string($_POST['box']);
		}
	}

	/**
	 * Create a form for inputing the fields
	 */
	public function printForm($action) {
		if($action == 'checkout'){
			return $this->printCOForm();
		}else if($action == 'checkin'){
			return $this->printCIForm();
		} else if($action == 'checkin2'){
			return $this->printCIFormP2();
		}
		//make sure the is a valid item type and get it's name
		if($this->itemType == '') {
			echo "You must select an asset type before you can add items
			<br><br>";
			return;
		}
		$query = "
		SELECT at_name
		FROM asset_types
		WHERE at_index = $this->itemType";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		$itemTypeName = $row[0];
		$query = "
		SELECT c_index, c_value
		FROM conditions;";
		$this->connection->query($query);

		echo "<center>
		<form action=\"index.php?action=$action&type=asset\" method=\"post\" enctype=\"multipart/form-data\">
		Barcode: <br>
		<input type=\"text\" name=\"barcode\" value=\"$this->barcode\"> <br>
		Name: <br>
		<input type=\"text\" name=\"name\" value=\"$this->name\"> <br>
		Description: <br>
		<textarea name=\"description\" rows=\"10\" cols=\"60\">$this->description</textarea> <br>
		Box: <br>
		<input type=\"text\" name=\"box\"value=\"$this->box\"> <br>
		Checked out to(Leave blank if no one): <br>
		<input type=\"text\" name=\"checkoutTo\" value=\"$this->checkoutTo\"> <br>
		Condition: <br>
		<select name=\"condition\"";
		//list conditions
		while($row = $this->connection->fetch_row())
		{
			$index = $row[0];
			$value = $row[1];
			if($index == $this->condition)
			{
				echo "<option value=\"$index\" selected=\"yes\">$value</option>";
			} else {
				echo "<option value=\"$index\">$value</option>";
			}
		}
		echo "</select> <br>
		The item type that will be used is: $itemTypeName<br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</center>";
		
	}	
	//print Checkout Form
	public function printCOForm(){
		$checkoutTo = isset($_POST['checkoutTo'])?$_POST['checkoutTo']:'';
		echo "<center>
		Checkout Asset<br>
		<form action=\"index.php?action=checkout&type=asset\" method=\"post\" enctype=\"multipart/form-data\">
		Asset Barcode: <br>
		<input type=\"text\" name=\"barcode\"> <br>
		Checkout to (Person Barcode): <br>
		<input type=\"text\" name=\"checkoutTo\" value=\"$checkoutTo\"> <br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</form>
		</center";
	}

	public function printCIForm(){
		echo "<center>
		Checkin Asset<br>
		<form action=\"index.php?action=checkin&type=asset\" method=\"post\" enctype=\"multipart/form-data\">
		Asset Barcode: <br>
		<input type=\"text\" name=\"barcode\"> <br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</form>
		</center";
	}

	public function printCIFormP2(){
		$the_box = new Box($this->connection);
		$the_box->loadEntry($this->box);
		echo "<center>
		Checkin Asset<br>
		<form action=\"index.php?action=checkin2&type=asset\" method=\"post\" enctype=\"multipart/form-data\">
		Asset Barcode: <br>
		<input type=\"text\" name=\"barcode\"> <br>
		Current Box Name: {$the_box->getDescription()}<br>
		Box Barcode: <br>
		<input type=\"text\" name=\"box\" value=\"{$this->box}\"><br>
		<input type=\"submit\" name=\"submit\" value=\"Confirm Box\">
		</form>
		</center";
	}

	/**
	 * Insert this object into the DB
	 * @return new id (auto increment value) genereated
	 */

	public function insert() {
		$this->connection->query("begin;");
		$list = array("a_barcode"=>$this->barcode, "a_name"=>$this->name, "a_description"=>$this->description, "a_condition"=>$this->condition, "a_checkout_to"=>$this->checkoutTo, "a_box"=>$this->box, "a_item_type"=>$this->itemType);
		$sql = "insert into assets values (";
		foreach ($list as $key => $value){
			if(is_string($value)) {
				$sql .= "'$value', ";
			} else {
				$sql .= "$value, ";
			}
		}
		$sql = substr($sql, 0, -2).")";
		$this->connection->query($sql);
		
		//do a little adaptive learning magic
		if(!isset($_SESSION['box'])) {
			$_SESSION['box'] = $this->box;
		} else {
			//threshhold of 2
			if(isset($_SESSION['lastbox'])) {
				if($this->box == $_SESSION['lastbox']) {
					$_SESSION['box'] = $this->box;
				} else {
					$_SESSION['lastbox'] = $this->box;
				}
			} else {
				$_SESSION['lastbox'] = $this->box;
			}
		}
		
		//handle the logging of the add
		$user = new User();
		$logEntry = new LogEntry($this->connection);
		$logEntry->setBarcode($this->barcode);
		$logEntry->setPerson($user->get_Username());
		$logEntry->setType("Asset Creation");
		$logEntry->insert();
		$this->connection->query("commit;");
	}
	/**
	 * Update this object into the DB
	 * @return number of updated records
	 */
	public function update() {
		$this->connection->query("begin;");
		$this->logUpdate();	
		$list = array("a_barcode"=>$this->barcode, "a_name"=>$this->name, "a_description"=>$this->description, "a_condition"=>$this->condition, "a_checkout_to"=>$this->checkoutTo, "a_box"=>$this->box, "a_item_type"=>$this->itemType);
		$sql = "update assets set ";
		foreach ($list as $key => $value) {
			if(is_string($value)) {
				$sql .= "$key='$value', ";
			} else {
				$sql .= "$key=$value, ";
			}
		}
		$sql = substr($sql, 0, -2)." where `a_barcode`='$this->barcode'";
		$this->connection->query($sql);
		$this->connection->query("commit;");
	}
	
	private function logUpdate() {
		//todo: this could probably a stored proceedure of some kind
		//get what is currently in the system and search for differences
		$query = "
		SELECT a_name, a_description, a_condition, a_checkout_to, a_box, a_item_type
		FROM assets
		WHERE a_barcode = '$this->barcode'";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		$user = new User();
		$username = $user->get_Username();
		if($this->name != $row[0]) {
			$this->createLogEntry($username, "Asset Name Changed", $row[0],
				$this->name);
		}
		$desc = $this->connection->validate_string($row[1]);
		if($this->description != $desc) {
			$this->createLogEntry($username, "Asset Description Changed", 
				$desc, $this->description);
		}
		if($this->condition != $row[2]) {
			if($row[3] != '') {
				$this->createLogEntry($row[3], "Asset Condition Changed",
					$row[2], $this->condition);
			} else {
				$this->createLogEntry($username, "Asset Condition Changed",
					$row[2], $this->condition);
			}
		}
		if($this->checkoutTo != $row[3] &&
			$this->checkoutTo != '') {
			$this->createLogEntry($username, "Asset Checked Out", 
				$row[3], $this->checkoutTo);
		}
		if($this->box != $row[4]) {
			$this->createLogEntry($username, "Asset Box Changed", $row[4],
				$this->box);
		}
		if($this->itemType != $row[5]) {
			$this->createLogEntry($username, "Asset Item Type Changed",
				$row[5], $this->itemType);
		}
	}
	
	private function createLogEntry($user, $type, $oldValue, $newValue) {
		$logEntry = new LogEntry($this->connection);
		$logEntry->setBarcode($this->barcode);
		$logEntry->setPerson($user);
		$logEntry->setType($type);
		$logEntry->setOldValue($oldValue);
		$logEntry->setNewValue($newValue);
		$logEntry->insert();
	}
	
	public function loadEntry($barcode) {
		$query = "
		SELECT a_barcode, a_name, a_description, a_condition, a_checkout_to, a_box, a_item_type
		FROM assets
		WHERE a_barcode = '$barcode';";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}

	public function printSuccess() {
		echo "<center>
		Successfully added the new asset.
		<br><br><br></center>";
	}
	
	public function findAsset($barcode) {
		$this->loadEntry($barcode);
		if($this->barcode == '') {
			echo "<br>Could not find item: $barcode<br><br>";
			return;
		}
		echo "Item Name: $this->name ";
		echo "<a href=\"index.php?action=edit&type=asset&barcode=$this->barcode\">edit</a> ";
		echo "<a href=\"index.php?action=edit&type=box&barcode=$this->box\">Goto box</a> ";
		echo "<a href=\"index.php?action=delete&type=asset&barcode=$this->barcode\">delete</a> ";
	}
	
	public function deleteAsset($barcode) {
		$this->connection->query("begin;");
		//log this sad event
		$user = new User();
		$logEntry = new LogEntry($this->connection);
		$logEntry->setBarcode($barcode);
		$logEntry->setPerson($user->get_Username());
		$logEntry->setType("Asset Deleted");
		$logEntry->insert();
		
		$query = "DELETE FROM assets WHERE a_barcode = '$barcode'";
		$this->connection->query($query);
		$this->connection->query("commit;");
	}
	
	public function printFindForm() {
		echo "
		<form action=\"index.php?action=find&type=asset\" method=\"post\" enctype=\"multipart/form-data\">
		Barcode to look for:
		<input type=\"text\" name=\"barcode\" value=\"$this->barcode\"><br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">";
	}
}
?>
