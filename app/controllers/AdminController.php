<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;

use App\Forms\RegisterForm;
use App\Forms\LoginForm;

class AdminController extends ControllerBase
{

    public $loginForm;
    public $usersModel;
    public function onConstruct() {}
    public function initialize()
    {
        $this->loginForm = new LoginForm();
        $this->usersModel = new Users();
    }

    public function loginAction()
    {
        $this->view->form = new LoginForm();
    }

    public function loginSubmitAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->redirect('admin/login');
        }
        if (!$this->security->checkToken()) {
            $this->flashSession->error("Invalid Token");
            return $this->response->redirect('admin/login');
        }
        $this->loginForm->bind($_POST, $this->usersModel);

        if (!$this->loginForm->isValid()) {
            foreach ($this->loginForm->getMessages() as $message) {
                $this->flashSession->error($message);
                $this->dispatcher->forward([
                    'controller' => $this->router->getControllerName(),
                    'action'     => 'login',
                ]);
                return;
            }
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = Users::findFirst([
            'username = :username:',
            'bind' => [
               'username' => $username,
            ]
        ]);
        if ($user) {
            if ($this->security->checkHash($password, $user->password))
            {
                $this->session->set('AUTH_ID', $user->id);
                $this->session->set('AUTH_NAME', $user->name);
                $this->session->set('AUTH_EMAIL', $user->email);
                $this->session->set('AUTH_USERNAME', $user->username);
                $this->session->set('IS_LOGIN', 1);
                return $this->response->redirect('news/manage');
            }
        } else {
            $this->security->hash(rand());
        }
        $this->flashSession->error("Invalid login");
        return $this->response->redirect('admin/login');
    }

    public function logoutAction()
    {
        $this->session->destroy();
        return $this->response->redirect('admin/login');
    }
}
