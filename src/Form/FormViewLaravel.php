<?php

namespace Igorwanbarros\Php2HtmlLaravel\Form;

use Igorwanbarros\Php2Html\Form\FormView;
use Igorwanbarros\BaseLaravel\Models\BaseModel;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\DateField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TextField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TimeField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\SelectField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\ButtonField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\HiddenField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\NumberField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\CheckboxField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\DateTimeField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TextAreaField;

class FormViewLaravel extends FormView
{

    protected $template = '/../templates/%s/form-laravel.php';

    protected $csrfToken = true;

    protected $submitSave = true;

    protected $createFieldsFromDatabase = true;

    protected $modelClass;

    protected $rules = [];

    protected $tableClassReferences = [
        //Strings
        'char'          => TextField::class,
        'varchar'       => TextField::class,
        'tinytext'      => TextField::class,
        'text'          => TextAreaField::class,
        'mediumtext'    => TextAreaField::class,
        'longtext'      => TextAreaField::class,
        'binary'        => TextAreaField::class,
        'varbinary'     => TextAreaField::class,
        'enum'          => SelectField::class,
        //Numeric
        'bit'           => CheckboxField::class,
        'tinyint'       => NumberField::class,
        'smallint'      => NumberField::class,
        'mediumint'     => NumberField::class,
        'int'           => NumberField::class,
        'integer'       => NumberField::class,
        'bigint'        => NumberField::class,
        'decimal'       => NumberField::class,
        'dec'           => NumberField::class,
        'numeric'       => NumberField::class,
        'fixed'         => NumberField::class,
        'float'         => NumberField::class,
        'double'        => NumberField::class,
        'real'          => NumberField::class,
        'bool'          => NumberField::class,
        'boolean'       => NumberField::class,
        //Dates
        'date'          => DateField::class,
        'datetime'      => DateTimeField::class,
        'timestamp'     => DateTimeField::class,
        'time'          => TimeField::class,
        'year'          => DateField::class,
    ];

    protected $removeFieldsByName = [
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'deleted_at' => 'deleted_at',
    ];

    protected $convertTypeFieldsByName = [
        'id' => HiddenField::class,
    ];


    public function __construct($action, $modelClass = null)
    {
        $this->modelClass = $modelClass;

        if ($this->csrfToken) {
            $this->addField(HiddenField::create('_token', '', csrf_token()));
        }

        $this->beforeCreate();

        parent::__construct($action, 'POST', []);

        $this->afterCreate();

        $this->setBasePath(__DIR__);
    }


    public function beforeCreate() {}


    public function afterCreate() {}


    public function toStart()
    {
        parent::toStart();

        if ($this->createFieldsFromDatabase) {
            $this->_createFieldsFromDatabase();
        }
    }


    protected function _createFieldsFromDatabase()
    {
        if (!$this->modelClass) {
            return;
        }

        $class = $this->modelClass;
        $model = new $class;

        if (!($model instanceof BaseModel)) {
            return;
        }

        $schema = $class::getSchema();

        foreach ($schema as $col) {
            if (array_key_exists($col->COLUMN_NAME, $this->removeFieldsByName)) {
                continue;
            }

            $field = TextField::class;
            $value = null;

            if (array_key_exists($col->DATA_TYPE, $this->tableClassReferences)) {
                $field = $this->tableClassReferences[$col->DATA_TYPE];
            }

            if (array_key_exists($col->COLUMN_NAME, $this->convertTypeFieldsByName)) {
                $field = $this->convertTypeFieldsByName[$col->COLUMN_NAME];
            }

            if ($col->DATA_TYPE == 'enum') {
                $enum = str_replace(['enum(', ')', '\''], '', $col->COLUMN_TYPE);
                $value = explode(',', $enum);
                $value = array_combine($value, $value);
            }

            $label = title_case(str_replace('_', ' ', $col->COLUMN_NAME));

            $field = $field::create($col->COLUMN_NAME, $col->COLUMN_COMMENT ?: $label, $value);

            if ($col->CHARACTER_MAXIMUM_LENGTH > 0) {
                $field->addAttribute('maxlength', $col->CHARACTER_MAXIMUM_LENGTH);
                $field->addRule("max:{$col->CHARACTER_MAXIMUM_LENGTH}|");
            }

            if ($col->IS_NULLABLE == 'NO' && $col->COLUMN_NAME != $model->getPrimaryKey()) {
                $field->addRule('required|')
                    ->setLabelRequired();
            }

            if (strpos($col->COLUMN_NAME, 'email') !== false) {
                $field->addRule('email|');
            }

            $this->addField($field);
        }
    }


    public function search($action = null)
    {
        $this->action = $action ?: str_replace(['/salvar', '/store'], '', $this->action);
        $this->setMethod('GET');

        foreach ($this->fields as $field) {
            $field->setLabelRequired(false);
        }

        $this->submitSave = ButtonField::create('submit', '', '<i class="fa fa-search"></i> Pesquisar')
            ->addAttribute('class', 'btn btn-default')
            ->setType('submit');

        return $this;
    }


    public function render($template = null)
    {
        if ($this->submitSave) {
            $attributes = ButtonField::getPersonalizations();
            $class = isset($attributes['class'])
                ? $attributes['class']
                : 'btn btn-success fa fa-save';

            $submitSave = $this->submitSave instanceof ButtonField
                ? $this->submitSave
                : ButtonField::create('submit', 'Salvar', ' Salvar')
                    ->setType('submit')
                    ->addAttribute('class', $class);

            $this->addField($submitSave);
        }

        return parent::render($template);
    }


    public function getRules()
    {
        foreach ($this->fields as $field) {
            $rules = $field->getRules();

            if ($rules) {
                $rules = strrpos($rules, '|') !== false
                    ? substr($rules, 0, -1)
                    : $rules;

                $this->rules[$field->getName()] = $rules;
            }
        }

        return $this->rules;
    }


    public function setAction($action)
    {
        if (strpos($action, 'http') === false) {
            $action = url($action);
        }

        return parent::setAction($action);
    }
}
