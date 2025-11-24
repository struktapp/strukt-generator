<?php

namespace Strukt;

/**
* DocBlocker Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class DocBlocker{

	/**
	* DocBlock
	*
	* @var string $block
	*/
	private $block = null;

	/**
	* Constructor
	*
	* @param string $block
	*/
	public function __construct(string $block){

		$this->block = explode("\n", trim($block));
	}

	/**
	* Remove encapsulating comment tokens
	*
	* @param string $block
	*
	* @return string
	*/
	public static function deBlock(string $block):string{

		$block = str($block)->replace(array("/**","*/"), "");

		$parts = explode("\n", $block);
		foreach($parts as $idx=>$part){

			$str = str($part);
			if($str->startsWith("*"))
				$parts[$idx] = trim(ltrim($part, "*"));

			if($str->startsWith("\t*"))
				$parts[$idx] = trim(ltrim($part, "\t*"));
		}

		return implode("\n", $parts);
	}

	/**
     * Render DocBlock
     */
	public function __toString(){

		foreach($this->block as $idx=>$part)
			$this->block[$idx] = sprintf("* %s", $part);

		$block = sprintf("/**\n%s\n*/", implode("\n", $this->block));
		if(empty(str($block)->replace(array("/**","*/","*"), "")))
			$block="";

		return $block;
	}
}