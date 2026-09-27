<?php

use Strukt\DocBlocker;
use Strukt\Templator as Tpl;

if (function_exists('helper')) {
    helper('generator');
}

if (!function_exists('template') && function_exists('helper_add') && helper_add('template')) {
    /**
     * Renders a generator template.
     *
     * @param string $template Template text.
     * @param array<string, mixed> $data Template values.
     * @return string Rendered template.
     */
    function template(string $template, array $data): string
    {
        return Tpl::create($template, $data);
    }
}

if (!function_exists('huecli') && function_exists('helper_add') && helper_add('huecli')) {
    /**
     * Resolves optional console color directives.
     *
     * @param string $template Console template.
     * @return string Console-ready text.
     */
    function huecli(string $template): string
    {
        return Tpl::console($template);
    }
}

if (!function_exists('deblock') && function_exists('helper_add') && helper_add('deblock')) {
    /**
     * Removes PHPDoc comment delimiters.
     *
     * @param string $input Complete or partial docblock.
     * @return string Plain annotation text.
     */
    function deblock(string $input): string
    {
        return trim(DocBlocker::deBlock($input));
    }
}
