<?php defined('DS') or die('No direct script access.');

/**
 * For now this is just installing by another name
 *
 * What it will do is:
 * 1. Only install modules that have been updated.
 * 2. Specifically update a single module if the name is passed: `php packman update wt-menu`
 * 3. Check for any modules that have been removed and delete them.
 * 
 */
class Update {

	private $mode = 'Updating';

	public function go($argv)
	{
		$json = Json::getPackageFile();

		// Check if single module is specified
		if(isset($argv[2]))
		{
			// Make sure the module is in the json file
			if(!is_object($json->modules->$argv[2]))
			{
				echo 'Could not find module `' .$argv[2] .'` to update' .PHP_EOL;
			}
			// Its there so process it
			else
			{
				$this->processModule($argv[2]);
			}
		}
		// Loop all modules
		else
		{
			foreach($json->modules as $name => $module)
			{
				$this->processModule($name);	
			}
		}
		
		File::copy(BASEPATH .'packman.json', BASEPATH .'packman.lock');
	}

	private function processModule($name)
	{
		$m = new Module();
		$m->setup($name, $this->mode);
		$m->update();
	}
}