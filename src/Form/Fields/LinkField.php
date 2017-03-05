<?php

namespace Igorwanbarros\Php2HtmlLaravel\Form\Fields;

use Igorwanbarros\Php2Html\TagHtml;

class LinkField extends HtmlField
{

    protected $tagName = 'a';


    public function __construct($name, $value, $url)
    {
        parent::__construct($name, $this->tagName, $value);
        $this->attributes['href'] = url($url);
    }


    public function setIconClass($iconClass)
    {
        $this->texto = "<i class=\"{$iconClass}\"></i> {$this->texto}";

        return $this;
    }


    protected function _createFieldHtml()
    {
        $field = TagHtml::source($this->tagName, $this->texto ?: $this->name)
            ->addAttributeRaw('id', $this->name);

        return $this->_setPersonalizationAttribute($field);
    }
}