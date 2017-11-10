<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/03/17
 * Time: 17:06
 */

namespace Eukles\Service\Pagination;

interface PaginationInterface
{

    /**
     *
     */
    const REQUEST_PARAM_PAGE = 'page';
    /**
     *
     */
    const REQUEST_PARAM_LIMIT = 'limit';
    /**
     * The maximum number of items to return when using paginate
     */
    const MAX_LIMIT = 500;
    /**
     *
     */
    const DEFAULT_LIMIT = 20;
    /**
     *
     */
    const DEFAULT_PAGE = 1;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return int
     */
    public function getPage(): int;
}
