<?php
/**
 * WYSIWYGPro helper aids in the use of WYSIWYGPro within CakePHP applications.
 * WYSIWYGPro must be purchased separately from - http://www.wysiwygpro.com/
 * Developer documentation is located - http://www.wysiwygpro.com/index.php?id=56
 * 
 * PHP versions 4 and 5
 *
 * Copyright 2009, Brightball, Inc. (http://www.brightball.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2009, Brightball, Inc. (http://www.brightball.com)
 * @link          http://github.com/aramisbear/brightball-open-source/tree/master Brightball Open Source
 * @lastmodified  $Date: 2009-04-02 13:17:10 -0500 (Thu, 2 Apr 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 
require_once("wysiwygPro/wysiwygPro.class.php");

class WysiwygproHelper extends AppHelper {
   
   var $helpers = array('Form');
   
   var $headers = '';
   var $inlineHeaders = false;
   var $editor_width = '100%';
   var $editor_height = '300';
   
   var $directory_permissions = '0777'; //Permissions for directories that are created
   
   var $defaults = null;
   
   //Apply special handling to these setting arguments
   var $special_settings = array(
         'addRegisteredButton',
         'directories' => array('filters')
      );
   
   function beforeRender() {
     $this->_set_defaults(); //Initialize
   }
   
   function afterLayout() { //If inline headers is false, this is a failsafe
      if(!empty($this->headers)) echo $this->getHeaders();
   }
   
   function _set_defaults() {
      if($this->defaults == null) {
         $this->special_settings = Set::normalize($this->special_settings);
         
         $this->defaults = array(
            'htmlCharset' => 'UTF-8',     //Place nice with internationalization  
            'safariSupport' => true,      //Not officially supported
            'operaSupport' => true,       //Not officially supported
            'disabledFeatures' => array(  //Disabled but likely requested features: fontcolor, highlight
               'print','outdent','indent','full','fontcolor','spacer','emoticon','snippets','highlight','dirltr','dirrtl','bookmark'
            ),
            'addRegisteredButton' => array('document','after:link'),
            'stylesMenu' => array( 
                   'p' => 'Paragraph',
                   'div' => 'Div',
                   'h2' => 'Heading 2',
                   'h3' => 'Heading 3',
                   'h4' => 'Heading 4',
                   'h5' => 'Heading 5',
                   'blockquote' => 'Blockquote',
                   'p class="warning"' => 'Warning Box' //Example of a style with a class
            ),
            'directories' => array( //Default directories to include array($type,$settings)
                  array('image',array()),
                  array('media',array()),
                  array('document',array())
               ),
            'directory_types' => array(
               //images
               'image' => array(
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
                  'upload' => false,
                  'overwrite' => false,
                  'createFolders' => true,
                  'filters' => array('Thumbnails')
               ),
               //Any file to be linked to, including images and videos
               'document' => array(
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
               )
         );   
      }
   }
   
   function input($fieldName, $options = array(),$settings = array()) {
      $model =& ClassRegistry::getObject($this->Form->model());
      $ret = $this->Form->input($fieldName,$options);
      
      if($model->getColumnType($this->field()) == 'text') //Convert only text areas
         return $this->replaceTextArea($fieldName,$options,$ret,$settings);
      else return $ret;
   }
   
   function addHeaders($hc = '') {
      if(!empty($hc)) $this->headers .= $hc . "\n";  
   }
   
   function getHeaders() {
      if(!empty($this->headers)) {
         $out = $this->headers;
         $this->headers = '';
         return $this->output($out);
      }
   }
   
   function replaceTextArea($fieldName, $options, $input_html, $settings = array()) { //Convert textarea html into wysiwygpro html      
      
      list($model,$field) = explode((strpos('/',$fieldName) !== false ? '/' : '.'),$fieldName);
      //Configure WYSIWYGPro      
      $editor = new wysiwygPro();
      $editor->name = 'data[' . $model . '][' . $field . ']';
      $editor->value = isset($this->data[$model][$field]) ? $this->data[$model][$field] : '';
      
      $editor->clearFontMenu();
      $editor->clearSizeMenu();
      
      $empty = Set::normalize($this->defaults);
      $settings = Set::normalize($settings);                 
      $settings = Set::merge($empty,$settings);
      
      foreach($settings AS $st => $val) {
         if(!is_scalar($st)) {
            trigger_error('Invalid setting match: ' . print_r($st,1));
         }
         elseif(!array_key_exists($st, $this->special_settings)) { //Normal                        
            if(method_exists($editor,$st)) { //Single arg functions
               $editor->{$st}($val);
            }
            else { //Variable
               $editor->{$st} = $val;
            }
         }
         else { //Special handlers
            if($st == 'directories') {
               foreach($val AS $args) {
                  if(!isset($args[1])) $args[1] = array();
                  
                  $this->addDirectory($editor, $args[0], $args[1]);
               }
            }
            elseif($st == 'addRegisteredButton') {
               $editor->addRegisteredButton($val[0],$val[1]);
            }
            else { 
              trigger_error($st . ' is a special setting that has not been handled yet.');
            }
         }
      }
      
      //Get all the HTML the way that Cake wanted to build it
      //But move the error after the label and before the editor
      
      //The Helper::output function is not used because $editor->display immediately outputs the editor HTML
      
      list($begin,$junk) = explode('<textarea',$input_html);
      $error = $this->Form->error($fieldName);   
      echo $begin . (empty($error) ? '' : $error) . '<div class="wysiwygpro">';
      
      //Output the editor
      if(!$this->inlineHeaders) $this->addHeaders($editor->fetchHeadContent());   
      
      $editor->display($this->editor_width,$this->editor_height); //width, height
      echo '</div></div>';
   }
   
   function addDirectory(&$editor, $type, $settings = array()) {
      
      if(!isset($this->defaults['directory_types'][$type])) {
         trigger_error($type . ' is not a valid directory type');
         return false;
      }
      
      $settings = Set::merge($this->defaults['directory_types'][$type],$settings);      
      $special = $this->special_settings['directories'];
      
      $dir = new wproDirectory();
      
      foreach($settings AS $st => $val) {
         if(!in_array($st,$special)) { //Default settings
            if(method_exists($dir,$st)) { //Single arg functions
               $dir->{$st}($val);
            }
            else { //Variable
               $dir->{$st} = $val;
            }
         }
         else { //Special handlers
            if($st == 'filters') {
               $dir->filters = $this->formatFilters($val);
            }
            else { 
              trigger_error($st . ' is a special setting that has not been handled yet.');
            }
         }
      }
      
      if(!file_exists($dir->dir)) mkdir($dir->dir,$this->directory_permissions,true);
      
      $editor->addDirectory($dir);
   }
   
   function formatFilters($filters) {
      $out = array();
      foreach($filters AS $f) {
         $out[] = '#' . $f . '#';
      }
      
      return $out;
   }
   
}