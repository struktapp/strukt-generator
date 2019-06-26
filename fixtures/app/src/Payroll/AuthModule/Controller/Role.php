<?php

namespace Payroll\AuthModule\Controller;

class Role extends \App\Data\Controller implements \App\Data\IController, \App\Data\IStatus{

	/**
	* @param integer $id
	* 
	* @return Payroll\AuthModule\Model\Role
	*/
	public function find($id=1){

		
	}

	/**
	* @param array $filter
	* @param integer $rows
	* @param integer $pageNum
	* 
	* @return array
	*/
	public function findAll(array $filter, $rows, $pageNum){

		
	}

	/**
	* @param Payroll\AuthModule\Model\Role $role
	* 
	* @return boolean
	*/
	public function add(Payroll\AuthModule\Model\Role $role){

		
	}

	/**
	* @param $id
	* @param array $data
	* 
	* @return boolean
	*/
	public function update($id, array $data){

		
	}

	/**
	* @param $id
	* 
	* @return boolean
	*/
	public function remove($id){

		
	}

	/**
	* @param integer $id
	* @param boolean $status
	* 
	* @return boolean
	*/
	public function activate($id, boolean $status=false){

		
	}
}