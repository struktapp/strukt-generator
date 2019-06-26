<?php

use Strukt\Fs;
use Strukt\Generator\Parser;
use Strukt\Generator\Annotation\Standard as StandardAnnotation;
use Strukt\Generator\Annotation\Basic as BasicAnnotation;
use Strukt\Generator\Compiler\Configuration;
use Strukt\Generator\Compiler\Runner as Compiler;

class CompilerTest extends PHPUnit\Framework\TestCase{

	public function testSimple(){

		$sgfRoleController = Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Tests/RoleTest.sgf");

		$parser = new Parser($sgfRoleController);
		
		$compiler = new Compiler($parser);

		// exit($compiler->compile());

		$fixture = Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Tests/RoleTest.php"));

		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}

	public function testIntermediate(){

		$sgfRoleController = Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Controller/Role.sgf");

		$parser = new Parser($sgfRoleController);

		$config = new Configuration();
		$config->setExcludedMethodParamTypes(array(

			"string",
			"integer",
			"double",
			"float"
		));

		$config->addAnnotationBuilder("method", function(array $method){

			return new StandardAnnotation(array(

				"returnType"=>$method["type"],
				"params"=>$method["params"],
				"descr"=>$method["descr"]
			));
		});
		
		$compiler = new Compiler($parser, $config);

		// exit($compiler->compile());

		$fixture = Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Controller/Role.php"));

		// print_r(array(

		// 	(string)$compiler->compile(),
		// 	$fixture
		// ));exit;
		
		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}

	public function testAdvanced(){

		$sgfRoleRouter = Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Router/Role.sgf");
		
		$parser = new Parser($sgfRoleRouter);
		
		$config = new Configuration();
		$config->setExcludedMethodParamTypes(array(

			"string",
			"integer",
			"double",
			"float"
		));

		$config->addAnnotationBuilder("property", function(array $param){

			return new StandardAnnotation(array(

				"type"=>$param["type"],
				"param"=>$param["name"],
				"descr"=>""
			));
		});

		$config->addAnnotationBuilder("method", function(array $method){

			if(empty($method["annotations"]))
				return null; 
			
			foreach($method["annotations"] as $annotation){

				list($aKey, $aVal) = explode(":", $annotation, 2);

				if(strpos($aVal, "|") !== false)
					$aVal = explode("|", $aVal);

				$methAnnots[trim($aKey, "@")] = $aVal;
			}

			return new BasicAnnotation($methAnnots);
		});

		$config->addAnnotationBuilder("method", function(array $method){

			return new StandardAnnotation(array(

				"returnType"=>$method["type"],
				"params"=>$method["params"],
				"descr"=>$method["descr"]
			));
		});

		$compiler = new Compiler($parser, $config);

		// exit($compiler->compile());

		$fixture = Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Router/Role.php"));

		// print_r(array(

		// 	(string)$compiler->compile(),
		// 	$fixture
		// ));exit;
		
		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}
}