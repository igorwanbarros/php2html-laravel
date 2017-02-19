<?php

namespace Igorwanbarros\Php2HtmlLaravel\Form\Fields;

use Igorwanbarros\Php2Html\Form\Fields\Checkbox;

class CheckboxField extends Checkbox
{

    /**
     * @var string
     */
    protected $decorator;


    public function icheck()
    {
        $this->decorator = "square";

        return $this;
    }


    protected function _createFieldHtml()
    {
        $field = parent::_createFieldHtml();

        if ($this->decorator) {
            $field->addAttribute('class', $this->decorator);
        }

        return $field;
    }
}
