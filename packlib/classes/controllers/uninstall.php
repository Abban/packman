<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Uninstall {

	private $mode = 'Uninstalling';

	public function go()
	{
		$json = Json::getLockFile();

		// Can't find json
		if(!$json)
		{
			echo 'Error: Could not load packman.lock, does it exist and is it in the right location?' .PHP_EOL;
		}
		else
		{
			foreach($json->modules as $name => $module)
			{
				$m = new Module();
				$m->setup($name, $this->mode);
				$m->delete();
			}

			// Archive the old lock file
			archiveLock();
		}
	}
}