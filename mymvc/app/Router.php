<?php
namespace app;
class Router {
    private $registry;
    private $path;
    
    public $file;
    public $controller;
    public $action; 
    
    /*
     * @param $registry - set global variables
     * @param $path - path to controller
     */
    function __construct($registry,$path) 
    {
        if (is_dir($path) == false)
        {
            throw new Exception ('Invalid controller path: `' . $path . '`');
        }
        $this->path = $path;
        $this->registry = $registry;
    }
    //call action in controller
    public function loader()
    {
        $this->initController();

        if (is_readable($this->file) == false)
        {   //no controller found, 404 error
            $this->file = $this->path.'/error404.php';
            $this->controller = 'error404';
        }

        include $this->file;
        //force controllers to have 'C' letter
        $class = $this->controller . 'C';
        $controller = new $class($this->registry);

        if (is_callable(array($controller, $this->action)) == false)
        {
            $action = 'index';
        }
        else
        {
            $action = $this->action;
        }
        //call action
        $controller->$action();
    }
    //sets controller and aciton. If no action or controller is found, redirect to index action or controller
    private function initController() {
        //requests must be either get or post
        if($_SERVER['REQUEST_METHOD']==='GET' 
        || $_SERVER['REQUEST_METHOD']==='POST'){
            $route = (empty($_REQUEST['r'])) ? '' : $_REQUEST['r'];

            if (empty($route))
            {
                $route = 'index';
            }
            else
            {
                // get the parts of the route
                $parts = explode('/', $route);
                $this->controller = $parts[0];
                if(isset( $parts[1]))
                {
                    $this->action = $parts[1];
                }
            }

            if (empty($this->controller))
            {
                $this->controller = 'index';
            }
            if (empty($this->action))
            {
                $this->action = 'index';
            }
            $this->file = $this->path .'/'. $this->controller . 'C.php';
        }
    }
}

