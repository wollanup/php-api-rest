<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/07/17
 * Time: 10:17
 */

namespace Eukles\Config;

use Adbar\Dot;
use Eukles\Env\Env;

class Config extends Dot
{
    
    /**
     * Export config as array php code
     *
     * Use in production to write a config.cache.php
     *
     * @return string array php code
     */
    public function export()
    {
        return var_export($this->all(), true);
    }
    
    /**
     * @param string|array $environment Can be 'production' or ['dev', 'test']
     *
     * @return bool
     */
    public function isEnvironment($environment)
    {
        return in_array($this->get('app.environment'), (array)$environment);
    }
    
    /**
     * @return bool
     */
    public function isNotProduction()
    {
        return false === $this->isProduction();
    }
    
    /**
     * @return bool
     */
    public function isProduction()
    {
        return in_array($this->get('app.environment'), [Env::PRODUCTION, Env::STAGING, Env::QUALITY_ASSURANCE]);
    }
}
