<?php

namespace Strukt\Generator;

use Strukt\Contract\AnnotationInterface;

/**
* ClassBuilder Class
*
* @author Moderator <pitsolu@gmail.com>
*/
class ClassBuilder{

	/**
	* Hold class data 
	*
	* @var Array
	*/
	private $data;

	/**
     * Constructor
     *
     * @param array $decl class declaration array expects (namespace, extends, name) keys
     * @param \Strukt\Contract\AnnotationInterface $adapter
     */
	public function __construct(array $decl, ?AnnotationInterface $adapter = null){

		$decl = array_merge(array(

			"namespace"=>"",
			"extends"=>"",
			"name"=>"",
			"implement"=>""
		), 
		$decl);

		// print_r($decl);

		$decl["name"] = trim($decl["name"]);
		if(empty($decl["name"]))
			throw new \Exception("Class name cannot be empty!");

		if(!preg_match("/^\w+$/", $decl["name"]))
			throw new \Exception(sprintf("Invalid class name [%s]!", $decl["name"]));

		$decl["descr"] = "";
		if(!is_null($adapter))
			$decl["descr"] = $adapter;
		
		$this->data["class"] = $decl;
	}

	/**
	* Setter for class properties
	*
	* @param array $properties accepts arrays with keys ([scope], [access], name, [value])
	* @param \Strukt\Contract\AnnotationInterface $adapter
	* 
	* @return static
	*/
	public function addProperty(array $property, ?AnnotationInterface $adapter = null):static{

		$property = array_merge(array(

			"access"=>"public",
			"value"=>"",
			"scope"=>"",
			"name"=>"",
			"descr"=>"",
			"type"=>""
		), 
		$property);

		$name = trim($property["name"]);
		if(empty($name))
			throw new \Exception("Property name cannot be empty!");

		if(!preg_match("/^\w+$/", $name))
			throw new \Exception(sprintf("Invalid property name [%s]!", $name));

		$accessor = trim($property["access"]);
		if(empty($accessor))
			$accessor = "public";

		if(!in_array($accessor, array("private","public", "protected")))
			throw new \Exception(sprintf("Invalid accessor [%s]!", $accessor));

		$name = sprintf("\$%s", $name);

		$value = trim($property["value"]);
		if(!empty($value))
			$name = sprintf("%s = %s", $name, $value);

		$scope = trim($property["scope"]);

		if(is_null($adapter))
			$annots="\n";

		if(!is_null($adapter)){

			foreach(explode("\n", $adapter) as $annot)
				$annotList[] = sprintf("\t%s", $annot);

			$annots = sprintf("\n%s\n", implode("\n", $annotList));
		}

		$this->data["properties"][] = sprintf("%s\t%s;\n", $annots, trim(implode(" ", array(

			$accessor,
			$scope,
			$name

		))));

		return $this;
	}

	/**
     * Build annotation DocBlock
     *
     * @param string $method method items i.e params, name
     * @param Strukt\Generator\IAnnotation $adapter
     *
     * @return static
     */
	public function addMethod(Array $method, ?AnnotationInterface $adapter = null):static{


		$method = array_merge(array(

			"access"=>"public",
			"name"=>"",
			"params"=>"",
			"body"=>"//"
		), 
		$method);

		$annots = "";
		$params = "()";

		$body = trim($method["body"]);

		$name = trim($method["name"]);
		if(empty($name))
			throw new \Exception("Method name cannot be empty!");

		if(!preg_match("/^\w+$/", $name))
			throw new \Exception(sprintf("Invalid method name [%s]!", $name));

		$accessor = trim($method["access"]);
		if(!in_array($accessor, array("private","public", "protected")))
			throw new \Exception(sprintf("Invalid accessor [%s]!", $accessor));

		if(!empty($method["params"])){

			if(is_string($method["params"])){

				$param = trim($method["params"]);
				if(!preg_match("/^\w+$/", $param))
					throw new \Exception(sprintf("Invalid method param [%s]!", $param));

				$params = sprintf("($%s)", $param);
			}

			if(is_array($method["params"])){

				unset($params);

				foreach($method["params"] as $key=>$val){

					if(!is_numeric($key)){

						if(is_array($val)){
							
							$key=sprintf("%s=%s", $key, $val["value"]);
							$val=$val["type"];
						}

						$params[] = trim(sprintf("%s $%s", $val, trim(trim($key), "$")));
					}
					else
						$params[] = sprintf("$%s", trim(trim($val), "$"));
				}

				$params = sprintf("(%s)", implode(", ", $params));
			}
		}

		if(!is_null($adapter)){

			foreach(explode("\n", $adapter) as $annot)
				$annotList[] = sprintf("\t%s", $annot);

			$annots = sprintf("%s\n", implode("\n", $annotList));
		}

		$this->data["methods"][] = sprintf("%s\t%s function %s%s{\n\n\t\t%s\n\t}\n",
											$annots, 
											$accessor,
											$name,
											$params,
											$body);
		return $this;
	}

	/**
     * Render generated class
     *
     * @return string
     */
	public function __toString(){

		$properties = "";
		$methods = "";
		
		if(in_array("properties", array_keys($this->data)))
			$properties = implode("", $this->data["properties"]);	
		
		if(in_array("methods", array_keys($this->data)))
			$methods = implode("\n", $this->data["methods"]);

		$extend = trim($this->data["class"]["extends"]);
		if(!empty($extend))
			$extend = sprintf(" extends %s", $extend);

		$implement = trim($this->data["class"]["implement"]);
		if(!empty($implement))
			$implement = sprintf(" implements %s", $implement);

		$namespace = sprintf("\nnamespace %s;\n\n", trim($this->data["class"]["namespace"]));

		$imports = "";
		if(in_array("use", array_keys($this->data["class"])))
			$imports = sprintf("%s\n\n", implode("\n", array_map(function($use){

				return sprintf("use %s;", $use);

			}, $this->data["class"]["use"])));

		$descr = $this->data["class"]["descr"];
		if(!empty($descr))
			$descr = sprintf("%s\n", $descr);

		$name = trim($this->data["class"]["name"]);

		$class = sprintf("class %s%s%s{\n%s\n%s}", $name,  $extend, $implement, $properties, $methods);

		return implode("", array($namespace, $imports, $descr, $class));
	}
}