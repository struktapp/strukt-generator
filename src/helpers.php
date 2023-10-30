<?php

use Strukt\Templator;
use Strukt\Generator\ClassBuilder;
use Strukt\Generator\Annotation\Basic as BasicNotes;

if(!function_exists("template")){

	function template(string $tpl, array $data){

		return Templator::create($tpl, $data);
	}
}

if(!function_exists("generator")){

	function generator(array $class, array $options = []){

		return new class($class, $options){

			private $options;
			private $builder;
			private $class;

			public function __construct(array $class, array $options = []){

				$this->options = $options;

				$this->builder = new ClassBuilder($class["declaration"]);
				$this->class = $class;
				$this->properties();
				$this->methods();
			}

			private function properties(){

				foreach($this->class["properties"] as $property)
					$this->property($property);
			}

			public function property(array $property){

				$use_notes = fn($property)=>null;
				if(arr(array_keys($this->options))->has("property_notes"))
					if($this->options["property_notes"])
						$use_notes = fn($property)=>new BasicNotes($property["annotations"]);

				$this->builder->addProperty($property, $use_notes($property));

				return $this;
			}

			private function methods(){

				foreach($this->class["methods"] as $method)
					$this->method($method);
			}

			public function method(array $method){

				$use_notes = fn($method)=>null;
				if(arr(array_keys($this->options))->has("method_notes"))
					if($this->options["method_notes"])
						$use_notes = fn($method)=>new BasicNotes($method["annotations"]);

				$this->builder->addMethod($method, $use_notes($method));

				return $this;
			}

			public function __toString(){

				return (string)$this->builder;
			}
		};
	}
}


if(!function_exists("notes")){

	function notes($class_name){

		$ref = new \ReflectionClass($class_name);
		$parser = new \Strukt\Annotation\Parser\Basic($ref);
		return $parser->getAnnotations();
	}
}