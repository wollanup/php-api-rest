<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/07/17
 * Time: 09:20
 */

namespace Eukles\Config;

interface ConfigInterface
{

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key);
}
