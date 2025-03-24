<?php

namespace Strukt\Generator\Annotation;

use Strukt\Type\Str;
use Strukt\Contract\AnnotationInterface;

/**
* Standard Annotation Generator Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Standard implements AnnotationInterface{

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
     * @param array $notes
     */
	public function __construct(array $notes){

		$keySet = array(

			array(

				"descr",
				"params",
				"returnType"
			),
			array(

				"descr",
				"param",
				"type"
			)
		);

		$keys = array_keys($notes);
		if(!empty(array_diff(reset($keySet), $keys)))
			if(!empty(array_diff(next($keySet), $keys)))
				throw new \Exception(sprintf("Invalid keys set[%s]!", implode(",", $keys)));

		$this->notes = $notes;
		$this->block[] = "/**";		
	}

	/**
     * Build DocBlock
     * 
     * @return void
     */
	protected function build():void{

		$keys = array_keys($this->notes);

		if(in_array("descr", $keys)){			

			if(!empty($this->notes["descr"])){

				if(is_string($this->notes["descr"])){

					$rawDescr = trim($this->notes["descr"]);

					if(Str::create($rawDescr)->contains("\n"))
						$rawDescr = explode("\n", $rawDescr);

					if(!is_array($rawDescr))
						$descr = sprintf("* %s", $rawDescr);
				}

				if(is_array($this->notes["descr"]) || is_array($rawDescr)){

					if(is_array($this->notes["descr"]))
						$rawDescr = $this->notes["descr"];

					$descr = implode("\n", array_map(function($val){

						return sprintf("* %s", trim($val));

					}, $rawDescr));
				}

				if(!empty($descr)){

					$this->block[] = $descr;
					$this->block[] = "*";
				}
			}
		}

		if(in_array("params", $keys)){

			if(!empty($this->notes["params"])){
				
				foreach($this->notes["params"] as $key=>$val){

					if(is_array($val)){

						if(in_array("descr", array_keys($val)))
							$key = sprintf("%s %s", $key, $val["descr"]);
						
						$val = $val["type"];
					}

					$this->block[] = sprintf("* @param %s", trim(sprintf("%s \$%s", $val, $key)));
				}
			}
		}

		if(in_array("param", $keys)){

			$val = sprintf("\$%s", $this->notes["param"]);
			$this->block[] = sprintf("* @var %s %s", $this->notes["type"], $val);
		}

		if(in_array("returnType", $keys)){

			$returnType = trim($this->notes["returnType"]);

			if(!empty($returnType)){
				
				if(!empty($this->notes["params"]))
					$this->block[] = "*";

				$this->block[] = sprintf("* @return %s", $returnType);
			}
		}
	}

	/**
     * Render DocBlock
     *
     * @return string
     */
	public function __toString(){

		$this->build();
		$this->block[] = "*/";

		return implode("\n", $this->block);
	}
}
