<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 17/10/16
 * Time: 16:45
 */

namespace Eukles\Entity;

use Eukles\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

class EntityFactoryConfig
{

    const TYPE_FETCH = 'fetch';
    const TYPE_CREATE = 'create';
    /**
     * Instance creation method
     *
     * @var array
     */
    protected static $types = [self::TYPE_CREATE, self::TYPE_FETCH];
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
    protected $hydrateEntityFromRequest;
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
     * @var string
     */
    protected $type;

    /**
     * Constructor wrapper
     *
     * @return EntityFactoryConfig
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getEntityRequest(): string
    {
        return $this->entityRequest;
    }

    /**
     * @param RequestInterface $request
     * @param ContainerInterface $container
     *
     * @return EntityRequestInterface
     */
    public function createEntityRequest(
        RequestInterface $request,
        ContainerInterface $container
    ): EntityRequestInterface
    {
        /** @var EntityRequestInterface $er */
        $er = new $this->entityRequest($request);
        $er->setContainer($container);

        return $er;
    }

    /**
     * @param string $entityRequestClass Name
     *
     * @return EntityFactoryConfig
     */
    public function setEntityRequest(string $entityRequestClass): EntityFactoryConfig
    {
        $this->entityRequest = $entityRequestClass;

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
    public function setParameterToInjectInto(string $parameterToInjectInto): EntityFactoryConfig
    {
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
     * @param string $requestParameterName
     *
     * @return EntityFactoryConfig
     */
    public function setRequestParameterName(string $requestParameterName): EntityFactoryConfig
    {
        $this->requestParameterName = $requestParameterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return EntityFactoryConfig
     */
    public function setType(string $type): EntityFactoryConfig
    {
        $this->type = $type;

        return $this;
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
    public function setHydrateEntityFromRequest(bool $hydrateEntityFromRequest): EntityFactoryConfig
    {
        $this->hydrateEntityFromRequest = $hydrateEntityFromRequest;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTypeCreate(): bool
    {
        return $this->type === self::TYPE_CREATE;
    }

    /**
     * @return bool
     */
    public function isTypeFetch(): bool
    {
        return $this->type === self::TYPE_FETCH;
    }

    /**
     * @return bool
     */
    public function issetEntityRequest(): bool
    {
        return null !== $this->entityRequest;
    }

    public function issetHydrateEntityFromRequest(): bool
    {
        return $this->hydrateEntityFromRequest !== null;
    }

    /**
     * @return bool
     */
    public function issetParameterToInjectInto(): bool
    {
        return $this->parameterToInjectInto !== null;
    }

    /**
     * @throws EntityFactoryConfigException
     */
    public function validate()
    {
        if (!$this->type || !in_array($this->type, self::$types)) {
            throw new EntityFactoryConfigException('Config must have a type');
        }
        if ($this->hydrateEntityFromRequest === null) {
            throw new EntityFactoryConfigException('Config must know if entity will be hydrated with request params');
        }
        if (!$this->entityRequest) {
            throw new EntityFactoryConfigException('Config must have an EntityRequest class');
        }
        if (!$this->parameterToInjectInto) {
            throw new EntityFactoryConfigException(
                'Config must have a parameter name for inject entity in action method'
            );
        }
    }
}
