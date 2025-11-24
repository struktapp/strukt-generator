<?php

use Strukt\Templator as Tpl;

helper("generator");

if(helper_add("template")){

	/**
	 * @param string $tpl
	 * @param array $data
	 * 
	 * @return string
	 */
	function template(string $tpl, array $data):string{

		return Tpl::create($tpl, $data);
	}
}

if(helper_add("huecli")){

	/**
	 * @param string $tpl
	 * 
	 * @return string
	 */
	function huecli(string $tpl):string{

		return Tpl::console($tpl);
	}
}

