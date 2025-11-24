<?php

namespace Strukt;

class BasicBlock{

	public static function parse(string $doc){

		$lines = str(trim($doc))->split("\n");
		$lines = arr($lines)->filter()->map(function($k, $v){

			if(preg_match("/\@\w+\(.*\)/", $v)){

				$params = str($v)->btwn("(",")");
				if($params->contains(","))
					$params = $params->split(",");

				if($params instanceof \Strukt\Str || $params instanceof \Strukt\Arr)
					$params = $params->yield();

				return [str($v)->btwn("@","(")->yield() => $params];
			}

			return [];
		});

		$nlines = [];
		$lines = $lines->level(2);
		foreach($lines as $k=>$v)
			$nlines[preg_match("/\d+\.\w/", $k)?explode(".", $k)[1]:$k] = $v;

		return $nlines;
	}
}