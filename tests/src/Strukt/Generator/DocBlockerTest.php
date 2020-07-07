<?php

class DocBlockerTest extends PHPUnit\Framework\TestCase{

	public function testDeBlock(){

		$after = (string) new Strukt\Generator\Annotation\Standard(array(

			"descr"=>"Flag to check if compiled was executed",
			"param"=>"compiled",
			"type"=>"boolean"
		));

$before = "Flag to check if compiled was executed

@var boolean \$compiled";

		$after = \Strukt\Generator\DocBlocker::deBlock($after);

		$before = trim(preg_replace("/[\r\n]/", "", $before));
		$after = trim(preg_replace("/[\r\n]/", "", $after));

		$this->assertEquals($before, $after);
	}
}