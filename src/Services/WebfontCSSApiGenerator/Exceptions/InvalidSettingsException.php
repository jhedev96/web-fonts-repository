<?php

namespace Src\Services\WebfontCSSApiGenerator\Exceptions;

/**
 * Class InvalidSettingsException
 *
 * Error: the given settings data has incorrect format.
 *
 * @author Finesse
 * @package Src\Services\WebfontCSSApiGenerator\Exceptions
 */
class InvalidSettingsException extends \InvalidArgumentException implements IException {}
