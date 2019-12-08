<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Autoloader Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
namespace functions;

class Autoloader
{
    // Define class variables
    public $preBasePath = DIRECTORY_SEPARATOR;

    /**
     * Autoloader constructor.
     */
    public function __construct()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * @param string $strBasePath           Base path of project
     */
    public static function register($strBasePath)
    {
        $object = new Autoloader();
        $object->preBasePath = $strBasePath;
    }

    /**
     * Load class from path
     * @param string $strClassName          Class name
     */
    public function loadClass($strClassName)
    {
        $className = ltrim($strClassName, '\\');
        $fileName  = '';
        $lastNsPos = strrpos($className, '\\');
        if ($lastNsPos != 0) {
            $namespace  = substr($className, 0, $lastNsPos);
            $className  = substr($className, $lastNsPos + 1);
            $fileName   = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName     .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $strFilePath1  = $this->preBasePath.$fileName;
        $strFilePath2  = $this->preBasePath.'install/'.$fileName;
        if (file_exists($strFilePath1) && is_readable($strFilePath1)) {
            require_once $strFilePath1;
        }
        if (file_exists($strFilePath2) && is_readable($strFilePath2)) {
            require_once $strFilePath2;
        }
    }
}
