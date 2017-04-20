<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 19/04/17
 * Time: 17:54
 */

namespace Eukles\Entity;

use Eukles\Service\QueryModifier\Modifier\Exception\RequestFactoryExceptionInterface;
use Symfony\Component\Translation\Translator;

class PrimaryKeyNotFoundException extends \Exception implements RequestFactoryExceptionInterface
{
    
    /**
     * @var int
     */
    protected $code = 400;
    /**
     * @var EntityRequestInterface
     */
    protected $entityRequest;
    /**
     * @var string
     */
    protected $message = "Primary key not found in request";
    
    /**
     * RequestFactoryExceptionInterface constructor.
     *
     * @param EntityRequestInterface $entityRequest
     * @param \Exception             $previous
     */
    public function __construct(
        EntityRequestInterface $entityRequest,
        \Exception $previous = null
    ) {
        $this->entityRequest = $entityRequest;
        parent::__construct($this->message, $this->code, $previous);
    }
    
    /**
     * @param Translator $translator
     *
     * @return string
     */
    public function getDetail(Translator $translator)
    {
        // TODO: Implement getDetail() method.
    }
    
    /**
     * @return string
     */
    public function getInstance()
    {
        // TODO: Implement getInstance() method.
    }
    
    /**
     * @param Translator $translator
     *
     * @return string
     */
    public function getTitle(Translator $translator)
    {
        return $translator->trans("exception.rights.permissionDenied");
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        // TODO: Implement getType() method.
        return "";
    }
}
