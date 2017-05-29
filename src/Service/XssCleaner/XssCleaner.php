<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 29/05/17
 * Time: 16:06
 */

namespace Eukles\Service\XssCleaner;

class XssCleaner implements XssCleanerInterface
{
    
    /**
     * @param array $array
     *
     * @return array
     */
    public function cleanArray(array $array)
    {
        return array_map(['self', 'cleanString'], $array);
    }
    
    /**
     * @param string $string
     *
     * @return string
     */
    public function cleanString($string)
    {
        return is_scalar($string) ? filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS) : $string;
    }
}
