<?php defined('DS') or die('No direct script access.');
/**
 * Packman - Minimal Front End Package Manager
 *
 * @package  Packman
 * @since  0.2
 * @author   Abban Dunne <himself@abandon.ie>
 * @link     http://abandon.ie
 */

class Module {

	private $installed = false;
	private $deleting = false;

	private $url;
	private $path;
	private $name;
	private $files;
	private $mode;

	private $lockName;
	private $lockPath;
	private $lockFiles;

	/**
	 * Add the module details
	 * 
	 * @param  string $name
	 * @param  object $module
	 * @param  string $path
	 * @return bool
	 */
	public function setup($name, $mode)
	{
		$json = Json::getPackageFile();
		$lock = Json::getLockFile();
		$this->mode = $mode;

		// Set up the json vars
		if($json && is_object($json->modules->$name))
		{
			$module = $json->modules->$name;

			// Create the repo url from data passed
			$url = $module->url;
			if($module->pathData)
			{
				foreach($module->pathData as $title => $data) $url = str_replace('%' .$title .'%', $data, $url);
			}

			$this->url   = $url;
			$this->path  = BASEPATH.(isset($module->path) ? $module->path : $json->path).DS.$name;
			$this->name  = $name;
			$this->files = (isset($module->files)) ? $module->files : false;
		}

		// Set up the lock vars
		if($lock && is_object($lock->modules->$name))
		{
			$module = $lock->modules->$name;

			$this->installed = true;
			$this->lockPath  = BASEPATH.(isset($module->path) ? $module->path : $lock->path).DS.$name;
			$this->lockName  = $name;
			$this->lockFiles = (isset($module->files)) ? $module->files : false;
		}

		if(!$this->name && $this->lockName)
		{
			$this->deleting = true;
		}

		return ($this->name || $this->lockName);
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

		// Set the module to installed
		$this->setInstalled();
	}

	function update()
	{
		$this->delete();
		if(!$deleting) $this->install();
	}

	function delete()
	{
		if($this->installed)
		{
			File::rmdir($this->lockPath);
			$this->setInstalled(false);
		}
		else
		{
			echo 'Error, cannot remove ' .$this->name .' as it is not installed.' .PHP_EOL;
		}
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

	protected function setInstalled($installed = true)
	{
		$this->installed = $installed;
		$this->lockPath  = ($installed) ? $this->path : null;
		$this->lockName  = ($installed) ? $this->name : null;
		$this->lockFiles = ($installed) ? $this->files : null;
	}
}