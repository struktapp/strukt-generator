<?php

namespace Strukt\Generator;

use Strukt\Helper\Str;

/**
* DocBlocker Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class DocBlocker implements \Strukt\Generator\IAnnotation{

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
	public function __construct($block){

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
	public static function deBlock($block){

		if(!is_string($block))
			throw new \Exception("Blocker constructor takes a string!");

		$block = trim(str_replace(array("/**","*/"), "", $block));

		$blockParts = explode("\n", $block);

		foreach($blockParts as $seqKey=>$part)
			if(Str::startsWith($part, "*"))
				$blockParts[$seqKey] = trim(ltrim($part, "*"));

		return implode("\n", $blockParts);
	}

	/**
	* Rebuild DocBlock
	*/
	protected function build(){

		foreach($this->block as $seqKey=>$part)
			$this->block[$seqKey] = sprintf("* %s", $part);
	}

	/**
     * Render DocBlock
     *
     * @return string
     */
	public function __toString(){

		$this->build();

		$block = sprintf("/**\n%s\n*/", implode("\n", $this->block));
		if(empty(trim(str_replace(array("/**","*/","*"), "", $block))))
			$block="";

		return $block;
	}
}