<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since    0.8
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Install {

	private $mode = 'Installing';
	private $folders = array();
	


	/**
	 * runs the installation
	 * 
	 * @return void
	 */
	public function go()
	{
		if(!Json::getLockFile())
		{
			$json = Json::getPackageFile();

			// Can't find json
			if(!$json)
			{
				echo 'Error: Could not load packman.json, does it exist and is it in the right location?' .PHP_EOL;
			}
			else
			{
				foreach($json->modules as $name => $module)
				{
					$m = new Module();
					$m->setup($name, $this->mode);
					$this->folders[$name] = $m->install();
				}

				// Add the created folders to the module in the lock file
				foreach($this->folders as $name => $folder)
				{
					// Order folders by string length to make sure we delete subfolders before parent folders
					usort($folder, 'sortByLength');
					if(isset($json->modules->$name)) $json->modules->$name->folders = $folder;
				}

				Json::createLock($json);
			}
		}
		else
		{
			echo 'Error: lock file exists, you need to run `php packman update`';
		}
	}
}