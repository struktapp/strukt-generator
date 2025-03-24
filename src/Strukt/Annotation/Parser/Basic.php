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
	* @var array $notes
	*/
	private $notes = null;

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

		$this->getClassNotes();
		$this->getPropertyNotes();
		$this->getMethodNotes();
	}

	/**
	* get all annotations
	*
	* @return array
	*/
	public function getNotes():array{

		$this->notes["class_name"] = $this->ref->getName();

		return $this->notes;
	}

	/**
	* get annotations from class DocBlock
	*
	* @return void
	*/
	private function getClassNotes():void{

		$docBlock = $this->ref->getDocComment();
		if(!empty($docBlock))
			$this->notes["class"] = $this->resolveNotes($docBlock);		
	}

	/**
	* get annotations from variable DocBlocks
	*
	* @return void
	*/
	private function getPropertyNotes():void{

		foreach($this->ref->getProperties() as $refProp){

			$propName = $refProp->getName();
			$docBlock = $refProp->getDocComment();

			if(!empty($docBlock))
				$this->notes["properties"][$propName] = $this->resolveNotes($docBlock);
		}
	}

	/**
	* get annotations from method DocBlocks
	*
	* @return void
	*/
	private function getMethodNotes():void{

		foreach($this->ref->getMethods() as $refMeth){

			$methName = $refMeth->getName();
			$docBlock = $refMeth->getDocComment();

			if(!empty($docBlock))
				$this->notes["methods"][$methName] = $this->resolveNotes($docBlock);
		}
	}

	/**
	* Break up and clean up DocBlock from comment tokens
	*
	* @param string $docBlock
	*
	* @return array
	*/
	private function sanitizeDocBlock(string $docBlock):array{

		$doc = str_replace(array("/**","*/","*"), "", $docBlock);

		$rawNotes = array_map(function($val){

			return trim(preg_replace("/^@/", "", trim($val)));
			
		}, explode("\n", trim($doc)));

		return $rawNotes;
	}

	/**
	* Analyze and extract annotations from DocBlock
	*
	* @param string $docBlock
	*
	* @return array|null
	*/
	private function resolveNotes(string $docBlock):array|null{

		$rawNotes = $this->sanitizeDocBlock($docBlock);

		$notes = null;
		foreach($rawNotes as $rawNote){

			$note = [];
			preg_match("/\w+(?=\((.*)\))/", trim($rawNote), $matches); 

			$note["name"] = current($matches);
			if(empty($note["name"]))
				continue;

			$note["item"] = next($matches);
			if(preg_match("/,/", $note["item"]))
				$note["items"] = array_map("trim", explode(",", $note["item"]));

			if(preg_match("|=|", $note["item"])){

				$items = $note["items"];
				if(empty($note["items"]))
					$items = array($note["item"]);

				unset($note["items"]);
				foreach($items as $item){

					$items = explode("=", $item);
					$note["items"][trim(current($items))] = trim(next($items));
				}
			}

			if(!empty($note["items"]))
				unset($note["item"]);

			$key = $note["name"];
			if(!is_null($notes)){

				$whichIKey = "item";
				if(array_key_exists("items", $note))
					$whichIKey = "items";

				if(array_key_exists($key, $notes)){

					$nItems[] = $note[$whichIKey];
					if(is_array($note[$whichIKey]))
						$nItems = $note[$whichIKey];

					$nkey = "items";
					if(array_key_exists("item", $notes[$key]))
						$nkey = "item";

					$mItems[] = $notes[$key][$nkey];
					if(is_array($notes[$key][$nkey]))
						$mItems = $notes[$key][$nkey];

					$note = array(

						"name"=>$key,
						"items"=>array_merge($mItems, $nItems)
					);
				}
			}

			$notes[$key] = $note;
		}

		return $notes;
	}
}