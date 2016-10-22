<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 22.10.2016
 * Time: 15:02
 */

namespace MykolaDanylov\RemotePics;

/**
 * Class RemotePicException
 * @package MykolaDanylov\RemotePics
 */
class RemotePicException extends \Exception
{
    // List of error codes for use
    /** This is not error but message used exception can be transfered up to JS */
    const E_IS_NOT_ERROR = 0; //0
    /** This error like previous but must be shown to user necessarily. Application specified. */
    const E_JUST_FOR_USER = E_WARNING ; // 2
    /** This for handled error if you want send mail and/or log to error log. */
    const E_SERIOUS = E_USER_ERROR; // 256
    /** @var array - Here it stores any variables passed to Exception as second parameter */
    public $variables;

    /**
     * @param string $message - Message about error
     * @param array $variables - any variables. Also can be used in $message like "...:var_name..."
     * @param int $code - error code. Using constants above is good idea.
     * @param bool $escape - Prevent XSS by escaping all of the variables, some of which may be user-generated.
     */
    public function __construct($message, array $variables = NULL, $code = 0)
    {
        $this->variables = $variables;
        // Make the error by the parent Exception class
        parent::__construct($message, $code);
    }
}