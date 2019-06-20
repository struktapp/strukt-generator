<?php

class CompilerTest extends PHPUnit\Framework\TestCase{

	public function testSimple(){

		$sgfRoleController = \Strukt\Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Tests/RoleTest.sgf");

		$parser = new \Strukt\Generator\Parser($sgfRoleController);
		
		$compiler = new \Strukt\Generator\Compiler($parser);

		// exit($compiler->compile());

		$fixture = Strukt\Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Tests/RoleTest.php"));

		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}

	public function testIntermediate(){

		$sgfRoleController = \Strukt\Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Controller/Role.sgf");

		$parser = new \Strukt\Generator\Parser($sgfRoleController);
		
		$compiler = new \Strukt\Generator\Compiler($parser, array(

			"excludeMethodParamTypes"=>array(

				"string",
				"integer",
				"double",
				"float"
			)
		));

		// exit($compiler->compile());

		$fixture = Strukt\Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Controller/Role.php"));
		
		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}

	public function testAdvanced(){

		$sgfRoleRouter = \Strukt\Fs::cat("fixtures/sgf/app/src/Payroll/AuthModule/Router/Role.sgf");
		
		$parser = new \Strukt\Generator\Parser($sgfRoleRouter);
		
		$compiler = new \Strukt\Generator\Compiler($parser, array(

			// "excludeStandardAnnotation"=>true,
			"excludeMethodParamTypes"=>array(

				"string",
				"integer",
				"double",
				"float"
			),
			"methodAnnotationBuilder"=>function(Array $method){

				if(empty($method["annotations"]))
					return null; 
				
				foreach($method["annotations"] as $annotation){

					list($aKey, $aVal) = explode(":", $annotation, 2);

					if(strpos($aVal, "|") !== false)
						$aVal = explode("|", $aVal);

					$methAnnots[trim($aKey, "@")] = $aVal;
				}

				return new \Strukt\Generator\Annotation\Basic($methAnnots);
			}
		));

		// exit($compiler->compile());

		$fixture = Strukt\Fs::cat(sprintf("fixtures/app/src/Payroll/AuthModule/Router/Role.php"));
		
		$result = sprintf("<?php\n%s", $compiler->compile());
		
		$this->assertEquals($fixture, $result);
	}
}