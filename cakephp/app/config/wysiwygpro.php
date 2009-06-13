<?php 
//Place this file in your app/config directory
//WYSIWYGPro settings
//WYSIWYGPro Developer Documentation - http://www.wysiwygpro.com/index.php?id=56
$config['Wysiwygpro']['htmlCharset'] = 'UTF-8';
//$config['Wysiwygpro']['safariSupport'] = true; //This setting is included in the documentation but apparently not actually available
$config['Wysiwygpro']['operaSupport'] = true;

//Disabled but likely requested features: fontcolor, highlight
$config['Wysiwygpro']['disableFeatures'] = array(array('print','outdent','indent','full','fontcolor','spacer','emoticon','snippets','highlight','dirltr','dirrtl','bookmark'));

//Add buttons not included by default
$config['Wysiwygpro']['addRegisteredButton'] = array('document','after:link');

//Provide a list of styles that users can choose from
$config['Wysiwygpro']['stylesMenu'] = array( 
       'p' => 'Paragraph',
       'div' => 'Div',
       'h2' => 'Heading 2',
       'h3' => 'Heading 3',
       'h4' => 'Heading 4',
       'h5' => 'Heading 5',
       'blockquote' => 'Blockquote',
       'p class="warning"' => 'Warning Box' //Example of a style with a class
);

//Directories to include for browsing and file inclusion
//Any unspecified settings will use the defaults for the directory type
//All included directories will be available for linking directly to the files
$config['Wysiwygpro']['directories'] = array(
      array('type' => 'image'),
      array('type' => 'document'),
      array('type' => 'media'),
      array( //Example of including a custom directory
         'type' => 'image',
         'dir' => WWW_ROOT . 'img/mine',
         'URL' => '/img/mine',
         'name' => 'My Images',
         'editImages' => true,
         'renameFiles' => true,
         'renameFolders' => true,
         'deleteFiles' => true,
         'deleteFolders' => true,
         'copyFiles' => true,
         'copyFolders' => true,
         'moveFiles' => true,
         'moveFolders' => true,
         'upload' => true,
         'overwrite' => true,
         'createFolders' => true,
         'filters' => array('Thumbnails')
      ),
);

//Helper Settings
$config['Wysiwygpro']['_inline_headers'] = false;
$config['Wysiwygpro']['_editor_width'] = '100%';
$config['Wysiwygpro']['_editor_height'] = '300';
$config['Wysiwygpro']['_directory_permissions'] = '0777'; //Permissions for default created directory

//Directory type defaults
$config['Wysiwygpro']['_directory_settings'] = array(
   //images
   'image' => array(
      'type' => 'image',
      'dir' => WWW_ROOT . 'img',
      'URL' => '/img',
      'name' => 'All Images',
      'editImages' => false,
      'renameFiles' => false,
      'renameFolders' => false,
      'deleteFiles' => false,
      'deleteFolders' => false,
      'copyFiles' => true,
      'copyFolders' => true,
      'moveFiles' => false,
      'moveFolders' => false,
      'upload' => true,
      'overwrite' => false,
      'createFolders' => true,
      'filters' => array('Thumbnails')
   ),
   //Any file to be linked to, including images and videos
   'document' => array(
      'type' => 'document',
      'dir' => WWW_ROOT . 'files' . DS . 'docs',
      'URL' => '/files/docs',
      'name' => 'All Documents',
      'editImages' => true,
      'renameFiles' => true,
      'renameFolders' => true,
      'deleteFiles' => true,
      'deleteFolders' => true,
      'copyFiles' => true,
      'copyFolders' => true,
      'moveFiles' => true,
      'moveFolders' => true,
      'upload' => true,
      'overwrite' => true,
      'createFolders' => true,
      'fitlers' => array()
   ),
   //Video, flash, etc
   'media' => array(
      'type' => 'media',
      'dir' => WWW_ROOT . 'files' . DS . 'media',
      'URL' => '/files/media',
      'name' => 'All Media',
      'editImages' => false,
      'renameFiles' => true,
      'renameFolders' => true,
      'deleteFiles' => true,
      'deleteFolders' => true,
      'copyFiles' => true,
      'copyFolders' => true,
      'moveFiles' => true,
      'moveFolders' => true,
      'upload' => true,
      'overwrite' => true,
      'createFolders' => true,
      'filters' => array()
   )
);
?>