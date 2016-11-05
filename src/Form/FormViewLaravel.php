<?php

namespace Igorwanbarros\Php2HtmlLaravel\Form;

use Igorwanbarros\Php2Html\Form\FormView;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\DateField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TextField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TimeField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\ButtonField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\HiddenField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\NumberField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\CheckboxField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\DateTimeField;
use Igorwanbarros\Php2HtmlLaravel\Form\Fields\TextAreaField;

class FormViewLaravel extends FormView
{

    protected $csrfToken = true;

    protected $submitSave = true;

    protected $createFieldsFromDatabase = true;

    protected $modelClass;

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


    public function __construct($action, $modelClass)
    {
        $this->modelClass = $modelClass;

        if ($this->csrfToken) {
            $this->addField(HiddenField::create('_token', '', csrf_token()));
        }

        parent::__construct($action, 'GET', []);
    }


    public function toStart()
    {
        parent::toStart();

        if ($this->createFieldsFromDatabase) {
            $this->_createFieldsFromDatabase();
        }
    }


    protected function _createFieldsFromDatabase()
    {
        $class = $this->modelClass;
        $schema = $class::getSchema();

        foreach ($schema as $col) {
            if (array_key_exists($col->COLUMN_NAME, $this->removeFieldsByName)) {
                continue;
            }

            $field = TextField::class;

            if (array_key_exists($col->DATA_TYPE, $this->tableClassReferences)) {
                $field = $this->tableClassReferences[$col->DATA_TYPE];
            }

            if (array_key_exists($col->COLUMN_NAME, $this->convertTypeFieldsByName)) {
                $field = $this->convertTypeFieldsByName[$col->COLUMN_NAME];
            }

            $field = $field::create($col->COLUMN_NAME, studly_case($col->COLUMN_NAME));

            if ($col->CHARACTER_MAXIMUM_LENGTH > 0) {
                $field->addAttribute('maxlength', $col->CHARACTER_MAXIMUM_LENGTH);
                $field->addRule("|max:{$col->CHARACTER_MAXIMUM_LENGTH}");
            }

            if ($col->IS_NULLABLE == 'NO') {
                $field->addRule('|required');
            }

            $this->addField($field);
        }
    }


    public function search($action = null)
    {
        $this->action = $action ?: str_replace(['/salvar', '/store'], '', $this->action);
        $this->setMethod('GET');

        $this->submitSave = ButtonField::create('submit', '', ' Pesquisar')
            ->addAttribute('class', 'btn btn-default fa fa-search')
            ->setType('submit');

        return $this;
    }


    public function render($template = null)
    {
        if ($this->submitSave) {
            $submitSave = $this->submitSave instanceof ButtonField
                ? $this->submitSave
                : ButtonField::create('submit', 'Salvar', ' Salvar')
                    ->setType('submit')
                    ->addAttribute('class', 'btn btn-success fa fa-save');

            $this->addField($submitSave);
        }

        return parent::render($template);
    }
}