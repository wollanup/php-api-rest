<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 04/04/17
 * Time: 09:08
 */

namespace Eukles\Service\Pagination;

class Pagination implements PaginationInterface
{

    /**
     * @var
     */
    protected $limit = self::DEFAULT_LIMIT;
    /**
     * @var int
     */
    protected $page = self::DEFAULT_PAGE;

    /**
     * Pagination constructor.
     *
     * @param null $page
     * @param null $limit
     */
    public function __construct($page = null, $limit = null)
    {
        $this->setPage($page);
        $this->setLimit($limit);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return PaginationInterface
     */
    public function setLimit($limit)
    {
        if (!is_scalar($limit)) {
            $this->limit = self::DEFAULT_LIMIT;

            return $this;
        } else {
            $limit = (int)$limit;
        }

        if ($limit > self::MAX_LIMIT) {
            $this->limit = self::MAX_LIMIT;
        } elseif ($limit < 1) {
            $this->limit = self::DEFAULT_LIMIT;
        } else {
            $this->limit = $limit;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function setPage($page)
    {
        if (!is_scalar($page)) {
            $this->page = self::DEFAULT_PAGE;

            return $this;
        } else {
            $page = (int)$page;
        }

        if ($page < 1) {
            $this->page = 1;
        } else {
            $this->page = $page;
        }

        return $this;
    }
}
