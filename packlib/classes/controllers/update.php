<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Update {

	private $mode = 'Updating';



	/**
	 * Runs the update
	 * 
	 * @param  string $argv
	 * @return void
	 */
	public function go($argv)
	{
		$json = Json::getPackageFile();

		// Check if single module is specified
		if(isset($argv[2]))
		{
			// Make sure the module is in the json file
			if(!is_object($json->modules->$argv[2]))
			{
				echo 'Error: Could not find module `' .$argv[2] .'` to update' .PHP_EOL;
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



	/**
	 * Processes a single module by name
	 * 
	 * @param  string $name
	 * @return void
	 */
	private function processModule($name)
	{
		$m = new Module();
		if($m->setup($name, $this->mode))
		{
			$m->update();
		}
		else
		{
			echo 'Error: module `' .$name '` was not found in packman.json or packman.lock';
		}
	}
}