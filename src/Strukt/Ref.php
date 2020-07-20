<?php

namespace Strukt;

class Ref{

	private $class;
	private $instance;

	public function __construct(string $classname){

		$this->class = new \ReflectionClass($classname);
	}

	public static function create(string $classname){

		return new self($classname);
	}

	public function method(string $name){

		$rMethod = $this->class->getMethod($name);

		return new class($rMethod, $this->instance){

			private $oMethod;
			private $oInstance;

			public function __construct($method, $instance){

				$this->oMethod = $method;
				$this->oInstance = $instance;
			}

			public function getRef(){

				return $this->oMethod;
			}

			public function invoke(...$args){

				if(is_null($args))
					$result = $this->oMethod->invoke($this->oInstance);
				else
					$result = $this->oMethod->invokeArgs($this->oInstance, $args);

				return $result;
			}
		};
	}

	/**
	* \ReflectionClass
	*/
	public function getRef(){

		return $this->class;
	}

	public function getInstance(){

		return $this->instance;
	}

	/**
	* newInstanceArgs
	*/
	public function make(...$args){

		$this->instance = $this->class->newInstanceArgs($args);

		return $this;
	}

	/**
	* newInstanceWithoutConstructor
	*/
	public function noMake(){

		$this->instance = $this->class->newInstanceWithoutConstructor();

		return $this;
	}
}