<?php

namespace Strukt\Generator\Compiler;

use Strukt\Contract\AnnotationInterface;
use Strukt\Event\Event;

class Configuration{

	private $excluded_method_paramtypes;
	private $builders;

	public function __construct(){

		$this->excluded_method_paramtypes = [];
		$this->builders = array(

			"method" => new \ArrayIterator(array(null)),
			"property" => new \ArrayIterator(array(null))
		);
	}

	public function addAnnotationBuilder(string $type, \Closure $builder){

		$this->builders[$type]->append(new Event($builder));
	}

	public function hasAnnotationBuilder(string $type){

		return $this->builders[$type]->count() > 0;
	}

	public function nextAnnotationBuilder(string $type){

		$this->builders[$type]->next();

		return $this->builders[$type]->valid();
	}

	public function resetAnnotationBuilder(string $type){

		if($this->hasAnnotationBuilder($type))
			$this->builders[$type]->rewind();
	}

	public function getCurrentAnnotationBuilder(string $type):Event{

		return $this->builders[$type]->current();
	}

	public function setExcludedMethodParamTypes(array $paramtypes){

		return $this->excluded_method_paramtypes = $paramtypes;
	}

	public function getExcludedMethodParamTypes(){

		return $this->excluded_method_paramtypes;
	}
}