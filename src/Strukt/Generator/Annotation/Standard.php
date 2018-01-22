<?php

namespace Strukt\Generator\Annotation;

use Strukt\Helper\Str;

/**
* Standard Annotation Generator Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Standard implements \Strukt\Generator\IAnnotation{

	/**
	* List of annotation key value pairs
	*
	* @var array
	*/
	private $annotList;

	/**
	* Store annotations in block
	*
	* @var array
	*/
	private $block;

	/**
     * Constructor
     *
     * @param Array $annotList
     */
	public function __construct(array $annotList){

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

		$keys = array_keys($annotList);
		if(!empty(array_diff(reset($keySet), $keys)))
			if(!empty(array_diff(next($keySet), $keys)))
				throw new \Exception(sprintf("Invalid keys set[%s]!", implode(",", $keys)));

		$this->annotList = $annotList;
		$this->block[] = "/**";		
	}

	/**
     * Build DocBlock
     */
	protected function build(){

		$keys = array_keys($this->annotList);

		if(in_array("descr", $keys)){			

			if(!empty($this->annotList["descr"])){

				if(is_string($this->annotList["descr"])){

					$rawDescr = trim($this->annotList["descr"]);

					if(Str::contains($rawDescr, "\n"))
						$rawDescr = explode("\n", $rawDescr);

					if(!is_array($rawDescr))
						$descr = sprintf("* %s", $rawDescr);
				}

				if(is_array($this->annotList["descr"]) || is_array($rawDescr)){

					if(is_array($this->annotList["descr"]))
						$rawDescr = $this->annotList["descr"];

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

			if(!empty($this->annotList["params"])){
				
				foreach($this->annotList["params"] as $key=>$val){

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

			$val = sprintf("\$%s", $this->annotList["param"]);
			$this->block[] = sprintf("* @var %s %s", $this->annotList["type"], $val);
		}

		if(in_array("returnType", $keys)){

			$returnType = trim($this->annotList["returnType"]);

			if(!empty($returnType)){
				
				if(!empty($this->annotList["params"]))
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
