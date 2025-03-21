<?php

class StandardTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		//
	}

	public function testParam(){

		$s = new Strukt\Generator\Annotation\Standard(array(

			"descr"=>"Flag to check if compiled was executed",
			"param"=>"compiled",
			"type"=>"boolean"
		));

$docblock = "/**
* Flag to check if compiled was executed
*
* @var boolean \$compiled
*/";
		$this->assertEquals((string)$s, str_replace("\r", "", $docblock));	
	}

	public function testMethod(){

		$s = new Strukt\Generator\Annotation\Standard(array(

			"descr"=>"New user",
			"params"=>array(

				"username"=>"string",
				"password"=>"string",
				"role"=>array(

					"descr"=>"role identification",
					"type"=>"Auth\Model\Role"
				)
			),
			"returnType"=>"boolean"
		));

$docblock = "/**
* New user
*
* @param string \$username
* @param string \$password
* @param Auth\Model\Role \$role role identification
*
* @return boolean
*/";

		$this->assertEquals((string)$s, str_replace("\r", "", $docblock));	
	}
}