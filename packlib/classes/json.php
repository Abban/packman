<?php defined('DS') or die('No direct script access.');

class Json {
	
	static function getPackageFile()
	{
		if($json = file_get_contents(BASEPATH .'packman.json'))
		{
			// TODO: Error Reporting
			return json_decode($json);
		}

		// Throw a shit error for now
		else
		{
			echo 'Error: Could not load packman.json, does it exist and is it in the right location?' .PHP_EOL;
		}
	}
}