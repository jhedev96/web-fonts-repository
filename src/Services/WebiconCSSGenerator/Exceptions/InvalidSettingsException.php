<?php

namespace Src\Services\WebiconCSSGenerator\Exceptions;

/**
 * Class InvalidSettingsException
 *
 * Error: the given settings data has incorrect format.
 *
 * @author Finesse
 * @package Src\Services\WebiconCSSGenerator\Exceptions
 */
class InvalidSettingsException extends \InvalidArgumentException implements IException {}
