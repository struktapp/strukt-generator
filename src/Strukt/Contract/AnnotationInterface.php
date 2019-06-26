<?php

namespace Strukt\Contract;

/**
* Interface for annotation generators
*
* @author Moderator <pitsolu@gmail.com>
*/
interface AnnotationInterface{

	/**
     * Render DocBlock
     *
     * @return string
     */
	public function __toString();
}