<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/03/17
 * Time: 16:45
 */

namespace Eukles\Service\Request\Pagination;

use Eukles\Service\Request\GetParam;
use Psr\Http\Message\ServerRequestInterface;

class RequestPagination implements RequestPaginationInterface
{

    protected $limit = self::DEFAULT_LIMIT;
    protected $page = self::DEFAULT_PAGE;
    use GetParam;
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * RequestPagination constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        $limit = (int)$this->getParam($this->request, self::REQUEST_PARAM_LIMIT, self::DEFAULT_LIMIT);

        if ($limit > self::MAX_LIMIT) {
            $limit = self::MAX_LIMIT;
        }
        if ($limit < 1) {
            $limit = 1;
        }
        $this->limit = $limit;

        $page       = (int)$this->getParam($this->request, self::REQUEST_PARAM_PAGE, self::DEFAULT_PAGE);
        $this->page = $page < 1 ? self::DEFAULT_PAGE : $page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }
}
