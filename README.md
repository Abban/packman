Packman
=======

Minimal project module manager

**This software is in beta. The structure of the json is likely to change on the way to v1**

## Installation
1. Stick the ``packman`` file, ``packman.json`` file and ``packlib`` folder into your project root. You might need to chmod ``packman`` to 777.
2. Edit ``packman.json`` to include any modules you want to install.
3. Open Terminal, navigate to the project and type ``php packman install``.

## Usage
When you want to change something just edit the ``packman.json`` file then type ``php packman update`` and it will delete your modules and replace them with updated versions. You can also update a single module with ``php packman update module-name``.

## Don't Deploy Packman
To stop Packman being deployed you can add the following to your ``.gitignore``:
     
    packlib/
    packman
    packman.json
    packman.lock

## Description of the JSON
Here's a sample of the json file:

    {
    	"name" : "packman",
    	"description" : "Packman Sample",
    	"modulesFolder" : "assets/modules",
    	"modules" : {
    		"wt-menu": {
    			"url" : "https://api.github.com/repos/%username%/%repo%/zipball/%branch%",
    			"path" : "assets/wt-modules",
    			"pathData" : {
    				"username" : "webtogether",
    				"repo" : "wt-menu",
    				"branch" : "test"
    			}
    		},
    		"typeplate": {
    			"url" : "https://api.github.com/repos/%username%/%repo%/zipball/%branch%",
    			"pathData" : {
    				"username" : "typeplate",
    				"repo" : "typeplate.github.io",
    				"branch" : "master"
    			},
    			"files" : {
                    "typeplate/scss/_typeplate.scss" : "scss",
                    "typeplate/scss/_vars-typeplate.scss" : "scss",
                    "typeplate/css" : false
                }
    		}
    	}
    }

Currently ``name`` and ``description`` do nothing. ``modulesFolder`` is the default location you want your modules in.

Modules containes each module. You can see in this example wt-menu has its own custom path specified. If you don't include the path paramater it will default to the general one. The ``url`` parameter is the online location of your files. In this you can see ``%username%``, ``%repo%``, ``%branch%`` etc. These values will be replaced by those in ``pathData``. This just makes it a bit easier to add multiple repos with the same url.

###Individual files from a repo
The typeplate module also has ``files`` specified. This is used if you only want to use certain files or folders from a repo. Only those files or folders will be imported. You can also specify a folder inside the module folder to put the files or folder inside, eg: ``typeplate/scss/_typeplate.scss`` will be installed into ``assets/modules/typeplate/scss``.

##TODO
* Option to create consistent less/sass import files and a codekit prepend javascript file that can be imported into your own stylesheets/js. This means after you update you don't have to manually include all the resources.