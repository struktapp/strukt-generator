<?php

namespace Strukt\Annotation\Parser;

/**
 * Basic Annotation Parser class.
 *
 * @author Moderator <pitsolu@gmail.com>
 */
class Basic{

	/**
	* Nested list of annotation attributes
	*
	* @var array $annotations
	*/
	private $annotations = null;

	/**
	* Instance of reflector
	*
	* @var \ReflectionClass $reflector
	*/
	private $ref = null;

	/**
	* Constructor
	*
	* @param \ReflectionClass $reflector
	*
	* @throws \Exception
	*/
	public function __construct(\ReflectionClass $ref){

		$this->ref = $ref;

		$this->getClassAnnotations();
		$this->getPropertyAnnotations();
		$this->getMethodAnnotations();
	}

	/**
	* get all annotations
	*
	* @return array
	*/
	public function getAnnotations(){

		$this->annotations["class_name"] = $this->ref->getName();

		return $this->annotations;
	}

	/**
	* get annotations from class DocBlock
	*
	* @return void
	*/
	private function getClassAnnotations(){

		$docBlock = $this->ref->getDocComment();

		if(!empty($docBlock))
			$this->annotations["class"] = $this->resolveAnnotations($docBlock);		
	}

	/**
	* get annotations from variable DocBlocks
	*
	* @return void
	*/
	private function getPropertyAnnotations(){

		foreach($this->ref->getProperties() as $refProp){

			$propName = $refProp->getName();
			$docBlock = $refProp->getDocComment();

			if(!empty($docBlock))
				$this->annotations["properties"][$propName] = $this->resolveAnnotations($docBlock);
		}
	}

	/**
	* get annotations from method DocBlocks
	*
	* @return void
	*/
	private function getMethodAnnotations(){

		foreach($this->ref->getMethods() as $refMeth){

			$methName = $refMeth->getName();
			$docBlock = $refMeth->getDocComment();

			if(!empty($docBlock))
				$this->annotations["methods"][$methName] = $this->resolveAnnotations($docBlock);
		}
	}

	/**
	* Break up and clean up DocBlock from comment tokens
	*
	* @param string $docBlock
	*
	* @return array
	*/
	private function sanitizeDocBlock($docBlock){

		$doc = str_replace(array("/**","*/","*"), "", $docBlock);

		$rawAnnotations = array_map(function($val){

			return trim(preg_replace("/^@/", "", trim($val)));

		}, explode("\n", trim($doc)));

		return $rawAnnotations;
	}

	/**
	* Analyze and extract annotations from DocBlock
	*
	* @param string $docBlock
	*
	* @return array
	*/
	private function resolveAnnotations($docBlock){

		$rawAnnotations = $this->sanitizeDocBlock($docBlock);

		$annotations = null;
		foreach($rawAnnotations as $rawAnnotation){

			$annotation = [];
			preg_match("/\w+(?=\((.*)\))/", trim($rawAnnotation), $matches); 

			$annotation["name"] = current($matches);
			if(empty($annotation["name"]))
				continue;

			$annotation["item"] = next($matches);

			if(preg_match("/,/", $annotation["item"]))
				$annotation["items"] = array_map("trim", explode(",", $annotation["item"]));

			if(preg_match("|=|", $annotation["item"])){

				$items = $annotation["items"];
				if(empty($annotation["items"]))
					$items = array($annotation["item"]);

				unset($annotation["items"]);
				foreach($items as $item){

					$items = explode("=", $item);
					$annotation["items"][trim(current($items))] = trim(next($items));
				}
			}

			if(!empty($annotation["items"]))
				unset($annotation["item"]);

			$key = $annotation["name"];
			$whichIKey = "item";
			if(array_key_exists("items", $annotation))
				$whichIKey = "items";

			if(array_key_exists($key, $annotations)){

				$nItems[] = $annotation[$whichIKey];
				if(is_array($annotation[$whichIKey]))
					$nItems = $annotation[$whichIKey];

				$nkey = "items";
				if(array_key_exists("item", $annotations[$key]))
					$nkey = "item";

				$mItems = $annotations[$key][$nkey];

				$annotation = array(

					"name"=>$key,
					"items"=>array_merge($mItems, $nItems)
				);
			}

			$annotations[$key] = $annotation;
		}

		return $annotations;
	}
}