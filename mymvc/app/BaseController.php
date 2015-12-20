<?php
namespace app;
interface ControllerInterface
{
    function index();
}
//force every controller to implement index() method
abstract class BaseController implements ControllerInterface
{
    protected $registry;
    /*
     * @param $registry - registry to set
     */
    function __construct($registry) 
    {
        $this->registry = $registry;
    }
    
}

