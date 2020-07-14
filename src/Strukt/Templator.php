<?php

namespace Strukt;

use Strukt\Raise;

/**
* @link https://bit.ly/2Z4m8iI
*/
class Templator{

    public static $truncate_space = true;

    private function __construct(){

        //
    }

    public static function create($template, $data){

        $template = static::loop($template, $data);
        $template = static::element($template, $data);
        $template = static::condition($template, $data);

        return $template;
    }

    public static function trimBlanks($text){

        $text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);

        return $text;
    }

    public static function element($template, $data){

        $template = preg_replace_callback('#\{\{(\w+)\}\}#s', function($matches) use($data){

            list($content, $variable) = $matches;

            return $data[$variable];

        }, $template);

        return $template;
    }

    public static function loop($template, $data){

        $template = preg_replace_callback('#{\{begin:(\w+)\}\}(.+?){\{end:\w+\}\}#s', 

            function($matches) use($data){

                list($condition, $variable, $content) = $matches;

                foreach($data[$variable] as $datakey)
                    $elem[] = static::element($content, $datakey);

                $text = implode("", $elem);

                return static::trimBlanks($text);

        }, $template);

        if(static::$truncate_space)
            return static::trimBlanks($template);

        return $template;
    }

    /**
    * @link https://bit.ly/320Gpr3
    */
    public static function condition($template, $data){

        $template = preg_replace_callback('#\{if\s(.+?)}(.+?)\{/if}#s', function($matches) use ($data) {

            list($condition, $variable, $content) = $matches;
            if(isset($data[$variable]) && $data[$variable])
                return trim($content);

        }, $template);

        return $template;
    }
}