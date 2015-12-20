<?php
namespace app;

class Template {

private $registry;
private $layout_path;
private $vars = array();

function __construct($registry,$path) 
{
    $this->registry = $registry;
    $this->layout_path = $path;
    $this->vars['title'] = 'My MVC title';
    $this->vars['header'] = 'header';
    $this->vars['footer'] = 'footer';
}

 public function __set($index, $value)
 {
    $this->vars[$index] = $value;
 }

     /*
      * shows requested view
      * 
     * @param $name - name of view to load
     * @param $args - arguments to extract as variables to use in view
     */

function show($name,$args = null) {
    $path = __SITE_PATH . '/views' . '/' . $name . '.php';
    $header_path = $this->layout_path . '/' . $this->vars['header'] . '.php';
    $footer_path = $this->layout_path . '/' . $this->vars['footer'] . '.php';
    
    if (file_exists($path) == false)
    {
        throw new Exception('Template not found in '. $path);
    }
    
    if( $args !== null)
        $this->vars = array_merge($this->vars,$args);
    //set args as variables
    extract($this->vars);
    
    include ($header_path);   
    include ($path); 
    include ($footer_path);   
}


}