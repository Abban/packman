<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Json {
	


	/**
	 * Gets the packman.json file
	 * 
	 * @return json
	 */
	static function getPackageFile()
	{
		return self::getFile(BASEPATH .'packman.json');
	}



	/**
	 * Gets the lock file
	 * 
	 * @return json
	 */
	static function getLockFile()
	{
		return self::getFile(BASEPATH .'packman.lock');
	}



	/**
	 * Gets a json file by filename
	 * 
	 * @param  string $file
	 * @return json/bool
	 */
	private static function getFile($file)
	{
		if($json = @file_get_contents($file))
		{
			// TODO: Error Reporting
			return json_decode($json);
		}

		// Throw a shit error for now
		else
		{
			return false;
		}
	}



	static function createLock($json)
	{
		File::put(BASEPATH .'packman.lock', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
}