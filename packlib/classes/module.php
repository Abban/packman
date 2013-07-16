<?php defined('DS') or die('No direct script access.');

class Module {

	private $url;
	private $path;
	private $name;
	private $files;
	private $mode;
	
	/**
	 * Add the module details
	 * 
	 * @param  string $name
	 * @param  object $module
	 * @param  string $path
	 * @return void
	 */
	public function setup($name, $module, $path, $mode)
	{
		// Create the repo url from data passed
		$url = $module->url;
		if($module->pathData)
		{
			foreach($module->pathData as $title => $data) $url = str_replace('%' .$title .'%', $data, $url);
		}

		$this->url = $url;
		$this->path = BASEPATH.(isset($module->path) ? $module->path : $path).DS.$name;
		$this->name = $name;
		$this->files = (isset($module->files)) ? $module->files : false;
		$this->mode = $mode;
	}

	/**
	 * Download the zip and extract the files into the modules folder
	 * 
	 * @return void
	 */
	function install()
	{
		echo $this->mode .' ' .$this->name .PHP_EOL;

		// Set paths and name
		$work = BASEPATH.PACKLIB.DS.TEMP;
		$target = $work.'packman-module.zip';

		// Make the temp folder if it doesn't already exist
		if(!is_dir($work)) mkdir($work, 0700, true);

		// Download the file
		echo 'Downloading zip from ' .$this->url .PHP_EOL;
		File::put($target, $this->download($this->url));

		// Create Zip object
		echo 'Extracting and ' .strtolower($this->mode) .' ' .$this->name .PHP_EOL;
		$zip = new ZipArchive;
		$zip->open($target);

		// Make a directory to put the zip contents in
		mkdir($work.DS.'unzip');

		// Extract the zip
		$zip->extractTo($work.DS.'unzip');

		// If files are specified just move them
		if($this->files)
		{
			$latest = File::latest($work.DS.'unzip')->getRealPath();
			foreach($this->files as $file)
			{
				if(file_exists($latest.DS.$file))
				{
					echo 'Moving ' .$latest.DS.$file .' repo to ' .$this->path.DS.basename($file) .PHP_EOL;
					File::mkdir($this->path);
					File::move($latest.DS.$file, $this->path.DS.basename($file));
				}
				else
				{
					echo 'Error, file not found ' .$latest.DS.$file .PHP_EOL;
				}
			}
			File::rmdir($work.DS.'unzip');
		}
		// No files specified just stick the contents of the entire repo into the module folder
		else
		{
			// Move files by order created deleting as we go
			echo 'Moving entire repo to ' .$this->path .PHP_EOL;
			$latest = File::latest($work.DS.'unzip')->getRealPath();
			@chmod($latest, 0777);
			File::mvdir($latest, $this->path);
			File::rmdir($work.DS.'unzip');
		}

		// Close zip and delete
		$zip->close();
		@unlink($target);
	}

	/**
	 * Download a remote zip archive from a URL.
	 *
	 * @param  string  $url
	 * @return string
	 */
	protected function download($url)
	{
		$remote = file_get_contents($url);
		if ($remote === false)
		{
			throw new \Exception("Error downloading the requested bundle.");
		}

		return $remote;
	}
}