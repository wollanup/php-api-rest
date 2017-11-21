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
    const TYPE_FETCH = 'fetch';
    const TYPE_CREATE = 'create';
    const LOCATION_PATTERN = '#\{(\w+)\}#';
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
    protected $successLocationHeader = "";
    /**
     * @var string
     */
    protected $type;

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
     * Constructor wrapper
     *
     * @param ContainerInterface $container
     *
     * @return EntityFactoryConfig
     */
    public static function create(ContainerInterface $container)
    {
        return new self($container);
    }

    /**
     * @return EntityRequestInterface
     */
    public function getEntityRequest(): EntityRequestInterface
    {
        return $this->entityRequest;
    }

    /**
     * @param string $entityRequestClassName
     *
     * @return EntityFactoryConfig
     */
    public function setEntityRequest(string $entityRequestClassName): EntityFactoryConfig
    {
        $this->entityRequest = new $entityRequestClassName($this->getContainer());

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
     * @return bool
     */
    public function issetParameterToInjectInto(): bool
    {
        return $this->parameterToInjectInto !== null;
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
     * @param null $obj
     *
     * @return string
     */
    public function getSuccessLocationHeader($obj = null): string
    {
        if ($obj) {
            preg_match(self::LOCATION_PATTERN, $this->successLocationHeader, $matches);
            if (isset($matches[1])) {
                $getter = 'get' . ucfirst($matches[1]);
                if (method_exists($obj, $getter)) {
                    $value = call_user_func([$obj, $getter]);
                } else {
                    throw new \RuntimeException('Getter method not found in object');
                }
                preg_replace(self::LOCATION_PATTERN, $value, $this->successLocationHeader);
            } else {
                throw new \RuntimeException('Invalid pattern for replacement');
            }
        }

        return $this->successLocationHeader;
    }

    /**
     * Add a Location header to the response
     *
     * Can take a placeholder to replace a variable by an entity getter
     * e.g.
     * ```php
     * '/resource/{id}'
     * ```
     * will be replaced by
     * ```php
     * '/resource/' . $entity->getId()
     * ```
     *
     * @param string $successLocationHeader
     *
     * @return EntityFactoryConfig
     */
    public function setSuccessLocationHeader(string $successLocationHeader): EntityFactoryConfig
    {
        $this->successLocationHeader = $successLocationHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function issetHydrateEntityFromRequest(): bool
    {
        return $this->hydrateEntityFromRequest !== null;
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
    public function isTypeCreate(): bool
    {
        return $this->type === self::TYPE_CREATE;
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
    public function issetEntityRequest(): bool
    {
        return null !== $this->entityRequest;
    }

    /**
     * @return bool
     */
    public function hasSuccessLocationHeader(): bool
    {
        return $this->successLocationHeader !== null;
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

    public function validate()
    {
        if (!$this->type || !in_array($this->type, self::$types)) {
            throw new EntityFactoryConfigException('Config must have an EntityRequest class');
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
