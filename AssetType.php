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
	
	/*
	private function init($row) {
		$this->index = $row['at_index'];
		$this->name = $row['at_name'];
		$this->description = $row['at_description'];
		$this->needMore = $row['at_need_more'];
	}*/
	
	private function init($row) {
		$this->index = $row[0];
		$this->name = $row[1];
		$this->description = $row[2];
		$this->needMore = $row[3];
	}
	
	public function loadFromPage() {
		if(isset($_POST['name'])) {
			$this->name = $this->connection->validate_string($_POST['name']);
		}
		if(isset($_POST['description'])) {
			$this->description = $this->connection->validate_string($_POST['description']);
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
		VALUES ('$this->name', '$this->description', 0);
		";
		$this->connection->query($query);
		$index = $this->connection->get_insert_ID();
		//set the new type to be the type we are working with
		$_SESSION['itemtype'] = $index[0];
	}
	
	public function update() {
		$query = "
		UPDATE asset_types 
		SET at_name = '$this->name', at_description = '$this->description' , at_need_more = $this->needMore
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
	
	public function listAll() {
		Echo "Asset Types:<br><br>";
		$query = "
		SELECT at_index, at_name
		FROM asset_types;";
		$this->connection->query($query);
		while($row = $this->connection->fetch_row())
		{
			$index = $row[0];
			$name = $row[1];
			echo "$name ";
			echo "<a href=\"index.php?action=listByType&type=assettype&index=$index\">List All Assets</a> ";
			echo "<a href=\"index.php?action=use&type=assettype&index=$index\">Use</a> ";
			echo "<a href=\"index.php?action=edit&type=assettype&index=$index\">Edit</a> ";
			echo "<a href=\"index.php?action=delete&type=assettype&index=$index\">Delete</a><br>";
		}
	}

	public function listByType($index) {
		$query = "
		SELECT T.at_name, A.a_name, A.a_barcode, COALESCE(P.p_name,'Nobody')
		FROM assets A
		LEFT JOIN asset_types T ON A.a_item_type = T.at_index
		LEFT JOIN people P ON P.p_barcode = A.a_checkout_to
		WHERE T.at_index = $index";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		echo "<table>";
		echo "<caption>Listing all assets of type: {$row[0]}</caption>";
		echo "<tr><th>Asset Name</th><th>Barcode</th><th>Checked out to</th></tr><td></td>";
		do{
			echo "<tr><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td><a href=\"index.php?action=edit&type=asset&barcode={$row[2]}\">edit</a></td></tr>";
		}while($row = $this->connection->fetch_row());
		echo "</table>";
	}
	
	public function deleteAssetType($index) {
		$query = "
		SELECT COUNT(a_barcode)
		FROM assets
		WHERE a_item_type = $index;";
		$this->connection->query($query);
		$row = $this->connection->fetch_row();
		if($row[0] != 0) {
			echo "Cannot delete an Assest Type that is currently being used by assets<br><br>";
		} else {
			$query = "DELETE FROM asset_types WHERE at_index = $index;";
			$this->connection->query($query);
		}
	}
	
	public function printSuccess() {
		echo "<center>
		Successfully created asset type.
		Set new asset type as default asset type.
		<br><br><br></center>";
	}
}
?>
