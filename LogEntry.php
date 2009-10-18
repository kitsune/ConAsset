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
 * LogEntry.php
 * Allows the easy creation and retiving of log entries.
 * */

class LogEntry {
	
	private $connection;
	private $index = 0;
	private $barcode = '';
	private $person = '';
	private $timestamp = '';
	private $type = '';
	private $oldValue = '';
	private $newValue = '';

	function __construct($connection){
		$this->connection = $connection;
	}

	public function getIndex() {
		return $this->index;
	}
	
	public function getBarcode() {
		return $this->barcode;
	}
	
	public function getPerson() {
		return $this->person;
	}
	
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getOldValue() {
		return $this->oldValue;
	}
	
	public function getNewValue() {
		return $this->newValue;
	}
	
	public function setIndex($index) {
		$this->index = $index;
	}
	
	public function setBarcode($barcode) {
		$this->barcode = $barcode;
	}
	
	public function setPerson($person) {
		$this->person = $person;
	}
	
	public function setDate($timestamp) {
		$this->timestamp = $timestamp;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function setOldValue($oldValue) {
		$this->oldValue = $oldValue;
	}
	
	public function setNewValue($newValue) {
		$this->newValue = $newValue;
	}
	
	public function insert() {
		$query = "
		INSERT INTO log_entries (l_barcode, l_person, l_timestamp, l_type, l_old_value, l_new_value)
		VALUES ('$this->barcode', '$this->person', NOW(), '$this->type', '$this->oldValue', '$this->newValue');
		";
		$this->connection->query($query);
	}
}

?>
