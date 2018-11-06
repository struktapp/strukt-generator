<?php

class NoAnnotationsTest extends PHPUnit\Framework\TestCase{

	public function test(){

		$class = array(

			"declaration"=>array(

				"namespace"=>"Payroll\AuthModule\Model",
				"name"=>"User"
			),
			"properties"=>array(

				array(

					"access"=>"private",
					"name"=>"id",
					"type"=>"integer"
				),
				array(

					"access"=>"private",
					"name"=>"username",
					"type"=>"string"
				),
				array(

					"access"=>"private",
					"name"=>"password",
					"type"=>"string"
				)
			),
			"methods"=>array(

				array(

					"name"=>"__construct",
					"params"=>array("username","password"),
					"return"=>"",
					"body"=>"
\t\t\$this->setUsername(\$username);
\t\t\$this->setPassword(\$password);"
				),
				array(
					
					"name"=>"setId",
					"params"=>"id",
					"body"=>"\$this->id = \$id;"
				),
				array(
				
					"name"=>"getId",
					"body"=>"return \$this->id;"
				),
				array(

					"name"=>"setUsername",
					"params"=>array("username"),
					"body"=>"\$this->username = \$username;"
				),
				array(

					"name"=>"getUsername",
					"body"=>"return \$this->username;"
				),
				array(

					"name"=>"setPassword",
					"params"=>array("password"),
					"body"=>"\$this->password = sha1(trim(\$password));"
				),
				array(

					"name"=>"getPassword",
					"body"=>"return \$this->password;"
				),
				array(

					"name"=>"setPhone",
					"params"=>array(

						"prefix"=>"string",
						"number"=>"int"
					)
				),
				array(

					"name"=>"setDob",
					"params"=>array(

						"dd",
						"mm"=>"string",
						"yy"=>"int"
					)
				)
			)
		);
								
		$builder = new \Strukt\Generator\ClassBuilder($class["declaration"]);
		foreach($class["properties"] as $property)
			$builder->addProperty($property);

		foreach($class["methods"] as $method)
			$builder->addMethod($method);

		// exit($builder);
		$ns = sprintf(sprintf("%s\%s", $class["declaration"]["namespace"], $class["declaration"]["name"]));
		$fixture = Strukt\Fs::cat(sprintf("fixtures/root/app/src/%s.php", str_replace("\\", "/", $ns)));
		$result = sprintf("<?php\n%s", (string)$builder);

		// exit($result);
		
		$this->assertEquals($fixture, $result);
	}
}