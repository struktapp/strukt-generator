<?php

namespace Strukt;

class ClassBuilder{

	private $tpl_class;
	private $tpl_method;
	private $methods;
	private $params;

	public function __construct(array $params){

		$this->methods = [];
		$this->params = $params;
		$this->tpl_class = "namespace {{ns}};\n
class {{class_name}}{{extends}}{{implements}}{\n
{{body}}
}";
		$this->tpl_method = "{{docblock}}
public function {{name}}({{params}}){\n
{{body}}
}";
	}

	public function addInit(?array $params = null){

		$this->methods[] = str(template($this->tpl_method, [

			"name"=>"__construct",
			"params"=>"",
			"body"=>"\t//"

		]))->pad()->block()->left();;

		return $this;
	}

	public function __toString(){

		$implements = null;
		if(array_key_exists("implements", $this->params))
			if(negate(empty($this->params["implements"])))
				$implements = sprintf(" implements %s", $this->params["implements"]);

		$extends = null;
		if(array_key_exists("extends", $this->params))
			if(negate(empty($this->params["extends"])))
				$extends = sprintf(" extends %s", $this->params["extends"]);

		$methods = [];
		$tpl_method = $this->tpl_method;
		if(array_key_exists("methods", $this->params))
			if(negate(empty($this->params["methods"])))
				$methods = arr($this->params["methods"])->map(function($k,$v)use($tpl_method){
			
					$block=null;
					if(array_key_exists("block", $v))
						if(negate(empty($v["block"])))
							$block = (string)new DocBlocker(arr($v["block"])->map(function($k,$v){
								if(is_string($v))
									return sprintf("@%s(%s)", $k, $v);

								if(is_array($v))
									return sprintf("@%s(%s)", $k, implode(",",$v));
							})->join("\n"));

					return str(template($tpl_method, [

						"name"=>$v["name"],
						"params"=>$v["params"]??"",
						"body"=>$v["body"]??"\t//",
						"docblock"=>$block??""

					]))->pad()->block()->left();
				})->yield();

		if(negate(empty($methods)))
			$this->methods = array_merge($this->methods, $methods);

		if(negate(empty($this->methods)))
			$body = implode("\n\n", $this->methods);

		return template($this->tpl_class, [

			"ns"=>$this->params["ns"],
			"class_name"=>$this->params["class_name"],
			"implements"=>$implements??"",
			"extends"=>$extends??"",
			"body"=>$body??"\t//"
		]);
	}
}