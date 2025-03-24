<?php

class BasicParserTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		//
	}

	public function test(){

		$r = new \ReflectionClass(Payroll\AuthModule\Router\Index::class);
		$parser = new \Strukt\Annotation\Parser\Basic($r);

		$annotations = array(

			"class_name"=>Payroll\AuthModule\Router\Index::class,
			"methods"=>array(

				"welcome"=>array(

					"Route"=> array("name"=>"Route", "item"=>"/"),
					"Method"=>array("name"=>"Method", "items"=>array("GET", "POST")),
					"Provides"=>array("name"=>"Provides", "item"=>"application/json"),
					"Middleware"=>array("name"=>"Middleware", "items"=>array(

						"AuthToken","GVerify"
					))				
				),
				"hello"=>array(

					"Route"=>array("name"=>"Route", "item"=>"/hello/{to:alpha}"),
					"Method"=>array("name"=>"Method", "items"=>array("GET", "POST")),
					"Provides"=>array("name"=>"Provides", "item"=>"application/html"),
					"Middlewares"=>array("name"=>"Middlewares", "items"=>array(

						"A","B","C"
					))	
				),
				"login"=>array(

					"Route"=>array("name"=>"Route", "item"=>"/login"),
					"Method"=>array("name"=>"Method", "item"=>"GET"),
					"Secure"=>array("name"=>"Secure", "items"=>array(

						"username"=>"admin",
						"password"=>"p@55w0rd"
					)),
					"Expects"=>array(

						"name"=>"Expects",
						"items"=>array(

							"username",
							"password"
						)
					)
				)
			)
		);

		$this->assertEquals($parser->getNotes(), $annotations);
	}
}