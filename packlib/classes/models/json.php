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
	
	static function getPackageFile()
	{
		return self::getFile(BASEPATH .'packman.json');
	}

	static function getLockFile()
	{
		return self::getFile(BASEPATH .'packman.lock');
	}

	private static function getFile($file)
	{
		if($json = file_get_contents($file))
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
}