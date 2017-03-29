<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/03/17
 * Time: 17:00
 */

namespace Eukles\Service\Request;

use Psr\Http\Message\ServerRequestInterface;

trait GetParam
{
    
    /**
     * Fetch request parameter value from body or query string (in that order).
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param ServerRequestInterface $request
     * @param  string                $key     The parameter key.
     * @param  string                $default The default value.
     *
     * @return mixed The parameter value.
     */
    public function getParam(ServerRequestInterface $request, $key, $default = null)
    {
        $postParams = $request->getParsedBody();
        $getParams  = $request->getQueryParams();
        $result     = $default;
        if (is_array($postParams) && isset($postParams[$key])) {
            $result = $postParams[$key];
        } elseif (is_object($postParams) && property_exists($postParams, $key)) {
            $result = $postParams->$key;
        } elseif (isset($getParams[$key])) {
            $result = $getParams[$key];
        }
        
        return $result;
    }
}
