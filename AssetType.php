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
 * AssetType.php
 * Handles the entries for of asset types, including loading from the database
 * and inserting/updating the database.
 * */

class AssetType {
	
	private $connection;
	private $index = 0;
	private $name = '';
	private $description = '';
	private $needMore = 0;
	
	function __construct($connection){
		$this->connection = $connection;
	}
	
	public function getIndex() {
		return $this->index;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getNeedMore() {
		return $this->needMore;
	}
	
	public function setIndex($index) {
		$this->index = $index;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function setDescription($description) {
		$this->description = $description;
	}
	
	public function setNeedMore($needMore) {
		$this->needMore = $needMore;
	}
	
	private function init($row) {
		$this->index = $row['at_index'];
		$this->name = $row['at_name'];
		$this->description = $row['at_description'];
		$this->needMore = $row['at_need_more'];
	}
	
	public function loadFromPage() {
		if(isset($_POST['name'])) {
			$this->name = $_POST['name'];
		}
		if(isset($_POST['description'])) {
			$this->description = $_POST['description'];
		}
	}
	
	public function printForm($action) {
		echo "<center>
		<form action=\"index.php?action=$action&type=assettype\" method=\"post\" enctype=\"multipart/form-data\">
		Name: <br>
		<input type=\"text\" name=\"name\" value=\"$this->name\"> <br>
		Description: <br>
		<textarea name=\"description\" rows=\"10\" cols=\"60\">$this->description</textarea> 
		<br><br>
		<input type=\"submit\" name=\"submit\" value=\"Finished\">
		</center>
		";
	}
	
	public function insert() {
		$query = "
		INSERT INTO asset_types (at_name, at_description, at_need_more)
		VALUES ('$this->name', '$this->desciption', 0);
		";
		$this->connection->query($query);
		$index = $this->connection->get_insert_ID();
		//set the new type to be the type we are working with
		$_SESSION['itemtype'] = $index[0];
	}
	
	public function update() {
		$query = "
		UPDATE asset_types 
		SET at_name = $this->name, at_description = $this->description , at_need_more = $this->needMore
		WHERE at_index = $this->index;
		";
		$this->connection->query($query);
	}
	
	public function loadEntry($index) {
		$query = "
		SELECT at_index, at_name, at_description, at_need_more
		FROM asset_types
		WHERE at_index = $index";
		$this->connection->query($query);
		$this->init($this->connection->fetch_row());	
	}
	
	public function printSuccess() {
		echo "<center>
		Successfully created asset type.
		Set new asset type as default asset type.
		<br><br><br></center>";
	}
}
?>
