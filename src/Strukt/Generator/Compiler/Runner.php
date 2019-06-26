<?php

namespace Strukt\Generator\Compiler;

use Strukt\Generator\DocBlocker;
use Strukt\Generator\Parser;
use Strukt\Generator\ClassBuilder;

/**
* Compiler Runner Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Runner{

	/**
	* Token Parser
	*
	* @var Strukt\Generator\Parser $parser
	*/
	private $parser = null;

	/**
	* Compiler configuration
	*
	* @var array $config
	*/
	private $config = null;

	/**
	* Constructor
	*
	* @param Strukt\Generator\Parser $parser
	* @param array $config
	*/
	public function __construct(Parser $parser, Configuration $config = null){

		if(is_null($config))
			$config = new Configuration();

		$this->config = $config;
		$this->parser = $parser;
	}

	/**
	* Build annotations tokens method or properties
	*
	* @param array $tokens
	*
	* @return \Strukt\Generator\DocBlocker
	*/
	private function getAnnotations(string $type, Array $tokens, $space = "\n"){

		if($this->config->hasAnnotationBuilder($type)){

			$this->config->resetAnnotationBuilder($type);

			while($this->config->nextAnnotationBuilder($type)){

				$builder = $this->config->getCurrentAnnotationBuilder($type)
											->apply($tokens)
											->exec();

				$annotations[] = DocBlocker::deBlock((string) $builder);			
			}

			if(!empty($annotations))
				return new DocBlocker(implode($space, $annotations));
		}

		return null;
	}

	/**
	* Compile action
	*
	* @return Strukt\Generator\ClassBuilder
	*/
	public function compile(){

		$this->data = $this->parser->run();

		$descr = null;

		if(in_array("descr", array_keys($this->data["class"]))){
			
			if(!empty($this->data["class"]["descr"])){

				$rawDescr = implode("\n", array_map(function($val){

					return trim($val);

				}, explode("\n", $this->data["class"]["descr"])));

				$descr = new DocBlocker($rawDescr);
			}
		}

		$builder = new ClassBuilder($this->data["class"], $descr);

		if(!empty($this->data["params"])){

			foreach($this->data["params"] as $param){

				$propertyAnnotations = null;

				if($this->config->hasAnnotationBuilder("property")){

					$propertyAnnotations = $this->getAnnotations("property", $param);
				}

				$builder->addProperty($param, $propertyAnnotations);
			}
		}

		if(!empty($this->data["methods"])){

			foreach($this->data["methods"] as $method){

				$methodAnnotations = null;

				if($this->config->hasAnnotationBuilder("method")){

					$methodAnnotations = $this->getAnnotations("method", $method, "\n\n");
				}

				$excludeMethodParamTypes = $this->config->getExcludedMethodParamTypes();

				if(!empty($excludeMethodParamTypes) && !empty($method["params"])){

					foreach($method["params"] as $name=>$type){

						if(is_array($type)){

							$paramType = $method["params"][$name]["type"];

							if(in_array($paramType, $excludeMethodParamTypes)){

								$method["params"][$name]["type"] = "";
							}
						}

						if(is_string($type) && in_array($type, $excludeMethodParamTypes)){

							$method["params"][$name] = "";
						}
					}
				}

				$builder->addMethod($method, $methodAnnotations);
			}
		}
			
		
		return $builder;
	}
}