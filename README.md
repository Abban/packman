Packman
=======

Minimal project module manager

## Installation
1. Stick the ``packman`` file, ``packman.json`` file and ``packlib`` folder into your project root.
2. Edit ``packman.json`` to include any modules you want to install.
3. Open Terminal, navigate to the project and type ``php packman install``.

## Description of the JSON
    {
    	"name" : "packman", //Name: currently does nothing
    	"description" : "Packman Sample", //Description: currently does nothing
    	"modulesFolder" : "assets/modules", //Modules Folder: Where the modules will be installed
    	"modules" : { //All modules you want, names are the folder created
    		"wt-menu": { //No files are specified so entire repo will be imported
    			"url" : "https://api.github.com/repos/%username%/%repo%/zipball/%branch%", //URL of the repo
    			"pathData" : { //These vars will be inserted into the appropriate place in the URL
    				"username" : "webtogether",
    				"repo" : "wt-menu",
    				"branch" : "test"
    			}
    		},
    		"typeplate": { // Files to be installed are specified
    			"url" : "https://api.github.com/repos/%username%/%repo%/zipball/%branch%",
    			"pathData" : {
    				"username" : "typeplate",
    				"repo" : "typeplate.github.io",
    				"branch" : "master"
    			},
    			"files" : [
    				"typeplate/scss/_typeplate.scss",
    				"typeplate/scss/_vars-typeplate.scss"
    			]
    		}
    	}
    }