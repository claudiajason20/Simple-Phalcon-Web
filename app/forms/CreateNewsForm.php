<?php
namespace App\Forms;
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email;

class CreateNewsForm extends Form
{
    public function initialize($entity = null, $options = [])
    {
        if (isset($options["edit"])) {
            $id = new Hidden('eid', [
                "required" => true,
            ]);
            $this->add($id);
        }

        $title = new Text('title', [
            "class" => "form-control",
            "placeholder" => "Article Title"
        ]);
        $title->addValidators([
            new PresenceOf(['message' => 'Article Title is required']),
        ]);
        $description = new textArea('description', [
            "class" => "form-control",
            "placeholder" => "Article Description",
            "rows" => "5"
        ]);

        $description->addValidators([
            new PresenceOf(['message' => 'Article Description is required']),
            new StringLength(['min' => 50, 'message' => 'Description is too short. Minimum 50 characters.']),
        ]);

        $publish = new Submit('publish', [
            "name" => "publish",
            "value" => "Publish",
            "class" => "btn btn-primary",
        ]);
        $this->add($title);
        $this->add($description);
        $this->add($publish);
    }
}
