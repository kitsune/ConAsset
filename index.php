<?php

function __autoload($class_name) {
    require_once $class_name . '.php';
}

$user = new User();

$connection = new Connection();

$webpage = new Webpage("ConAsset inventory management system");

//$asset = new Asset($connection);

if(isset($_GET['action']) && isset($_GET['type'])) {
	if($_GET['type'] == 'asset') {
		$asset = new Asset($connection);
		if(isset($_POST['submit']))
		{
			if($_GET['action'] == 'add') {
				$asset->loadFromPage();
				$asset->insert();
			} else if($_GET['action'] == 'edit') {
				$asset->loadFromPage();
				$asset->update();
			}
		} else {
			if($_GET['action'] == 'edit') {
				$asset->loadEntry($_GET['barcode']);
			}
			$asset->printForm($_GET['action']);	
		}
	}
	if($_GET['type'] == 'assettype') {
		$assettype = new AssetType($connection);
		if(isset($_POST['submit']))
		{
			if($_GET['action'] == 'add') {
				$assettype->loadFromPage();
				$assettype->insert();
			} else if($_GET['action'] == 'edit') {
				$assettype->loadFromPage();
				$assettype->update();
			}
		} else {
			if($_GET['action'] == 'edit') {
				$assettype->loadEntry($_GET['index']);
			}
			$assettype->printForm($_GET['action']);
		}
	}
	if($_GET['type'] == 'box') {
		$box = new Box($connection);
		if(isset($_POST['submit']))
		{
			if($_GET['action'] == 'add') {
				$box->loadFromPage();
				$box->insert();
			} else if($_GET['action'] == 'edit') {
				$box->loadFromPage();
				$box->update();
			}
		} else {
			if($_GET['action'] == 'edit') {
				$box->loadEntry($_GET['index']);
			}
			$box->printForm($_GET['action']);
		}
	}
}

echo "
<center>
<br><br>";
$webpage->addURL("index.php?action=add&type=assettype",
	"Create new asset type");
echo "<br>";
$webpage->addURL("index.php?action=add&type=box",
	"Create new box entry");
echo "<br>";
$webpage->addURL("index.php?action=add&type=asset",
	"Add a new asset");
echo "</center>";


?>
