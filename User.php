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

/*require_once($sServerDocRoot . '/forum/SSI.php');

class User {
	private $admin;
	private $user;
	private $username;
	
	function __construct()
	{
		global $context;
		$self->admin = $context['user']['is_admin'];
		$self->user = !$context['user']['is_guest'];
		$self->username = $context['user']['username'];
		echo $username;
	}
	
	public function is_Admin()
	{
		return $this->admin;
	}
	
	public function is_User()
	{
		return $this->user;
	}
	
	public function get_Username()
	{
		return $this->username;
	}
} */

class User {
	private $admin;
	private $user;
	private $username;
	
	function __construct() {
	$self->admin = true;
	$self->user = true;
	$self->username = "kitten";
	}
	
	public function is_Admin()
	{
		return $this->admin;
	}
	
	public function is_User()
	{
		return $this->user;
	}
	
	public function get_Username()
	{
		return $this->username;
	}
}
?>