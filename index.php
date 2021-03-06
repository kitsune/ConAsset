<?php

function __autoload($class_name) {
    require_once $class_name . '.php';
}

$user = new User();

$connection = new Connection();

$webpage = new Webpage("ConAsset inventory management system");

//$asset = new Asset($connection);
try{
if(isset($_GET['action']) && isset($_GET['type'])) {
	if($_GET['type'] == 'asset') {
		$asset = new Asset($connection);
		if(isset($_POST['submit'])) {
			if($_GET['action'] == 'add') {
				$asset->loadFromPage();
				$asset->insert();
			} else if($_GET['action'] == 'edit'){
				$asset->loadFromPage();
				$asset->update();
			} else if ($_GET['action'] == 'checkout') {
				if(User::exists($_POST['checkoutTo'],$connection)){
					$asset->loadEntry($_POST['barcode']);
					$asset->setCheckoutTo($_POST['checkoutTo']);
					$asset->update();
					$asset->printForm('checkout');
				}else{
					echo "User {$_POST['checkoutTo']} does not exist";
				}
			} else if ($_GET['action'] == 'batchcheckout'){
				if(User::exists($_POST['checkoutTo'],$connection)){
					$barcodes = $_POST['barcodes'];
					foreach ($barcodes as $barcode){
						$asset->loadEntry($barcode);
						$asset->setCheckoutTo($_POST['checkoutTo']);
						$asset->update();
					}
					$asset->printForm('batchcheckout');
				}else{
					echo "User {$_POST['checkoutTo']} does not exist";
				}
			} else if($_GET['action'] == 'find') {
				$asset->findAsset($_POST['barcode']);
			} else if($_GET['action'] == 'checkin') {
				$asset->loadEntry($_POST['barcode']);
				$asset->printForm('checkin2');
			} else if($_GET['action'] == 'checkin2'){
				$asset->loadEntry($_POST['barcode']);
				$asset->loadEntry($_POST['barcode']);
				$asset->setCheckoutTo('');
				$asset->setBox($_POST['box']);
				$asset->update();
				$asset->printForm('checkin');
			}
		} else {
			if($_GET['action'] == 'find') {
				$asset->printFindForm();
			} else if($_GET['action'] == 'delete') {
				if(isset($_GET['confirm'])) {
					$asset->deleteAsset($_GET['barcode']);
				} else {
					$barcode = $_GET['barcode'];
					echo "<br><center>Are you sure?<br>
					<a href=\"index.php?action=delete&type=asset&barcode=$barcode&confirm=yes\">Yes</a> 
					<a href=\"index.php\">No</a>
					</center>";
				}
			} else if($_GET['action'] == 'edit') {
				$asset->loadEntry($_GET['barcode']);
				$asset->printForm($_GET['action']);
			} else {
				$asset->printForm($_GET['action']);	
			}
		}
	}
	if($_GET['type'] == 'assettype') {
		$assettype = new AssetType($connection);
		if(isset($_POST['submit'])) {
			if($_GET['action'] == 'add') {
				$assettype->loadFromPage();
				$assettype->insert();
			} else if($_GET['action'] == 'edit') {
				$assettype->loadFromPage();
				$assettype->update();
			}
		} else {
			if($_GET['action'] == 'list') {
				$assettype->listAll();
			} else if($_GET['action'] == 'delete') {
				if(isset($_GET['confirm'])) {
					$assettype->deleteAssetType($_GET['index']);
				} else {
					$index = $_GET['index'];
					echo "<br><center>Are you sure?<br>
					<a href=\"index.php?action=delete&type=assettype&index=$index&confirm=yes\">Yes</a> 
					<a href=\"index.php\">No</a>
					</center>";
				}
			} else if($_GET['action'] == 'use') {
				$_SESSION['itemtype'] = $_GET['index'];
			} else if($_GET['action'] == 'edit') {
				$assettype->loadEntry($_GET['index']);
				$assettype->printForm($_GET['action']);
			} else if($_GET['action'] == 'listByType') {
				$assettype->listByType($_GET['index']);
			} else {
				$assettype->printForm($_GET['action']);
			}
		}
	}
	if($_GET['type'] == 'box') {
		$box = new Box($connection);
		/*echo '<pre>';
		nl2br(var_dump($_POST));
		echo '</pre>';
		*/
		if(isset($_POST['submit']))
		{
			if($_GET['action'] == 'add') {
				$box->loadFromPage();
				$box->insert();
			} else if($_GET['action'] == 'edit') {
				$box->loadFromPage();
				$box->update();
			} else if($_GET['action'] == 'find') {
				$box->findBox($_POST['barcode']);
			} else if($_GET['action'] == 'checkout') {
				$connection->query("SELECT a_barcode FROM assets WHERE a_box = '{$_POST['barcode']}' and a_checkout_to = ''");
				if($connection->result_size() <= 0){ 
				   	throw new Exception('no assets or all assets checked out inside box with barcode: ' .$_POST['barcode']);
				}
				$barcodes = array();
				while($row = $connection->fetch_row()) {
					$barcodes[] = $row[0];
				}
				foreach($barcodes as $barcode){
					$the_asset = new Asset($connection);
					$the_asset->loadEntry($barcode);
					$the_asset->setCheckoutTo($connection->validate_string($_POST['checkoutTo']));
					$the_asset->update();
				}
			} else if($_GET['action'] == 'batchmove'){
				$location = new Location($connection);
				$location->loadEntry($_POST['location']);
				$barcodes = $_POST['barcodes'];
				foreach ($barcodes as $barcode){
					$box->loadEntry($barcode);
					$box->setLocation($location->getIndex());
					$box->update();
				}
				$box->printForm('batchmove');
			}
		} else {
			if($_GET['action'] == 'find') {
				$box->printFindForm();
			} else if($_GET['action'] == 'delete') {
				if(isset($_GET['confirm'])) {
					$box->deleteBox($_GET['barcode']);
				} else {
					$barcode = $_GET['barcode'];
					echo "<br><center>Are you sure?<br>
					<a href=\"index.php?action=delete&type=box&barcode=$barcode&confirm=yes\">Yes</a> 
					<a href=\"index.php\">No</a>
					</center>";
				}
			} else if($_GET['action'] == 'list'){
				$box->listBoxes();
			} else if($_GET['action'] == 'view'){
				$box->loadEntry($_GET['barcode']);
				$box->printForm($_GET['action']);
			} else if($_GET['action'] == 'edit') {
				$box->loadEntry($_GET['barcode']);
				$box->printForm($_GET['action']);
			} else {
				$box->printForm($_GET['action']);
			}
		}
	}
	if($_GET['type'] == 'location') {
		$location = new Location($connection);
		if(isset($_POST['submit'])) {
			if($_GET['action'] == 'add') {
				$location->loadFromPage();
				$location->insert();
			} else if($_GET['action'] == 'edit') {
				$location->loadFromPage();
				$location->update();
			}
		} else {
			if($_GET['action'] == 'edit') {
				$location->loadEntry($_GET['index']);
			}
			$location->printForm($_GET['action']);
		}
	}
}
}catch(Exception $e){
	echo "ERROR: " .  $e->getMessage();
}

echo "
<center>
<br><br>";
$webpage->addURL("index.php?action=add&type=asset",
	"Add a new asset");
echo "<br>";
$webpage->addURL("index.php?action=find&type=asset",
	"Search for an Assest by barcode");
echo "<br>";
$webpage->addURL("index.php?action=add&type=assettype",
	"Create new asset type");
echo "<br>";
$webpage->addURL("index.php?action=list&type=assettype",
	"List avalible asset types");
echo "<br>";
$webpage->addURL("index.php?action=add&type=box",
	"Create new box entry");
echo "<br>";
$webpage->addURL("index.php?action=list&type=box",
	"List Boxes");
echo "<br>";
$webpage->addURL("index.php?action=find&type=box",
	"Search for a box by barcode");
echo "<br>";
$webpage->addURL("index.php?action=add&type=location",
	"Add a new location");
echo "<br>";
$webpage->addURL("index.php?action=checkout&type=asset",
	"Checkout an Asset");
echo "<br>";
$webpage->addURL("index.php?action=batchcheckout&type=asset",
	"Batch checkout Assets");
echo "<br>";
$webpage->addURL("index.php?action=checkout&type=box",
	"Checkout all items in Box");
echo "<br>";
$webpage->addURL("index.php?action=batchmove&type=box",
	"Move multiple Boxes to one location (Batch move)");
echo "<br>";
$webpage->addURL("index.php?action=checkin&type=asset",
	"Checkin an Asset");
echo "</center>";
?>
