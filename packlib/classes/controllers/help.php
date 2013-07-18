<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Help {
	
	function go()
	{
		global $top_level_commands;

		echo 'Allowed Commands:' .PHP_EOL;
		foreach($top_level_commands as $command => $description) echo '    ' .$command .': ' .$description .PHP_EOL;
	}
}