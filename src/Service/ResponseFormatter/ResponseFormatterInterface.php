<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 28/07/16
 * Time: 16:50
 */

namespace Eukles\Service\ResponseFormatter;

use Psr\Http\Message\ResponseInterface;

interface ResponseFormatterInterface
{

    public function __invoke(ResponseInterface $response, $result);
}
