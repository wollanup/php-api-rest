<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 17/10/16
 * Time: 16:45
 */

namespace Eukles\Entity;

use Eukles\Container\ContainerInterface;
use Eukles\Container\ContainerTrait;

class EntityFactoryConfig
{

    use ContainerTrait;
    /**
     * Entity Request class used to instantiate and hydrate Entity
     *
     * @var EntityRequestInterface
     */
    protected $entityRequest;
    /**
     * Use request parameters to hydrate Entity, or not
     *
     * @var bool
     */
    protected $hydrateEntityFromRequest = true;
    /**
     * Name of parameter in Action method
     *
     * @var string
     */
    protected $parameterToInjectInto;
    /**
     * Name of parameter representing id of Entity in request
     *
     * @var string
     */
    protected $requestParameterName = "id";

    /**
     * EntityFactoryConfig constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return EntityRequestInterface
     */
    public function getEntityRequest(): EntityRequestInterface
    {
        return $this->entityRequest;
    }

    /**
     * @param EntityRequestInterface $entityRequest
     *
     * @return EntityFactoryConfig
     */
    public function setEntityRequest(EntityRequestInterface $entityRequest
    ): EntityFactoryConfig {
        $this->entityRequest = $entityRequest;

        return $this;
    }

    /**
     * @return string
     */
    public function getParameterToInjectInto(): string
    {
        return $this->parameterToInjectInto;
    }

    /**
     * @param string $parameterToInjectInto
     *
     * @return EntityFactoryConfig
     */
    public function setParameterToInjectInto(string $parameterToInjectInto
    ): EntityFactoryConfig {
        $this->parameterToInjectInto = $parameterToInjectInto;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestParameterName(): string
    {
        return $this->requestParameterName;
    }

    /**
     * @return bool
     */
    public function isHydrateEntityFromRequest(): bool
    {
        return $this->hydrateEntityFromRequest;
    }

    /**
     * @param bool $hydrateEntityFromRequest
     *
     * @return EntityFactoryConfig
     */
    public function setHydrateEntityFromRequest(bool $hydrateEntityFromRequest
    ): EntityFactoryConfig {
        $this->hydrateEntityFromRequest = $hydrateEntityFromRequest;

        return $this;
    }

    /**
     * @param string $requestParameterName
     *
     * @return EntityFactoryConfig
     */
    public function setRequestParameter(string $requestParameterName
    ): EntityFactoryConfig {
        $this->requestParameterName = $requestParameterName;

        return $this;
    }
}
