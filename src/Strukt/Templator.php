<?php

namespace Strukt;

/**
* @link https://bit.ly/2Z4m8iI
*/
class Templator{

    private $template;
    private $data;
    private $stack;

    public function __construct($template){

        $this->template = $template;
    }

    public static function create($template, $data){

        $engine = new self($template);

        return $engine->process($data);
    }

    private function showVariable($name){

        if (isset($this->data[$name]))
            echo $this->data[$name];
        else
            echo '{' . $name . '}';
    }

    private function wrap($element){

        $this->stack[] = $this->data;

        foreach ($element as $k => $v){

            $this->data[$k] = $v;
        }
    }

    private function unwrap(){

        $this->data = array_pop($this->stack);
    }

    private function run(){

        ob_start ();
        eval (func_get_arg(0));

        return ob_get_clean();
    }

    public function process($data) {

        $template = $this->template;
        $this->data = $data;
        $this->stack = array();
        $template = str_replace('<', '<?php echo \'<\'; ?>', $template);
        $template = preg_replace('~\{\{(\w+)\}\}~', '<?php $this->showVariable(\'$1\'); ?>', $template);
        $template = preg_replace('~\{\{begin:(\w+)\}\}~', '<?php foreach ($this->data[\'$1\'] as $ELEMENT): $this->wrap($ELEMENT); ?>', $template);
        $template = preg_replace('~\{\{end:(\w+)\}\}~', '<?php $this->unwrap(); endforeach; ?>', $template);
        $template = '?>' . $template;

        return $this->run($template);
    }
}