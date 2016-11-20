<?php

namespace Igorwanbarros\Php2HtmlLaravel\Panel;


use Igorwanbarros\Php2Html\Panel\PanelView;

class PanelViewLaravel extends PanelView
{
    protected static $colorPanel = self::PANEL_BOOTSTRAP_DEFAULT;


    public function __construct($title = null, $body = null, $footer = null)
    {
        parent::__construct($title, $body, $footer);

        $this->classPanel = static::$colorPanel;
    }


    public static function setColorPanel($colorPanel)
    {
        self::$colorPanel = $colorPanel;
    }
}
