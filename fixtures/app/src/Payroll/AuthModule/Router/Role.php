<?php

namespace Payroll\AuthModule\Router;

use Payroll\AuthModule\Controller\Role as RoleC;
use App\Data\Router as BaseRouter;

/**
* Router for roles
* 
* @author: Moderator <pitsolu@gmail.com>
*/
class Role extends BaseRouter{

	/**
	* @var string $name
	*/
	public static $name = "Payroll\AuthModule\Router\Role";

	/**
	* @Route(/role/{id:int})
	* @Method(POST)
	* 
	* Blah
	* Blah
	* Blah
	* 
	* @param integer $id
	* 
	* @return Strukt\Rest\ResposeType\JsonResponse
	*/
	public function findRoleById($id){

		//
	}

	/**
	* @Route(/role/{id:int})
	* @Method(DELETE)
	* 
	* Delete Role
	* 
	* @param $id
	* 
	* @return Strukt\Rest\ResposeType\JsonResponse
	*/
	public function deleteByRoleId($id){

		//
	}

	/**
	* @Route(/role/all)
	* @Method(GET, POST)
	* 
	* Find All
	* 
	* @return Strukt\Rest\ResposeType\JsonResponse
	*/
	public function findAll(){

		// To be implemented
	}

	/**
	* @Route(/role/{role_id:int}/add/perm/{perm_id:int})
	* @Method(POST)
	* 
	* Role Add Permission
	* 
	* @param integer $role_id
	* @param integer $perm_id
	* 
	* @return string
	*/
	public function addRolePermission($role_id, $perm_id){

		$rolePerm = RoleC::addPerm($role_id, $perm_id);

		return "success";
	}
}