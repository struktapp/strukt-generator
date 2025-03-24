<?php

namespace Strukt\Generator\Annotation;

use Strukt\Contract\AnnotationInterface;

/**
* Basic Annotation Generator Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Basic implements AnnotationInterface{

	/**
	* List of annotation key value pairs
	*
	* @var array
	*/
	private $notes;

	/**
	* Store annotations in block
	*
	* @var array
	*/
	private $block;

	/**
     * Constructor
     *
     * @param array $annotation
     */
	public function __construct(array $notes){

		$this->notes = $notes;
	}

	/**
     * Build DocBlock
     * 
     * @return void
     */
	protected function build():void{

		foreach($this->notes as $name=>$item){

			if(is_array($item)){
				
				if(!empty(array_filter(array_keys($item), "is_string")))
					$item = array_map(function($key, $val){

						return sprintf("%s=%s", $key, $val);

					}, array_keys($item), $item);

				$item = implode(", ", $item);
			}

			$this->block[] = sprintf("* @%s(%s)", $name, $item);
		}
	}

	/**
     * Render DocBlock
     *
     * @return string
     */
	public function __toString(){

		$this->build();

		return sprintf("/**\n%s\n*/", implode("\n", $this->block));
	}
}
