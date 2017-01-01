<?php

namespace Igorwanbarros\Php2HtmlLaravel\Tabs;

use Igorwanbarros\Php2Html\Tabs\TabsView;

class TabsViewLaravel extends TabsView
{

    protected $mergeTitle;

    public function __construct(array $titles = [], array $contents = [], $mergeTitle = null)
    {
        $this->mergeTitle = $mergeTitle;
        parent::__construct($titles, $contents);
    }


    protected function _preparedTitle($name, $title)
    {
        parent::_preparedTitle($name, $title);

        $this->contents[$name] = '';

        if ($this->mergeTitle && array_key_exists('data-href', $this->titles[$name])) {
            $this->titles[$name]['data-href'] = sprintf(
                $this->titles[$name]['data-href'],
                is_array($this->mergeTitle)
                    ? extract($this->mergeTitle)
                    : $this->mergeTitle
            );
        }

        return $this;
    }


    public function setTitles(array $titles)
    {
        parent::setTitles($titles);

        return $this->_orderTitles();
    }


    protected function _orderTitles()
    {
        $actual = 0;
        uasort(
            $this->titles,
            function ($arrayOne, $arrayTwo) use (&$actual) {
                ++$actual;

                if (!array_key_exists('order', $arrayOne)) {
                    $arrayOne['order'] = $actual;
                }

                if (!array_key_exists('order', $arrayTwo)) {
                    $arrayTwo['order'] = $actual;
                }

                return ($arrayOne['order'] < $arrayTwo['order']) ? false : true;
            });

        return $this;
    }
}