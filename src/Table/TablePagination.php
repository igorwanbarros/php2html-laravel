<?php

namespace Igorwanbarros\Php2HtmlLaravel\Table;

use Igorwanbarros\Php2Html\ViewAbstract;

class TablePagination extends ViewAbstract
{

    protected $template = '/../templates/%s/table-pagination.php';

    protected $collection;


    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->basePath = __DIR__;
    }


    public function getVars()
    {
        return ['paginator' => $this->collection];
    }


    public function getCollection()
    {
        return $this->collection;
    }


    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }
}
