<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/07/17
 * Time: 13:21
 */

namespace Eukles\Env;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

abstract class Env
{
    
    const INTEGRATION = 'INTEGRATION';
    const DEVELOPMENT = 'DEV';
    const QUALITY_ASSURANCE = 'QA';
    const STAGING = 'STAGING';
    const PRODUCTION = 'PROD';
    
    /**
     * Env constructor.
     */
    protected function __construct()
    {
        try {
            $dotenv = new Dotenv($this->configPath());
            $dotenv->overload();
        } catch (InvalidPathException $e) {
            # Do nothing if no .env file
        }
    }
    
    /**
     * @return string Path of config dir
     */
    abstract protected function configPath();
    
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return array|mixed
     */
    public static function get($key, $default = null)
    {
        static $env;
        if (null === $env) {
            new static;
            $env = true;
        }
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
                return true;
            case 'false':
                return false;
            case 'null':
                return null;
        }
        
        return $value;
    }
}
