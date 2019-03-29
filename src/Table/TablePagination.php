<?php

namespace Igorwanbarros\Php2HtmlLaravel\Table;

use Igorwanbarros\Php2Html\ViewAbstract;

class TablePagination extends ViewAbstract
{

    protected $template = '/../templates/%s/table-pagination.php';

    protected $collection;

    protected $url;


    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->basePath = __DIR__;
    }


    public function getVars()
    {
        return ['paginator' => $this->collection, 'baseUrl' => $this->getUrl()];
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


    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}
