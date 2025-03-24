<?php

use Strukt\Templator;
use Strukt\Generator\ClassBuilder;
use Strukt\Generator\Annotation\Basic as BasicNotes;

helper("generator");

if(helper_add("template")){

	/**
	 * @param string $tpl
	 * @param array $data
	 * 
	 * @return string
	 */
	function template(string $tpl, array $data):string{

		return Templator::create($tpl, $data);
	}
}

if(helper_add("generator")){

	/**
	 * @param array $class
	 * @param array $options
	 * 
	 * @return object
	 */
	function generator(array $class, array $options = []):object{

		return new class($class, $options){

			private $options;
			private $builder;
			private $class;

			/**
			 * @param array $class
	 		 * @param array $options
	 		 */
			public function __construct(array $class, array $options = []){

				$this->options = $options;

				$this->builder = new ClassBuilder($class["declaration"]);
				$this->class = $class;
				$this->properties();
				$this->methods();
			}

			/**
			 * @return void
			 */
			private function properties():void{

				foreach($this->class["properties"] as $property)
					$this->property($property);
			}

			/**
			 * @param array $property
			 * 
			 * @return static
			 */
			public function property(array $property):static{

				$use_notes = fn($property)=>null;
				if(arr(array_keys($this->options))->has("property_notes"))
					if($this->options["property_notes"])
						$use_notes = fn($property)=>new BasicNotes($property["annotations"]);

				$this->builder->addProperty($property, $use_notes($property));

				return $this;
			}

			/**
			 * @return void
			 */
			private function methods():void{

				foreach($this->class["methods"] as $method)
					$this->method($method);
			}

			/**
			 * @param array $method
			 * 
			 * @return void
			 */
			public function method(array $method):static{

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


if(helper_add("notes")){

	/**
	 * @param object|string $class_name
	 * 
	 * @return array
	 */
	function notes(object|string $class_name):array{

		$ref = new \ReflectionClass($class_name);
		$parser = new \Strukt\Annotation\Parser\Basic($ref);
		return $parser->getNotes();
	}
}