<?php

namespace Strukt\Generator;

// use Strukt\Helper\Str;

use Strukt\Util\Str;

/**
* Generator validation
*
* @author Moderator <pitsolu@gmail.com>
*/
class Validator{

	/**
	* Constructor
	*
	* @param string $tokens
	*/
	public function __construct($tokens){

		if(!is_string($tokens))
			throw new \Exception("Validator constructor requires string as 1st argument!");

		$this->validate(sprintf("%s\n", trim($tokens)));
	}

	/**
	* validator executions
	*
	* @param string $tokens
	*/
	private function validate($tokens){

		if(preg_match("/^\n/", $tokens))
			throw new \Exception("Space at the beginning of blocks!");

		preg_match_all("/(^@\w+|\n@\w+)/", $tokens, $tags);
		foreach(array_unique(reset($tags)) as $tag)
			if(!in_array(trim($tag), array(

					"@ns",
					"@import",
					"@class",
					"@inherit",
					"@interface",
					"@method",
					"@descr",
					"@body",
					"@param"
				)))
					throw new \Exception(sprintf("Invalid tag [%s]!", trim($tag)));

		$prevLine=null;
		$expectCloser=false;
		$hasClass=false;

		foreach(explode("\n", $tokens) as $seqKey=>$line){

			$line = trim($line);

			if((new Str($line))->startsWith("@ns"))
				if(!preg_match("/^@ns:[\w\\\w]+$/", $line))
					throw new \Exception("Invalid @ns tag!");

			if((new Str($line))->startsWith("@param")){

				if(!empty(trim($prevLine)))
					throw new \Exception(sprintf("Line before [%s] must be empty!", $line));

				if(!preg_match("/^@param:((public|protected|private)>)?(static>)?\w+(#[\w\\\w]+)?(=.*)?$/", $line))
					throw new \Exception(sprintf("Syntax error at [%s]!", $line));
			}

			if((new Str($line))->startsWith("@method")){

				if(!empty(trim($prevLine)))
					throw new \Exception(sprintf("Line before [%s] must be empty!", $line));

				if(!preg_match("/^@method:((public|protected|private)>)?\w+(#[\w\\\w]+)?(@param:\w+([|#\w]+|#[\w\\\w#|=]+)?)?$/", $line))
					throw new \Exception(sprintf("Syntax error at [%s]!", $line));
			}

			if((new Str($line))->startsWith("@descr")){

				if(!preg_match("/^@descr(:)?[@\w ].*$/", $line) &&
					!preg_match("/^@descr$/", $line))
						throw new \Exception(sprintf("Invalid tag block [%s]!", $line));

				if((new Str($line))->startsWith("@descr") && trim($line)=="@descr:")
					throw new Exception("Inline tag [@descr:] cannot be empty!");
			}

			if(trim($line) == "@descr"){

				if(!$expectCloser){

					if(!preg_match("/^@(body|ns|class|inherit|interface|descr|method)/", $prevLine))
						throw new \Exception(sprintf("Tag before [%s] must be either [body|ns|class|inherit|descr|method] but found [%s]!", $line, $prevLine));
				}

				$expectCloser =! $expectCloser;

				if($expectCloser)
					$expect="@descr";
			}

			if((new Str($line))->startsWith("@body")){

				if(!preg_match("/^@body$/", $line) &&
					!preg_match("/^@body:.*$/", $line))
						throw new \Exception(sprintf("Expecting [@body] or [@body:..] but found [%s]", $line));

				if((new Str($line))->startsWith("@body") && trim($line)=="@body:")
					throw new Exception("Inline tag [@body:] cannot be empty!");
			}

			if(trim($line) == "@body"){
			
				if(!$expectCloser)
					if(!(new Str($prevLine))->startsWith("@method"))
						throw new \Exception(sprintf("Tag before [%s] must be @method!", $line));

				$expectCloser =! $expectCloser;	

				if($expectCloser)
					$expect="@body";
			}

			if($expectCloser && (new Str($line))->startsWith("@"))
				if(preg_match("/^@(ns|class|inherit|interface|method|descr|body|param)/", $line))
					if(trim($line)!=$expect)
						throw new \Exception(sprintf("Expecting %s tag but found [%s]!", $expect, $line));

			if(!$expectCloser){

				if(!(new Str($line))->startsWith("@") && !empty(trim($line)))
					throw new \Exception(sprintf("Invalid line [%s]!", $line));

				if(trim($prevLine) == trim($line))
					throw new \Exception("Detected an extra newline outside @body tags!");
			}

			$prevLine = $line;
		}
	}
}