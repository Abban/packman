<?php defined('DS') or die('No direct script access.');

class Update extends Install {

	function __construct()
	{
		parent::__construct();
		$this->mode = 'Updating';
	}
}