<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 21/11/17
 * Time: 10:03
 */

namespace Eukles\Service\Router;

class HttpStatus
{

    /**
     * @var string
     */
    protected $description = "Response is successful";
    /**
     * @var bool
     */
    protected $mainSuccess = true;
    /**
     * @var int
     */
    protected $status = 200;

    public static function create()
    {
        return new self;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return HttpStatus
     */
    public function setDescription(string $description): HttpStatus
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return HttpStatus
     */
    public function setStatus(int $status): HttpStatus
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMainSuccess(): bool
    {
        return $this->mainSuccess;
    }

    /**
     * @param bool $mainSuccess
     *
     * @return HttpStatus
     */
    public function setMainSuccess(bool $mainSuccess): HttpStatus
    {
        $this->mainSuccess = $mainSuccess;

        return $this;
    }
}
