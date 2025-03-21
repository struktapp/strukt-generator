<?php

namespace Strukt;

use Strukt\Raise;

/**
* @link https://bit.ly/2Z4m8iI
* 
* @author Moderator <pitsolu@gmail.com>
*/
class Templator{

    private function __construct(){

        //
    }

    /**
     * @param string $template
     * @param array $data
     * 
     * @return string
     */
    public static function create(string $template, array $data):string{

        $template = static::loop($template, $data);
        $template = static::element($template, $data);
        $template = static::condition($template, $data);

        return $template;
    }

    /**
     * @param string|array $text
     * 
     * @return array|string
     */
    public static function trimBlanks(string|array $text):array|string{

        $text = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $text);

        return $text;
    }

    /**
     * @param string $template 
     * @param array $data
     * 
     * @return string|array|null
     */
    public static function element(string $template, array $data):string|array|null{

        $template = preg_replace_callback('#\{\{(\w+)\}\}#s', function($matches) use($data){

            list($content, $variable) = $matches;

            return $data[$variable];

        }, $template);


        return $template;
    }

    /**
     * @param string $template
     * @param array $data
     * 
     * @return string|array|null
     */
    public static function loop(string $template, array $data):string|array|null{

        $template = preg_replace_callback('#{\{begin:(\w+)\}\}(.+?){\{end:\w+\}\}#s', 

            function($matches) use($data){

                list($condition, $variable, $content) = $matches;

                foreach($data[$variable] as $datakey)
                    // print_r([$datakey]);
                    $elem[] = static::element($content, $datakey);

                $text = trim(implode("", $elem));

                return static::trimBlanks($text);

        }, $template);

        return $template;
    }

    /**
    * @link https://bit.ly/320Gpr3
    * 
    * @param string $template
    * @param array $data
    * 
    * @return string
    */
    public static function condition(string $template, array $data):string{

        $template = preg_replace_callback('#\{if\s(.+?)}(.+?)\{/if}#s', function($matches) use ($data) {

            list($condition, $variable, $content) = $matches;
            if(isset($data[$variable]) && $data[$variable])
                return trim($content);

        }, $template);

        return trim($template);
    }
}