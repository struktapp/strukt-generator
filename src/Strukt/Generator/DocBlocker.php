<?php

namespace Strukt\Generator;

use Strukt\Type\Str;
use Strukt\Contract\AnnotationInterface;

/**
* DocBlocker Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class DocBlocker implements AnnotationInterface{

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

		if(!is_string($block))
			throw new \Exception("Blocker constructor takes a string!");

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

		if(!is_string($block))
			throw new \Exception("Blocker constructor takes a string!");

		$block = trim((new Str($block))->replace(array("/**","*/"), ""));

		$blockParts = explode("\n", $block);

		foreach($blockParts as $seqKey=>$part)
			if((new Str($part))->startsWith("*"))
				$blockParts[$seqKey] = trim(ltrim($part, "*"));

		return implode("\n", $blockParts);
	}

	/**
	* Rebuild DocBlock
	* 
	* @return void
	*/
	protected function build():void{

		foreach($this->block as $seqKey=>$part)
			$this->block[$seqKey] = sprintf("* %s", $part);
	}

	/**
     * Render DocBlock
     */
	public function __toString(){

		$this->build();

		$block = sprintf("/**\n%s\n*/", implode("\n", $this->block));
		if(empty(trim((new Str($block))->replace(array("/**","*/","*"), ""))))
			$block="";

		return $block;
	}
}