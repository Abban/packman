<?php defined('DS') or die('No direct script access.');

class Module {

	private $url;
	private $path;
	private $name;
	private $files;
	
	/**
	 * Add the module details
	 * 
	 * @param  string $name
	 * @param  object $module
	 * @param  string $path
	 * @return void
	 */
	public function setup($name, $module, $path)
	{
		// Create the repo url from data passed
		$url = $module->url;
		if($module->pathData)
		{
			foreach($module->pathData as $title => $data) $url = str_replace('%' .$title .'%', $data, $url);
		}

		$this->url = $url;
		$this->path = BASEPATH.$path.DS.$name;
		$this->name = $name;
		$this->files = $module->files;
	}

	/**
	 * Download the zip and extract the files into the modules folder
	 * 
	 * @return void
	 */
	function install()
	{
		// Set paths and name
		$work = BASEPATH.PACKLIB.DS.TEMP;
		$target = $work.'packman-module.zip';

		// Make the temp folder if it doesn't already exist
		if(!is_dir($work)) mkdir($work, 0700, true);

		// Download the file
		File::put($target, $this->download($this->url));

		// Create Zip object
		$zip = new ZipArchive;
		$zip->open($target);

		// Make a directory to put the zip contents in
		mkdir($work.'zip');

		// Extract the zip
		$zip->extractTo($work.'zip');

		// Move files by order created deleting as we go
		$latest = File::latest($work.'zip')->getRealPath();
		@chmod($latest, 0777);
		File::mvdir($latest, $this->path);
		File::rmdir($work.'zip');

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