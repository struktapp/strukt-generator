<?php

namespace Payroll;

use Contract\Model as AbstractModel;

class User extends AbstractModel{

	private  $id;

	private  $username;

	private  $password;

	public function __construct($username, $password){

		$this->setUsername($username);
		$this->setPassword($password);
	}

	public function setId($id){

		$this->id = $id;
	}

	public function getId(){

		return $this->id;
	}

	public function setUsername($username){

		$this->username = $username;
	}

	public function getUsername(){

		return $this->username;
	}

	public function setPassword($password){

		$this->password = sha1(trim($password));
	}

	public function getPassword(){

		return $this->password;
	}

	public function setPhone(string $prefix, int $number){

		//
	}

	public function setDob($dd, string $mm, int $yy){

		//
	}
}