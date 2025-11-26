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

if(helper_add("deblock")){

	/**
	 * @param string $input
	 * 
	 * @return string
	 */
	function deblock(string $input){

		$output = Strukt\DocBlocker::deBlock($input);

		return trim($output);
	}
}

