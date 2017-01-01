<?php

namespace Igorwanbarros\Php2HtmlLaravel\Form\Fields;

use Igorwanbarros\BaseLaravel\Models\BaseModel;
use Igorwanbarros\Php2Html\TagHtml;

/**
 * Class AutocompleteField
 * @package Igorwanbarros\Php2HtmlLaravel\Form\Fields
 *
 * @method  $this    optionInput(mixed $value)
 * @method  $this    optionMinLength(mixed $value)
 * @method  $this    optionMaxItem(mixed $value)
 * @method  $this    optionDynamic(mixed $value)
 * @method  $this    optionDelay(mixed $value)
 * @method  $this    optionOrder(mixed $value)
 * @method  $this    optionOffset(mixed $value)
 * @method  $this    optionHint(mixed $value)
 * @method  $this    optionAccent(mixed $value)
 * @method  $this    optionHighlight(mixed $value)
 * @method  $this    optionGroup(mixed $value)
 * @method  $this    optionGroupOrder(mixed $value)
 * @method  $this    optionMaxItemPerGroup(mixed $value)
 * @method  $this    optionDropdownFilter(mixed $value)
 * @method  $this    optionDynamicFilter(mixed $value)
 * @method  $this    optionBackdrop(mixed $value)
 * @method  $this    optionBackdropOnFocus(mixed $value)
 * @method  $this    optionCache(mixed $value)
 * @method  $this    optionTtl(mixed $value)
 * @method  $this    optionCompression(mixed $value)
 * @method  $this    optionSuggestion(mixed $value)
 * @method  $this    optionSearchOnFocus(mixed $value)
 * @method  $this    optionResultContainer(mixed $value)
 * @method  $this    optionGenerateOnLoad(mixed $value)
 * @method  $this    optionMustSelectItem(mixed $value)
 * @method  $this    optionHref(mixed $value)
 * @method  $this    optionDisplay(mixed $value)
 * @method  $this    optionTemplate(mixed $value)
 * @method  $this    optionGroupTemplate(mixed $value)
 * @method  $this    optionCorrelativeTemplate(mixed $value)
 * @method  $this    optionEmptyTemplate(mixed $value)
 * @method  $this    optionCancelButton(mixed $value)
 * @method  $this    optionLoadingAnimation(mixed $value)
 * @method  $this    optionFilter(mixed $value)
 * @method  $this    optionMatche(mixed $value)
 */
class AutocompleteField extends TextField
{
    protected $options = [];

    protected $template = '/../../templates/%s/autocomplete.php';

    protected $input;

    protected $callbackAvailable = [
        'onInit'                    => 'function (node) {%s}',
        'onReady'                   => 'function (node) {%s}',
        'onShowLayout'              => 'function (node, query) {%s}',
        'onHideLayout'              => 'function (node, query) {%s}',
        'onSearch'                  => 'function (node, query) {%s}',
        'onResult'                  => 'function (node, query, result, resultCount, resultCountPerGroup) {%s}',
        'onLayoutBuiltBefore'       => 'function (node, query, result, resultHtmlList) {%s}',
        'onLayoutBuiltAfter'        => 'function (node, query, result) {%s}',
        'onNavigateBefore'          => 'function (node, query, event) {%s}',
        'onNavigateAfter'           => 'function (node, lis, a, item, query, event) {%s}',
        'onMouseEnter'              => 'function (node, a, item, event) {%s}',
        'onMouseLeave'              => 'function (node, a, item, event) {%s}',
        'onClick'                   => 'function (node, a, item, event) {%s}',
        'onClickBefore'             => 'function (node, a, item, event) {%s}',
        'onClickAfter'              => 'function (node, a, item, event) {%s}',
        'onSendRequest'             => 'function (node, query) {%s}',
        'onReceiveRequest'          => 'function (node, query) {%s}',
        'onPopulateSource'          => 'function (node, data, group, path) {%s}',
        'onCacheSave'               => 'function (node, data, group, path) {%s}',
        'onSubmit'                  => 'function (node, form, item, event) {%s}',
        'onCancel'                  => 'function (node, event) {%s}',
    ];

    protected $callbackScript = [];

    protected $inputHidden = false;

    protected $labelInput = 'id';

    protected $hiddenName;

    protected $hiddenId;

    protected $hiddenValue;

    protected $table;

    protected $dataParams = [];

    protected $dataParamsDinamic = [];


    public function __construct($name, $label = '', $table = null)
    {
        parent::__construct($name, $label, null);

        $this->hiddenName = $name;
        $this->hiddenId = $this->getId();
        $this->basePath = __DIR__;
        $this->template = $this->getBasePath() . $this->getTemplate();
        $this->table = $table;
        $this->callbackScript('onClick', "$('#{$this->getHiddenId()}').val(item.{$this->labelInput});");
    }


    public static function create($name, $label = '', $table = null)
    {
        return parent::create($name, $label, $table);
    }


    public function render($template = null)
    {
        $this->attributes['class'] .= ' autocomplete typeahead';
        $this->attributes['autocomplete'] = 'off';

        $this->_renderInputs();

        $this->_defineAssets();

        return parent::render($template ?: $this->template);
    }


    public function setValue($value)
    {
        $this->hiddenValue = $value;

        if (is_string($this->table)) {
            $table = $this->table;
            $this->table = new $table;
        }

        if ($this->table instanceof BaseModel && ($display = $this->options['display'])) {
            $model = $this->table->find($value);
            $value = $model ? $model->{$display} : $value;
        }

        $this->value = $value;

        return $this;
    }


    public function getVars()
    {
        return ['autocomplete' => $this];
    }


    public function __call($name, $arguments)
    {
        if (strpos($name, 'option') !== false && array_key_exists('0', $arguments)) {
            return $this->setOption(substr($name, 6), $arguments[0]);
        }

        return $this;
    }


    public function setOption($key, $value)
    {
        $this->options[strtolower($key)] = $value;

        return $this;
    }


    public function sourceDynamic($url, $display = 'id', $dataParams = null)
    {
        $default = ['results' => [
            'ajax' => [
                'type' => 'POST',
                'url'  => $url,
                'data' => ['query' => '{{query}}'],
                'path' => 'results.data'
            ]
        ]];

        if ($dataParams) {
            $default['results']['ajax']['data'] += $dataParams;
        }

        if (is_array($url)) {
            $default = $url;
        }

        $this->setOption('source', $default);
        $this->setOption('dynamic', true);
        $this->setOption('display', $display ?: $this->getName());
        $this->setInputHidden(true);

        return $this;
    }


    public function addDataParams($key, $value, $dinamic = false)
    {
        $this->dataParams[$key] = $value;

        if ($dinamic) {
            $this->dataParamsDinamic[$key] = $value;
        }

        return $this;
    }


    public function sourceStatic(array $data, $display = null)
    {
        $default = [
            'data' => $data,
        ];

        $this->setOption('source', $default);
        $this->setOption('dynamic', false);

        if ($display) {
            $this->setOption('display', $display);
        }

        return $this;
    }


    public function callbackScript($name, $function, $keepFunction = false)
    {
        if (!array_key_exists($name, $this->callbackAvailable)) {
            return $this;
        }

        if ($keepFunction && array_key_exists($name, $this->callbackScript)) {
            $this->callbackScript[$name] = substr($this->callbackScript[$name], 0, -1) . "{$function}}";
            return $this;
        }

        $function = sprintf($this->callbackAvailable[$name], $function);
        $this->callbackScript[$name] = $function;

        return $this;
    }


    public function getInput()
    {
        return $this->input;
    }


    public function setInput($input)
    {
        $this->input = $input;
        return $this;
    }


    public function getInputHidden()
    {
        return $this->inputHidden;
    }


    public function setInputHidden($inputHidden)
    {
        $this->inputHidden = $inputHidden;

        return $this;
    }


    public function getHiddenId()
    {
        return $this->hiddenId;
    }


    public function setHiddenId($hiddenId)
    {
        $this->hiddenId = $hiddenId;
        return $this;
    }


    public function getHiddenName()
    {
        return $this->hiddenName;
    }


    public function setHiddenName($hiddenName)
    {
        $this->hiddenName = $hiddenName;
        return $this;
    }


    public function getLabelInput()
    {
        return $this->labelInput;
    }


    public function setLabelInput($labelInput)
    {
        $this->labelInput = $labelInput;
        return $this;
    }


    protected function _renderOptionsScript()
    {
        $options = [
            'input' => "#{$this->getId()}.typeahead",
            'hint' => true,
            'minLength' => 0,
            'maxItem' =>  10,
            'order' => 'asc',
            'emptyTemplate' => "<span class=\"empty\">Nenhum resultado encontrado para \"<strong>{{query}}</strong>\"</span>",
        ];

        $this->options += $options;

        $callbacks = json_encode($this->callbackScript, JSON_UNESCAPED_SLASHES);
        $callbacks = str_replace(['{"', '":', '"f', '"}', '}","'], ['{', ':', 'f', '}', '},'], $callbacks);

        $this->_renderDataParams();

        $options = json_encode($this->options, JSON_UNESCAPED_SLASHES);
        $options = substr($options, 0, -1);
        $options .= sprintf(',callback: %s}', $callbacks);
        $options = str_replace(['"data":"', '"data": "', '\'}"'], ['"data":','"data":', '\'}'], $options);

        return $options;
    }


    protected function _renderDataParams()
    {
        if (!isset($this->options['source']['results']['ajax']['data'])) {
            return;
        }

        $this->dataParams += $this->options['source']['results']['ajax']['data'];
        $data = '{';

        foreach ($this->dataParams as $key => $value) {
            if (array_key_exists($key, $this->dataParamsDinamic)) {
                $data .= "{$key}:{$value},";
                continue;
            }

            $data .= "{$key}:'{$value}',";
        }

        $this->options['source']['results']['ajax']['data'] = substr($data, 0, -1) . '}';
    }


    protected function _renderInputs()
    {
        $this->input = $this->_createFieldHtml();

        if ($this->inputHidden) {
            $this->inputHidden = TagHtml::source($this->tagName)
                ->addAttributeRaw('type', 'hidden')
                ->addAttributeRaw('name', $this->name)
                ->addAttributeRaw('id', $this->getId())
                ->addAttributeRaw('value', $this->hiddenValue);

            $this->setName("autocomplete_{$this->name}")
                 ->setId("autocomplete_{$this->id}");

            $this->input = TagHtml::source($this->tagName)
                ->addAttributeRaw('type', $this->type)
                ->addAttributeRaw('name', $this->name)
                ->addAttributeRaw('id', $this->getId())
                ->addAttributeRaw('value', $this->value);
        }

        $this->_addAttributesFieldHtml($this->input);
    }


    protected function _defineAssets()
    {
        $assets = app('assets');
        $assets->addStyle(url('assets/base-laravel/jquery.typeahead/jquery.typeahead.min.css'));
        $assets->addScript(url('assets/base-laravel/jquery.typeahead/jquery.typeahead.min.js'));
        $assets->addScriptInline(
            "$.typeahead({$this->_renderOptionsScript()});"
        );
    }
}
