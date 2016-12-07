<?php

namespace Strukt\Generator;

/**
* Compiler Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Compiler{

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
	public function __construct(Parser $parser, Array $config = null){

		if(is_null($config))
			$config = array();

		$this->config = array_merge(array(

			"excludeStandardAnnotation"=>false,
			"excludeMethodParamTypes"=>null,
			"methodAnnotationBuilder"=>null,
			"propertyAnnotationBuilder"=>null

		),$config);

		$this->parser = $parser;
	}

	/**
	* Build property annotations
	*
	* @param array $param
	*
	* @return \Strukt\Generator\DocBlocker
	*/
	private function getPropertyAnnotations(Array $param){
		
		$builderInstance = null;

		$useExtraBuilder = !is_null($this->config["propertyAnnotationBuilder"]);
		$useStandardBuilder = !$this->config["excludeStandardAnnotation"];
		$useMultipleBuilders =  $useStandardBuilder && $useExtraBuilder;

		if($useExtraBuilder){

			$builderInstance = call_user_func($this->config["propertyAnnotationBuilder"], $param);

			if($useMultipleBuilders)
				$builders[] = $builderInstance;
		}

		if($useStandardBuilder){

			$builderInstance = new \Strukt\Generator\Annotation\Standard(array(

				"type"=>$param["type"],
				"param"=>$param["name"],
				"descr"=>""
			));

			if($useMultipleBuilders)
				$builders[] = $builderInstance;
		}

		if($useMultipleBuilders){

			foreach($builders as $builder)
				$annotations[] = \Strukt\Generator\DocBlocker::deBlock((string) $builder);

			$builderInstance = new \Strukt\Generator\DocBlocker(implode("\n", $annotations));
		}

		return $builderInstance;
	}

	/**
	* Build method annotations
	*
	* @param array $method
	*
	* @return \Strukt\Generator\DocBlocker
	*/
	private function getMethodAnnotations(Array $method){

		$builderInstance = null;

		$useExtraBuilder = !is_null($this->config["methodAnnotationBuilder"]);
		$useStandardBuilder = !$this->config["excludeStandardAnnotation"];
		$useMultipleBuilders = $useStandardBuilder && $useExtraBuilder;

		if($useExtraBuilder){

			$builderInstance = call_user_func($this->config["methodAnnotationBuilder"], $method);

			if($useMultipleBuilders)
				$builders[] = $builderInstance;
		}

		if($useStandardBuilder){

			$builderInstance = new \Strukt\Generator\Annotation\Standard(array(

				"returnType"=>$method["type"],
				"params"=>$method["params"],
				"descr"=>$method["descr"]
			));

			if($useMultipleBuilders)
				$builders[] = $builderInstance;
		}

		if($useMultipleBuilders){

			foreach($builders as $builder)
				$annotations[] = \Strukt\Generator\DocBlocker::deBlock((string) $builder);

			$builderInstance = new \Strukt\Generator\DocBlocker(implode("\n\n", $annotations));
		}

		return $builderInstance;
	}

	/**
	* Compile action
	*
	* @return Strukt\Generator\ClassBuilder
	*/
	public function compile(){

		$this->data = $this->parser->run();

		$descr=null;

		if(in_array("descr", array_keys($this->data["class"]))){
			
			if(!empty($this->data["class"]["descr"])){

				$rawDescr = implode("\n", array_map(function($val){

					return trim($val);

				}, explode("\n", $this->data["class"]["descr"])));

				$descr = new \Strukt\Generator\DocBlocker($rawDescr);
			}
		}

		$builder = new ClassBuilder($this->data["class"], $descr);

		if(!empty($this->data["params"]))
			foreach($this->data["params"] as $param)
				$builder->addProperty($param, $this->getPropertyAnnotations($param));

		if(!empty($this->data["methods"])){

			foreach($this->data["methods"] as $method){

				$methodAnnotations = $this->getMethodAnnotations($method);

				$excludeMethodParamTypes = $this->config["excludeMethodParamTypes"];

				if(!is_null($excludeMethodParamTypes))
					if(!empty($method["params"]))
						foreach($method["params"] as $name=>$type){

							if(is_array($type)){

								$paramType = $method["params"][$name]["type"];
								if(in_array($paramType, $this->config["excludeMethodParamTypes"]))
									$method["params"][$name]["type"] = "";
							}

							if(is_string($type))
								if(in_array($type, $this->config["excludeMethodParamTypes"]))
									$method["params"][$name] = "";
						}

				$builder->addMethod($method, $methodAnnotations);
			}
		}
			
		
		return $builder;
	}
}