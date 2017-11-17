<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/03/17
 * Time: 17:06
 */

namespace Eukles\Service\Request\Pagination;

use Eukles\Service\Pagination\PaginationInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestPaginationInterface extends PaginationInterface
{

    /**
     * RequestPagination constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request);
}
