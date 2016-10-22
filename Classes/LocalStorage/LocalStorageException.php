<?php
/**
 * This file is a part of remotepics_gather project.
 * Author: Mykola Danylov (n.danylov@gmail.com)
 * Date: 22.10.2016
 * Time: 14:51
 */

namespace MykolaDanylov\LocalStorage;

use MykolaDanylov\RemotePics\RemotePicException;

/**
 * Class LocalStorageException
 * @package MykolaDanylov\LocalStorage
 *      This NEW exception class stores given variables data to public field!
 *      So, using variable data we can show any important information to user even if error occurs!
 *      Note that signature of exception constructor is standard as before. It's nice.
 */

class LocalStorageException extends RemotePicException
{
}