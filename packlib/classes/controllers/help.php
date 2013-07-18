<?php defined('DS') or die('No direct script access.');

class Help {
	
	function go()
	{
		global $top_level_commands;

		echo 'Allowed Commands:' .PHP_EOL;
		foreach($top_level_commands as $command => $description) echo '    ' .$command .': ' .$description .PHP_EOL;
	}
}