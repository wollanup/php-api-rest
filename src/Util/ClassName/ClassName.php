<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 01/02/17
 * Time: 13:53
 */

namespace Eukles\Util\ClassName;

/**
 * Class ClassNameList
 *
 * @package Eukles\Util
 */
class ClassName
{

    /**
     * @param $file
     *
     * @return string
     * @throws FileNotFoundException
     * @throws InvalidClassException
     */
    final public static function getFromFile($file)
    {
        if (!is_readable($file)) {
            throw new FileNotFoundException;
        }

        $fp = fopen($file, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;
        while (!$class) {
            if (feof($fp)) {
                break;
            }

            $buffer .= fread($fp, 512);
            $tokens = @token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }
            $total = count($tokens);
            for (; $i < $total; $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\' . $tokens[$j][1];
                        } else {
                            if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                                break;
                            }
                        }
                    }
                } elseif ($tokens[$i][0] === T_CLASS) {
                    for ($j = $i + 1; $j < count($tokens); $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i + 2][1];
                            break;
                        }
                    }
                    break;
                }
            }
        }
        fclose($fp);
        if (!$class) {
            throw new InvalidClassException;
        }

        return $namespace . '\\' . $class;
    }
}
