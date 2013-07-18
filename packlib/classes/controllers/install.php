<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Install {

	protected $mode = 'Installing';
	


	/**
	 * runs the installation
	 * 
	 * @return void
	 */
	public function go()
	{
		if(!Json::getLockFile())
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
		else
		{
			echo 'Error: lock file exists, you need to run `php packman update`';
		}
	}
}