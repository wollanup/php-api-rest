<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 28/07/16
 * Time: 16:50
 */

namespace Eukles\Service\ResponseBuilder;

use Psr\Container\ContainerInterface;

interface ResponseBuilderInterface
{

    /**
     * ResponseBuilderInterface constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param $result
     *
     * @return mixed
     */
    public function __invoke($result);
}
