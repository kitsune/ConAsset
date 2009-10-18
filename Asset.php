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
	private function init($row){
		$this->barcode = $row['a_barcode'];
		$this->name = $row['a_name'];
		$this->description = $row['a_description'];
		$this->condition = $row['a_condition'];
		$this->checkoutTo = $row['a_checkout_to'];
		$this->box = $row['a_box'];
		$this->itemType = $row['a_item_type'];
	}

	public function loadFromPage(){
		if (isset($_POST['barcode'])){
			$this->barcode = $_POST['barcode'];
		}
		if (isset($_POST['name'])){
			$this->name = $_POST['name'];
		}
		if (isset($_POST['description'])){
			$this->description = $_POST['description'];
		}
		if (isset($_POST['condition'])){
			$this->condition = $_POST['condition'];
		}
		if (isset($_POST['checkoutTo'])){
			$this->checkoutTo = $_POST['checkoutTo'];
		}
		if (isset($_POST['box'])) {
			$this->box = $_POST['box'];
		}
	}

	/**
	 * Create a form for inputing the fields
	 */
	public function printForm($action) {
		$itemType = $_GET['itemtype'];
		//make sure the is a valid item type and get it's name
		$query = "
		SELECT at_name
		FROM asset_types
		WHERE at_index = $this->itemType";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		if($row[0] == '') {
			echo "You must select an asset type before you can add items
			<br><br>";
			return;
		}
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
	/**
	 * Insert this object into the DB
	 * @return new id (auto increment value) genereated
	 */

	public function insert() {
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
	}
	/**
	 * Update this object into the DB
	 * @return number of updated records
	 */
	public function update() {	
		$list = array("a_barcode"=>$this->barcode, "a_name"=>$this->name, "a_description"=>$this->description, "a_condition"=>$this->condition, "a_checkout_to"=>$this->checkoutTo, "a_box"=>$this->box, "a_item_type"=>$this->itemType);
		$sql = "update assets set ";
		foreach ($list as $key => $value) {
			if(is_string($value)) {
				$sql .= "$key='$value', ";
			} else {
				$sql .= "$key=$value, ";
			}
		}
		$sql = substr($sql, 0, -2)." where `a_barcode`='$barcode'";
		$this->connection->query($sql);
	}
	
	public function loadEntry($barcode) {
		$query = "
		SELECT a_barcode, a_name, a_description, a_condition, a_checkoutto, a_box, a_item_type
		FROM assets
		WHERE a_barcode = $barcode";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}

	public function printSuccess() {
		echo "<center>
		Successfully added the new asset.
		<br><br><br></center>";
	}
	
}
?>
