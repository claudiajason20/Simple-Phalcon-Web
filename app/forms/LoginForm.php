<?php
namespace App\Forms;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
// Validation
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
class LoginForm extends Form
{
    public function initialize()
    {

        $email = new Text('username', [
            "class" => "form-control",
            "placeholder" => "Username"
        ]);
        // form email field validation
        $email->addValidators([
            new PresenceOf(['message' => 'The username is required']),
        ]);
        $password = new Password('password', [
            "class" => "form-control",
            "placeholder" => "Password"
        ]);
        // password field validation
        $password->addValidators([
            new PresenceOf(['message' => 'Password is required']),
            new StringLength(['min' => 5, 'message' => 'Password is too short. Minimum 5 characters.']),
        ]);
        $submit = new Submit('submit', [
            "value" => "Login",
            "class" => "btn btn-primary",
        ]);
        $this->add($email);
        $this->add($password);
        $this->add($submit);
    }
}
