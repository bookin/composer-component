<?php
namespace bookin\composer\web;


use Symfony\Component\Console\Formatter\OutputFormatter;
use yii\helpers\Html;

class BootstrapOutputFormatter extends OutputFormatter
{
    private static $availableForegroundColors = array(
        /*
            TODO - обавить свои стили (css - text-white, text-magenta)
            @author - Bookin
            @date - 25.09.2015
            @time - 3:46
        */

//        30 => 'black',
        31 => 'danger',
        32 => 'success',
        33 => 'warning',
        34 => 'primary',
//        35 => 'magenta',
        36 => 'info',
//        37 => 'white'
    );
    private static $availableBackgroundColors = array(
//        40 => 'black',
        41 => 'danger',
        42 => 'success',
        43 => 'warning',
        44 => 'primary',
//        45 => 'magenta',
        46 => 'info',
//        47 => 'white'
    );
    private static $availableOptions = array(
        1 => 'bold',
        4 => 'underscore',
        //5 => 'blink',
        //7 => 'reverse',
        //8 => 'conceal'
    );

    /**
     * @param array $styles Array of "name => FormatterStyle" instances
     */
    public function __construct(array $styles = array())
    {
        parent::__construct(true, $styles);
    }

    public function format($message)
    {
        $message = preg_replace('/<info>(.*),\s*(.*)<\/info>/i', '<info>$2 ($1)</info>', $message);

        $formatted = parent::format($message);

        $clearEscapeCodes = '(?:39|49|0|22|24|25|27|28)';

        return preg_replace_callback("{\033\[([0-9;]+)m(.*?)\033\[(?:".$clearEscapeCodes.";)*?".$clearEscapeCodes."m}s", array($this, 'formatHtml'), $formatted);
    }

    private function formatHtml($matches)
    {
        $tag = 'span';
        $options = [];
        foreach (explode(';', $matches[1]) as $code) {
            if (isset(self::$availableForegroundColors[$code])) {
                $options['class']="text-".self::$availableForegroundColors[$code];
            } elseif (isset(self::$availableBackgroundColors[$code])) {
                $options['class']="text-".self::$availableForegroundColors[$code]." bg-".self::$availableBackgroundColors[$code];
            } elseif (isset(self::$availableOptions[$code])) {
                switch (self::$availableOptions[$code]) {
                    case 'bold':
                        $tag='b';
                        break;

                    case 'underscore':
                        $tag='u';
                        break;
                }
            }
        }

        return Html::tag($tag, $matches[2], $options);

    }
}