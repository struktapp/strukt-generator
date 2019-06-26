<?php

namespace Payroll\AuthModule\Tests;

use Strukt\Core\Registry;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase{

	public function setUp(){

		$this->registry = Registry::getInstance();

		$this->core = $this->registry->get("core");
	}

	public function testActivate(){

		$isSuccess = $this->core->get("au.ctr.Role")->activate(1, false);

		$this->assertTrue($isSuccess);
	}
}