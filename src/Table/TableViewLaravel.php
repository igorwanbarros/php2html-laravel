<?php

namespace Igorwanbarros\Php2HtmlLaravel\Table;

use Igorwanbarros\Php2Html\Table\TableView;


class TableViewLaravel extends TableView
{

    protected $paginate = 10;

    protected $urlPaginator;


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
        //$this->collection = $this->collection->paginate($this->paginate);
        $paginator = new TablePagination($this->collection);
        $paginator->setUrl($this->getUrlPaginator());

        $this->setPaginator($paginator->render());

        return parent::render($template);
    }


    public function getPaginate()
    {
        return $this->paginate;
    }


    public function setPaginate($paginate)
    {
        $this->paginate = $paginate;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getUrlPaginator()
    {
        return $this->urlPaginator;
    }


    /**
     * @param $urlPaginator
     * @return $this
     */
    public function setUrlPaginator($urlPaginator)
    {
        $this->urlPaginator = $urlPaginator;
        return $this;
    }
}
