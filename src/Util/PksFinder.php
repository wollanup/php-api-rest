<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 21/04/17
 * Time: 09:54
 */

namespace Eukles\Util;

class PksFinder
{

    /**
     * @var array
     */
    protected $possiblePks = [];

    /**
     * PksFinder constructor.
     *
     * @param array $possiblePkName
     */
    public function __construct(array $possiblePkName = ['id'])
    {
        $this->possiblePks = $possiblePkName;
    }

    /**
     * @param array|object $data
     *
     * @return array
     */
    public function find(array $data)
    {
        if (empty($data)) {
            return [];
        }

        foreach ($this->possiblePks as $possiblePk) {
            if (array_key_exists($possiblePk, $data)) {
                $foundPk = $data[$possiblePk];
                return is_array($foundPk) ? $foundPk : [$foundPk];
            }
        }

        $pks = [];
        foreach ($data as $item) {
            if (is_scalar($item)) {
                $pks[] = $item;
                continue;
            }

            if (is_object($item)) {
                $item = (array)$item;
            }
            if (is_array($item)) {
                foreach ($this->possiblePks as $possiblePk) {
                    if (array_key_exists($possiblePk, $item)) {
                        $pks[] = $item[$possiblePk];
                        break;
                    }
                }
            }
        }

        return $pks;
    }
}
