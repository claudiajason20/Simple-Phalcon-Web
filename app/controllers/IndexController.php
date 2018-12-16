<?php

use Phalcon\Mvc\Controller;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
      //Check if have login
      if ($this->session->has('IS_LOGIN'))
      {
        $this->response->redirect('news');
      }
      else {
        $this->response->redirect('/admin/login');
      }

    }

}
