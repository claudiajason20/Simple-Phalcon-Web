<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
  public function onConstruct()
    {
        date_default_timezone_set('Europe/Amsterdam');
    }
    public function authorized()
    {
        if (!$this->isLoggedIn()) {
            return $this->response->redirect('admin/login');
        }
    }
    public function isLoggedIn()
    {
        // Check variable is defined
        if ($this->session->has('AUTH_NAME') AND $this->session->has('AUTH_EMAIL') AND $this->session->has('AUTH_USERNAME') ) {
            return true;
        }
        return false;
    }
}
