<?php defined('DS') or die('No direct script access.');

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