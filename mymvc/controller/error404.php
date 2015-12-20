<?php

class error404C extends \app\BaseController 
{
    public function index() 
    {
        $this->registry->template->show('error404');
    }
}
