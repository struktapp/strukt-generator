<?php

namespace Strukt\Generator;

/**
* Interface for annotation generators
*
* @author Moderator <pitsolu@gmail.com>
*/
interface IAnnotation{

	/**
     * Render DocBlock
     *
     * @return string
     */
	public function __toString();
}