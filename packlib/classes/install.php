<?php defined('DS') or die('No direct script access.');

class Install {
	
	function go()
	{
		$options = Json::getPackageFile();

		foreach($options->modules as $name => $module)
		{
			$m = new Module();
			$m->setup($name, $module, $options->modulesFolder);
			$m->install();
		}
	}
}