<?php

class EFullCalendar extends CWidget
{
    /**
     * @var string Google's calendar URL.
     */
    public $googleCalendarUrl;

    /**
     * @var string Theme's CSS file.
     */
    public $themeCssFile;

    /**
     * @var array FullCalendar's options.
     */
    public $options=array();

    /**
     * @var array HTML options.
     */
    public $htmlOptions=array();

    /**
     * @var bool
     */
    public $loadPrintCss=false;

    /**
     * @var string Language code as ./locale/<code>.php file
     */
    public $lang;
    
    /**
     * @var string PHP file extension. Default is php.
     */
    public $ext='php';

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->lang) {
            $this->registerLocale($this->getLanguageFilePath());
        }

        $this->registerFiles();

        echo $this->showOutput();
    }

    /**
     * Registers language file.
     *
     * @param $langFile string Path to the language file.
     */
    protected function registerLocale($langFile)
    {
        if (file_exists($langFile)) {
            $this->options=CMap::mergeArray($this->options, include($langFile));
        } else {
            Yii::log(sprintf('EFullCalendar language file %s is missing', $langFile), CLogger::LEVEL_WARNING);
        }
    }

    /**
     * Gets default language file.
     */
    protected function getLanguageFilePath()
    {
        return dirname(__FILE__).'/locale/'.$this->lang.'.'.$this->ext;
    }

    /**
     * Registers assets.
     */
    protected function registerFiles()
    {
        $assetsDir=(defined(__DIR__) ? __DIR__ : dirname(__FILE__)).'/assets';
        $assets=Yii::app()->assetManager->publish($assetsDir);

        $cs=Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery-ui');

        $ext=defined('YII_DEBUG') && YII_DEBUG ? 'js' : 'min.js';
        $cs->registerScriptFile($assets.'/fullcalendar/fullcalendar.'.$ext);
        $cs->registerCssFile($assets.'/fullcalendar/fullcalendar.css');

        if ($this->loadPrintCss) {
            $cs->registerCssFile($assets.'/fullcalendar/fullcalendar.print.css');
        }
        if ($this->googleCalendarUrl) {
            $cs->registerScriptFile($assets.'/fullcalendar/gcal.js');
            $this->options['events']=$this->googleCalendarUrl;
        }
        if ($this->themeCssFile) {
            $this->options['theme']=true;
            $cs->registerCssFile($assets.'/themes/'.$this->themeCssFile);
        }

        $js='$("#'.$this->id.'").fullCalendar('.CJavaScript::encode($this->options).');';
        $cs->registerScript(__CLASS__.'#'.$this->id, $js, CClientScript::POS_READY);
    }

    /**
     * Returns the html output.
     *
     * @return string Html output
     */
    protected function showOutput()
    {
        if (! isset($this->htmlOptions['id']))
            $this->htmlOptions['id']=$this->id;

        return CHtml::tag('div', $this->htmlOptions,'');
    }
}
