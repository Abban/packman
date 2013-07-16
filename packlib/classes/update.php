<?php defined('DS') or die('No direct script access.');

class Update extends Install {
	protected $mode = 'Install';

	function __construct()
	{
		parent::__construct();
		$this->mode = 'Updat';
	}
}