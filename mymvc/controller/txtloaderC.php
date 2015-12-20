<?php
class txtloaderC extends \app\BaseController 
{
    public function index() 
    {       
        $result_message = '';
        if(isset($_POST["submit"]))
        {
            $txtloader = new TxtLoader();
            $result_message = $txtloader->load($_FILES["fileToUpload"]);
        }
        $this->registry->template->show('txtloader',array('result_message'=>$result_message,
                                                          'file_added'=>false
                                                          )
        );
    }
}