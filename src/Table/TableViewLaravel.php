<?php

namespace Igorwanbarros\Php2HtmlLaravel\Table;

use Igorwanbarros\Php2Html\Table\TableView;


class TableViewLaravel extends TableView
{
    protected $template = 'templates/semantic/table.php';

    public function __construct(array $headers, $collection = null)
    {
        parent::__construct($headers, $collection);

        $this->classTable   = 'table table-striped table-hover';
        $this->classTd      = 'text-center vertical-align';
        $this->classThead   = 'text-center';
        $this->classTfooter = 'text-center';
    }


    public function render($template = null)
    {
        $this->collection = $this->collection->paginate(10);
        $paginator = view('html-element.paginator')->with('paginator', $this->collection);

        $this->setPaginator($paginator->render());

        return parent::render($template);
    }
}