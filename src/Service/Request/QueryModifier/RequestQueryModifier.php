<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 26/07/16
 * Time: 11:02
 */

namespace Eukles\Service\Request\QueryModifier;

use Eukles\Service\QueryModifier\RequestQueryModifierInterface;
use Eukles\Service\Request\QueryModifier\Modifier\FilterModifier;
use Eukles\Service\Request\QueryModifier\Modifier\SortModifier;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestQueryModifier
 *
 * @package Ged\Service
 */
class RequestQueryModifier implements RequestQueryModifierInterface
{
    
    /**
     * @var ServerRequestInterface
     */
    private $request;
    
    /**
     * Session constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function apply(ModelCriteria $query)
    {
        # Apply filters
        $filters = new FilterModifier($this->request);
        $filters->apply($query);
        
        # Apply sorters
        $sorters = new SortModifier($this->request);
        $sorters->apply($query);
        
        return $query;
    }
}
