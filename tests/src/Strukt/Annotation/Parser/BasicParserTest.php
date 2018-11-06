<?php

class BasicParserTest extends PHPUnit\Framework\TestCase{

	public function test(){

		$r = new \ReflectionClass("Payroll\AuthModule\Router\Index");
		$parser = new \Strukt\Annotation\Parser\Basic($r);

		$annotations["methods"]["welcome"]["Route"] = array("item"=>"/", "name"=>"Route");
		$annotations["methods"]["welcome"]["Method"]["name"] = "Method";
		$annotations["methods"]["welcome"]["Method"]["items"] = array("GET", "POST");
		$annotations["methods"]["welcome"]["Provides"]["name"] = "Provides";
		$annotations["methods"]["welcome"]["Provides"]["item"] = "application/json";
		$annotations["methods"]["hello"]["Route"]["name"] = "Route";
		$annotations["methods"]["hello"]["Route"]["item"] = "/hello/{to:alpha}";
		$annotations["methods"]["hello"]["Method"]["name"] = "Method";
		$annotations["methods"]["hello"]["Method"]["items"] = array("GET", "POST");
		$annotations["methods"]["hello"]["Provides"]["name"] = "Provides";
		$annotations["methods"]["hello"]["Provides"]["item"] = "application/html";
		$annotations["methods"]["login"]["Route"]["name"] = "Route";
		$annotations["methods"]["login"]["Route"]["item"] = "/login";
		$annotations["methods"]["login"]["Method"]["name"] = "Method";
		$annotations["methods"]["login"]["Method"]["item"] = "GET";
		$annotations["methods"]["login"]["Secure"]["name"] = "Secure";
		$annotations["methods"]["login"]["Secure"]["items"]["username"] = "admin";
		$annotations["methods"]["login"]["Secure"]["items"]["password"] = "p@55w0rd";
		$annotations["methods"]["login"]["Expects"]["name"] = "Expects";
		$annotations["methods"]["login"]["Expects"]["items"] = array("username","password");
		$annotations['class_name']='Payroll\AuthModule\Router\Index';

		$this->assertEquals($parser->getAnnotations(), $annotations);
	}
}