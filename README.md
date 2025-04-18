# ChameleonThemes

Provides a simple framework to easily create sub-skins (completed with Chameleon components, scripts and styles) for the Chameleon skin.
The extension includes additional Chameleon sub-skins/themes.

Please check https://www.mediawiki.org/wiki/Extension:ChameleonThemes for the official documentation.


Example conf:

```
wfLoadExtension( 'Bootstrap' );
wfLoadSkin( 'chameleon' );
 
wfLoadExtension( 'ChameleonThemes' );
$wgDefaultSkin = 'chameleon';

$wgChameleonThemesTheme = 'ak';
```

that's it!


Resources of a specific theme can be edited according to the following structure:

```
$IP/extensions/ChameleonThemes/resources/ak
|	resources.php
|
└───components
|	|	...
|
└───layout
|	|	layout.xml
|
└───scripts
|	|	script.js
|
└───styles
|	|	f_variables.scss
|	|	f_bootswatch.scss
|	|	...
|
└───layout
	|	layout.xml

```
	
	
