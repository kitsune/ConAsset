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

private $conn;
private $aBarcode  = 0;
private $aName;
private $aDescription;
private $aCondition;
private $aCheckoutOutTo;
private $aBox;
private $aItemType;

/**
 * Retrieves the value from the field a_barcode
 * @return String with the value of the field
 */
public function getABarcode(){
  return $this->aBarcode;
}
/**
 * Retrieves the value from the field a_name
 * @return String with the value of the field
 */
public function getAName(){
  return $this->aName;
}
/**
 * Retrieves the value from the field a_description
 * @return String with the value of the field
 */
public function getADescription(){
  return $this->aDescription;
}
/**
 * Retrieves the value from the field a_condition
 * @return String with the value of the field
 */
public function getACondition(){
  return $this->aCondition;
}
/**
 * Retrieves the value from the field a_checkout_out_to
 * @return String with the value of the field
 */
public function getACheckoutOutTo(){
  return $this->aCheckoutOutTo;
}
/**
 * Retrieves the value from the field a_box
 * @return String with the value of the field
 */
public function getABox(){
  return $this->aBox;
}
/**
 * Retrieves the value from the field a_item_type
 * @return String with the value of the field
 */
public function getAItemType(){
  return $this->aItemType;
}
/**
 * Set the value from the field a_barcode
 * @param aBarcode String with the value for the field
 */
public function setABarcode($aBarcode){
  $this->aBarcode = $aBarcode;
}
/**
 * Set the value from the field a_name
 * @param aName String with the value for the field
 */
public function setAName($aName){
  $this->aName = $aName;
}
/**
 * Set the value from the field a_description
 * @param aDescription String with the value for the field
 */
public function setADescription($aDescription){
  $this->aDescription = $aDescription;
}
/**
 * Set the value from the field a_condition
 * @param aCondition String with the value for the field
 */
public function setACondition($aCondition){
  $this->aCondition = $aCondition;
}
/**
 * Set the value from the field a_checkout_out_to
 * @param aCheckoutOutTo String with the value for the field
 */
public function setACheckoutOutTo($aCheckoutOutTo){
  $this->aCheckoutOutTo = $aCheckoutOutTo;
}
/**
 * Set the value from the field a_box
 * @param aBox String with the value for the field
 */
public function setABox($aBox){
  $this->aBox = $aBox;
}
/**
 * Set the value from the field a_item_type
 * @param aItemType String with the value for the field
 */
public function setAItemType($aItemType){
  $this->aItemType = $aItemType;
}
/**
 * Default constructor
 * @param conn the database connection
 */
 function __construct($conn){

      $this->conn = $conn;
    
}
/**
 * Returns the string representation of this obbject
 * @return String repesentation ofAssets
 */
public function toString(){
  $s = '';
  $s .= 'a_barcode: '.$this->aBarcode;
  $s .= 'a_name: '.$this->aName;
  $s .= 'a_description: '.$this->aDescription;
  $s .= 'a_condition: '.$this->aCondition;
  $s .= 'a_checkout_out_to: '.$this->aCheckoutOutTo;
  $s .= 'a_box: '.$this->aBox;
  $s .= 'a_item_type: '.$this->aItemType;
  return $s;
}
/**
 * Initialize the business object with data read from the DB.
 * @param row array containing one read record.
 */
private function init($row){
  $this->aBarcode = $row['a_barcode'];
  $this->aName = $row['a_name'];
  $this->aDescription = $row['a_description'];
  $this->aCondition = $row['a_condition'];
  $this->aCheckoutOutTo = $row['a_checkout_out_to'];
  $this->aBox = $row['a_box'];
  $this->aItemType = $row['a_item_type'];
}
/**
 * Initialize the business object with data read from the DB.
 */
private function initPOST(){
  if (isset($_POST['aBarcode'])){
    $this->aBarcode = $_POST['aBarcode'];
  }
  if (isset($_POST['aName'])){
    $this->aName = $_POST['aName'];
  }
  if (isset($_POST['aDescription'])){
    $this->aDescription = $_POST['aDescription'];
  }
  if (isset($_POST['aCondition'])){
    $this->aCondition = $_POST['aCondition'];
  }
  if (isset($_POST['aCheckoutOutTo'])){
    $this->aCheckoutOutTo = $_POST['aCheckoutOutTo'];
  }
  if (isset($_POST['aBox'])){
    $this->aBox = $_POST['aBox'];
  }
  if (isset($_POST['aItemType'])){
    $this->aItemType = $_POST['aItemType'];
  }
}
/**
 * Initialize the business object with data read from the DB.
 */
private function initGET(){
  if (isset($_GET['aBarcode'])){
    $this->aBarcode = $_GET['aBarcode'];
  }
  if (isset($_GET['aName'])){
    $this->aName = $_GET['aName'];
  }
  if (isset($_GET['aDescription'])){
    $this->aDescription = $_GET['aDescription'];
  }
  if (isset($_GET['aCondition'])){
    $this->aCondition = $_GET['aCondition'];
  }
  if (isset($_GET['aCheckoutOutTo'])){
    $this->aCheckoutOutTo = $_GET['aCheckoutOutTo'];
  }
  if (isset($_GET['aBox'])){
    $this->aBox = $_GET['aBox'];
  }
  if (isset($_GET['aItemType'])){
    $this->aItemType = $_GET['aItemType'];
  }
}

public function loadAll(){

      $rows = mysql_query("select * from assets", $this->conn);
      $assetss = array();
      while ($row = mysql_fetch_array($rows)) {
        $d = new Assets($this->conn);
        $d->init($row);
        array_push($assetss,$d);
      }
      return $assetss;
    
}
/**
 * Insert this object into the DB
 * @return new id (auto increment value) genereated
 */
public function insert(){

      $list = array("a_barcode"=>$this->aBarcode, "a_name"=>$this->aName, "a_description"=>$this->aDescription, "a_condition"=>$this->aCondition, "a_checkout_out_to"=>$this->aCheckoutOutTo, "a_box"=>$this->aBox, "a_item_type"=>$this->aItemType);
      $sql = "insert into assets values (";
      foreach ($list as $key => $value){
        if(is_string($value))
          $sql .= "'$value', ";
        else
          $sql .= "$value, ";
      }
      $sql = substr($sql, 0, -2).")";
      return mysql_query($sql, $this->conn);
    
}
/**
 * Update this object into the DB
 * @return number of updated records
 */
public function update(){

      $list = array("a_barcode"=>$this->aBarcode, "a_name"=>$this->aName, "a_description"=>$this->aDescription, "a_condition"=>$this->aCondition, "a_checkout_out_to"=>$this->aCheckoutOutTo, "a_box"=>$this->aBox, "a_item_type"=>$this->aItemType);
      $sql = "update assets set ";
      foreach ($list as $key => $value){
        if(is_string($value))
          $sql .= "$key='$value', ";
        else
          $sql .= "$key=$value, ";
      }
      $sql = substr($sql, 0, -2)." where `a_barcode`='$aBarcode'";
      return mysql_query($sql, $this->conn);
    
}
/**
 * Search for items by item type
 * @param item type to look for
 */
public function searchByType($itemType){

      $rows = mysql_query("select * from assets where a_item_type = $itemType", $this->conn);
      $assetss = array();
      while ($row = mysql_fetch_array($rows)) {
        $d = new Assets($this->conn);
        $d->init($row);
        array_push($assetss,$d);
      }
      return $assetss;      
    
}
}
?>