<?php defined('DS') or die('No direct script access.');

class Install {

	private static $folder;
	
	function go()
	{
		$options = Json::getPackageFile();
		print_r($options);

		$this->folder = BASEPATH .$options['modulesFolder'];
	}
}