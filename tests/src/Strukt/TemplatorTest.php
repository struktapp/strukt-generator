<?php

class TemplatorTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->data = array(

		    "title" => "The Title",
		    "subtitle" => "Subtitle",
		    "footer" => "Foot",
		    "people" => array(
	            array("name" => "Steve","surname" => "Johnson"),
	            array("name" => "James", "surname" => "Johnson"),
	            array("name" => "Josh", "surname" => "Smith")
		    ),
		    "page" => "Home"
		);

		$this->tpl = "<html>
<title>{{title}}</title>
<body>
<h1>{{subtitle}}</h1>
{{begin:people}}
<b>{{name}}</b> {{surname}}<br />
{{end:people}}
<br /><br />
<i>{{footer}}</i>
</body>
</html>";
	}

	public function testEngine(){

		$result ="<html>
<title>The Title</title>
<body>
<h1>Subtitle</h1>
<b>Steve</b> Johnson<br />
<b>James</b> Johnson<br />
<b>Josh</b> Smith<br />
<br /><br />
<i>Foot</i>
</body>
</html>";

		$output = Strukt\Templator::create($this->tpl, $this->data);

		$this->assertEquals($result, $output);
	}
} 