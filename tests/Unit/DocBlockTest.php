<?php

use Strukt\DocBlocker;

test("docblock", function(){

	$docblock = "	/**
	* @Route(/)
	* @Method(GET, POST)
	* @Provides(application/json)
	* @Middleware(AuthToken)
	* @Middleware(GVerify)
	*/";

	$deblocked = "@Route(/)
@Method(GET, POST)
@Provides(application/json)
@Middleware(AuthToken)
@Middleware(GVerify)";

	$output = DocBlocker::deBlock($docblock);
	expect(trim($output))->toBe($deblocked);
	$docblocked = (string)new DocBlocker($output);
	$docblocked = str($docblocked)->pad()->block()->left();
	expect($docblocked)->toBe($docblock);
});