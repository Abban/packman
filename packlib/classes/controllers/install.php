<?php defined('DS') or die('No direct script access.');

class Install {

	protected $mode = 'Installing';
	
	public function go()
	{
		$options = Json::getPackageFile();

		// Can't find json
		if(!$options)
		{
			echo 'Error: Could not load packman.json, does it exist and is it in the right location?' .PHP_EOL;
		}
		else
		{
			foreach($options->modules as $name => $module)
			{
				$m = new Module();
				$m->setup($name, $this->mode);
				$m->install();
			}
			
			File::copy(BASEPATH .'packman.json', BASEPATH .'packman.lock');
		}
	}
}