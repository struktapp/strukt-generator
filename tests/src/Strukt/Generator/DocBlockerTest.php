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

		$deblocked = \Strukt\Generator\DocBlocker::deBlock($after);
		$reblocked = (string) new \Strukt\Generator\DocBlocker($deblocked);

		$this->assertEquals($deblocked, $before);

		$reblocked = str_replace(array("\n", " "), "", $reblocked);
		$after = str_replace(array("\n", " "), "", $after);

		$this->assertEquals($reblocked, $after);
	}
}