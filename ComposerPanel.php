<?php
namespace bookin\composer\web;


use yii\debug\Panel;

class ComposerPanel extends Panel
{
    public function getName()
    {
        return 'Composer';
    }

    public function getSummary()
    {
        $url = $this->getUrl();
        $count = count($this->data);
        return "<div class=\"yii-debug-toolbar-block\"><a href=\"$url\">{$this->getName()}</a></div>";
    }
}