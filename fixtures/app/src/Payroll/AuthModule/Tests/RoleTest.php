<?php

namespace Payroll\AuthModule\Tests;

use Strukt\Core\Registry;
use Contract\TestCase as AbstractTestCase;

class RoleTest extends AbstractTestCase{

	public function setUp(){

		$this->registry = Registry::getInstance();

		$this->core = $this->registry->get("core");
	}

	public function testActivate(){

		$isSuccess = $this->core->get("au.ctr.Role")->activate(1, false);

		$this->assertTrue($isSuccess);
	}
}