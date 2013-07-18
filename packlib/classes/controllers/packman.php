<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Packman {

	static function go()
	{
		global $top_level_commands, $argv;
		$commands = array_keys($top_level_commands);

		if(in_array($argv[1], $commands))
		{
			$module = new $argv[1];
			$module->go($argv);
		}
		elseif(!isset($argv[1]))
		{
			echo 'You didn\'t enter a command. Try typing \'php packman help\' to see supported commands.' .PHP_EOL;
		}
		else
		{
			echo '\'' .$argv[1] .'\' is not a valid command. Try typing \'php packman help\' to see supported commands.' .PHP_EOL;
		}
	}
}