<?php

namespace Strukt\Generator;

use Strukt\Util\Str;

/**
* Parser
*
* @author Moderator <pitsolu@gmail.com>
*/
class Parser{

	/**
	* Token lines
	* 
	* @var array $lines
	*/
	private $lines;

	/**
	* Constructor
	*
	* @var string $tokens
	*/
	public function __construct($tokens){

		if(!is_string($tokens))
			throw new \Exception("Parser constructor requires string as 1st argument!");

		$tokens = sprintf("%s\n", trim($tokens));

		new Validator($tokens);

		$this->lines = explode("\n", $tokens);
	}

	/**
	* Build param
	*
	* @param string $line
	*
	* @return array
	*/
	private function getParam($line){

		list($paramAccess, $paramScope, $paramName, $paramVal) = array(null, null, null, null);

		$paramDef = (new Str($line))->replace("@param:", ""); 

		if((new Str($paramDef))->contains(">")){

			$paramElms = explode(">", $paramDef);

			if(!empty($paramElms)){

				foreach($paramElms as $seqKey=>$paramEl){

					if(in_array($paramEl, array("private","public", "protected"))){

						$paramAccess = $paramEl;

						unset($paramElms[$seqKey]);
					}
					
					if($paramEl == "static"){

						$paramScope = $paramEl;

						unset($paramElms[$seqKey]);
					}
				}
			}

			$paramName = reset($paramElms);
		}

		if(empty($paramName))
			$paramName = $paramDef;

		if((new Str($paramName))->contains("="))
			list($paramName, $paramVal) = explode("=", $paramName);

		$paramType="";
		if((new Str($paramName))->contains("#"))
			list($paramName, $paramType) = explode("#", $paramName);

		return array(

			"access"=>$paramAccess, 
			"scope"=>$paramScope, 
			"name"=>$paramName,
			"value"=>$paramVal,
			"type"=>$paramType
		);
	}

	/**
	* Build method
	*
	* @param string $line
	*
	* @return array
	*/
	private function getMethod($line){

		$methType = "";
		$methParams = "";
		$methAccess="public";
		$methName = (new Str($line))->replace("@method:","");

		if((new Str($methName))->contains(">"))
			list($methAccess, $methName) = explode(">", $methName);

		if((new Str($line))->contains("@param")){

			list($methName, $methParams) = explode("@param:", $methName);

			if((new Str($methParams))->contains("|"))
				$methParams = explode("|", $methParams);

			if(is_string($methParams))
				$methParams = array($methParams);

			foreach($methParams as $seqKey=>$methParam){

				$isMethodTyped = (new Str($methParam))->contains("#");
				if(!$isMethodTyped)
					$methParams[$methParam] = "";

				if($isMethodTyped){

					list($methParam, $methParamType) = explode("#", $methParam);

					$methParamVal=null;

					if((new Str($methParamType))->contains("=")){

						list($methParamType, $methParamVal) = explode("=", $methParamType);

						$methParams[$methParam] = array(

							"type"=>$methParamType,
							"value"=>$methParamVal
						);
					}
					
					if(is_null($methParamVal))
						$methParams[$methParam] = $methParamType;
				}

				unset($methParams[$seqKey]);
			}
		}

		if((new Str($methName))->contains("#"))
			list($methName, $methType) = explode("#", $methName);

		return array($methAccess, $methType, $methName, $methParams);
	}

	/**
	* Run parser
	*
	* @return array
	*/
	public function run(){

		$isBuffer = false;

		$classNames = array(

			"ns"=>"namespace", 
			"class"=>"name", 
			"inherit"=>"extends",
			"interface"=>"implement"
		);

		$classMetadata = array();

		foreach($this->lines as $line){

			//class
			if(preg_match("/^@(ns|import|class|inherit|interface):(.*)+/", $line)){

				list($alias, $meta) = explode(":", trim($line, "@"));

				if($alias == "import")
					$classMetadata["class"]["use"][] = $meta;

				if(in_array($alias, array_keys($classNames)))
					$classMetadata["class"][$classNames[$alias]] = $meta;
			}

			//param
			if((new Str($line))->startsWith("@param")){	

				//must preset
				if(!in_array("params", array_keys($classMetadata)))
					$classMetadata["params"] = null;

				$classMetadata["params"][] = $this->getParam($line);
			}

			//method
			if((new Str($line))->startsWith("@method")){

				//must preset
				if(!in_array("methods", array_keys($classMetadata)))
					$classMetadata["methods"] = null;

				list($methAccess, $methType, $methName, $methParams) = $this->getMethod($line);
			}

			//body
			if((new Str($line))->startsWith("@body:"))
				$methBody = (new Str($line))->replace("@body:", "");

			//body buffer
			if(in_array(trim($line), array("@body","@descr"))){

				$bufferType = trim($line);

				$isBuffer = !$isBuffer;

				if(!$isBuffer){

					$bufferOutput = implode("\n", $buffer);
					
					$classKeys = array_keys($classMetadata);
					if(!in_array("params", $classKeys) && 
						!in_array("methods", $classKeys))
							$classMetadata["class"]["descr"] = $bufferOutput;

					if($bufferType == "@body")
						$methBody = $bufferOutput;

					if($bufferType == "@descr")
						$methDescr = $bufferOutput;

					unset($buffer);
					unset($bufferOutput);
				}
			}

			if(!(new Str($line))->startsWith("@")
				&& !in_array(trim($line), array(

				"@ns",
				"@class",
				"@inherit",
				"@interface",
				"@method",
				"@descr",
				"@body",
				"@param"
			)))
				if($isBuffer)
				 	$buffer[] = $line; 

			//annotations
			if((new Str($line))->startsWith("@descr:@"))
				$methAnnots[] = (new Str($line))->replace("@descr:", "");

			//descr
			if(preg_match("/^@descr:[\w ]+$/", trim($line)))
				$methDescr[] = (new Str($line))->replace("@descr:", "");

			if(empty($line) && !$isBuffer){

				if(!empty($methName)){

					if(empty($methParams))
						$methParams = null;

					if(empty($methAnnots))
						$methAnnots = null;

					if(empty($methAnnots))
						$methAnnots = null;

					if(empty($methBody))
						$methBody = null;

					if(empty($methDescr))
						$methDescr = null;

					$classMetadata["methods"][] = array(

						"access"=>$methAccess,
						"name"=>$methName,
						"params"=>$methParams,
						"body"=>$methBody,
						"annotations"=>$methAnnots,
						"type"=>$methType,
						"descr"=>$methDescr
					);

					unset($methName);
					unset($methParams);
					unset($methBody);
					unset($methAnnots);
					unset($methDescr);
				}
			}
		}

		return $classMetadata;
	}
}