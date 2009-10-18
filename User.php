<?
/* 
 * Copyright © 2009 Dylan Enloe
 * ALL RIGHTS RESERVED
 * 
 * This program is distributed under the terms of
 * version 3 of the GNU Lesser General Public License.
 * Please see the file LICENSE in this distribution
 * for licensing information.
 *
 * User.php
 * Keep track of the user and the user's privilages
 * */

session_start();

require_once 'Connection.php';

class User {
	private $admin;
	private $user;
	private $username;
	private $barcode;
	
	function __construct() {
		if(!isset($_SESSION['username'])) {
			if(isset($_POST['login'])) {
				$this->username = $_POST['username'];
				$password = $_POST['password'];
				$query = "
				SELECT p_password, p_barcode
				FROM people
				WHERE p_username = '$this->username';";
				$connection = new Connection();
				$connection->query($query);
				if($connection->result_size() == 1) {
					$row = $connection->fetch_row();
					if($row[0] == $password) {
						$_SESSION['username'] = $this->username;
						$_SESSION['barcode'] = $row[1];
						$_SESSION['user'] = true;
						$_SESSION['admin'] = true;
					} else {
						$this->loginError();
						exit(0);
					}
				} else {
					$this->loginError();
					exit(0);
				}
			} else {
				$this->printLoginScreen();
				exit(0);
			}
		}
	}
	
	private function loginError() {
		echo "
<center>
Could not log in, please check your user name and password and try again
<br>
<a href=\"index.php\">Back to login screen</a>
</center>";
	}
	
	private function printLoginScreen() {
		echo "
<form action=\"index.php?action=login\" method=\"post\" enctype=\"multipart/form-data\">
Username: <input type=\"text\" name=\"username\"> <br>
password: <input type=\"password\" name=\"password\"> <br>
<input type=\"submit\" name=\"login\" value=\"Finished\">";
	}
	
	public function is_Admin() {
		return $this->admin;
	}
	
	public function is_User() {
		return $this->user;
	}
	
	public function get_Username() {
		return $this->username;
	}
}
?>
