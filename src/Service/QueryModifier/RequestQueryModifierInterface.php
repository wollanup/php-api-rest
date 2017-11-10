<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 26/07/16
 * Time: 11:07
 */

namespace Eukles\Service\QueryModifier;

use Psr\Http\Message\ServerRequestInterface;

interface RequestQueryModifierInterface extends QueryModifierInterface
{

    /**
     * Session constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request);
}
