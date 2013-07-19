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
	private $mode;

	private $url;
	private $path;
	private $name;
	private $files;
	private $absolutePaths;

	private $lockName;
	private $lockPath;
	private $lockFiles;
	private $lockAbsolutePaths;



	/**
	 * Add the module details
	 * 
	 * @param  string $name
	 * @param  string $mode
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

			$this->url           = $url;
			$this->path          = BASEPATH.(isset($module->path) ? $module->path : $json->path).DS.$name;
			$this->name          = $name;
			$this->files         = (isset($module->files)) ? $module->files : false;
			$this->absolutePaths = (isset($module->absolutePaths)) ? $module->absolutePaths : false;
		}

		// Set up the lock vars
		if($lock && is_object($lock->modules->$name))
		{
			$module = $lock->modules->$name;

			$this->installed         = true;
			$this->lockPath          = BASEPATH.(isset($module->path) ? $module->path : $lock->path).DS.$name;
			$this->lockName          = $name;
			$this->lockFiles         = (isset($module->files)) ? $module->files : false;
			$this->lockAbsolutePaths = (isset($module->lockAbsolutePaths)) ? $module->lockAbsolutePaths : false;
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

			// Move single files and folders
			if($this->files)
			{
				foreach($this->files as $file => $loc)
				{
					$from = $latest.DS.$file;
					$to = ($this->absolutePaths ? BASEPATH : $this->path.DS).($loc ? $loc.DS : '').basename($file);

					// If its a folder
					if(is_dir($from))
					{
						echo 'Moving folder ' .$from .' to ' .$to .PHP_EOL;
						File::mvdir($from, $to);
					}
					// If its a file
					elseif(file_exists($from))
					{
						echo 'Moving file ' .$from .' to ' .$to .PHP_EOL;
						File::mkdir(($this->absolutePaths ? BASEPATH : $this->path.DS).($loc ? $loc.DS : ''));
						File::move($from, $to);
					}
					else
					{
						echo 'Error, file not found ' .$from .PHP_EOL;
					}
				}
			}
		}
		// No files specified just stick the contents of the entire repo into the module folder
		else
		{
			// Move files by order created deleting as we go
			echo 'Moving entire repo to ' .$this->path .PHP_EOL;
			$latest = File::latest($work.DS.'unzip')->getRealPath();
			@chmod($latest, 0777);
			File::mvdir($latest, $this->path);
		}

		// Remove the temp folder
		File::rmdir($work.DS.'unzip');

		// Close zip and delete
		$zip->close();
		@unlink($target);

		// Set the module to installed
		$this->setInstalled();
	}



	/**
	 * update a module
	 * 
	 * @return void
	 */
	function update()
	{
		$this->delete();
		if(!$deleting) $this->install();
	}



	/**
	 * delete a module
	 * 
	 * @return void
	 */
	function delete()
	{
		if($this->installed)
		{
			if($this->lockFiles)
			{
				foreach($this->lockFiles as $file => $loc)
				{
					$path = ($this->lockAbsolutePaths ? BASEPATH : $this->lockPath.DS).($loc ? $loc.DS : '').basename($file);

					// If its a directory
					if(is_dir($path))
					{
						echo 'Deleting folder ' .$path .PHP_EOL;
						File::rmdir($path);
					}
					// If its a file
					elseif(file_exists($path))
					{
						echo 'Deleting file ' .$path .PHP_EOL;
						File::delete($path);
					}
				}
			}

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



	/**
	 * Sets the module to installed or not
	 * 
	 * @param boolean $installed
	 */
	protected function setInstalled($installed = true)
	{
		$this->installed = $installed;
		$this->lockPath  = ($installed) ? $this->path : null;
		$this->lockName  = ($installed) ? $this->name : null;
		$this->lockFiles = ($installed) ? $this->files : null;
	}
}