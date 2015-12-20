<?php
class indexC extends \app\BaseController 
{
    public function index() 
    {
        $this->registry->template->welcome = 'Webbylab, welcome to my mvc movie website!';
        $this->registry->template->show('index');
    }
}

