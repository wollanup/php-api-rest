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
    const LOCATION_PATTERN = '#\{(\w+)\}#';
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
     * @var string
     */
    protected $successLocationHeader = "";

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
     * @return bool
     */
    public function hasEntityRequest(): bool
    {
        return null !== $this->entityRequest;
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
     * @return string|null
     */
    public function getParameterToInjectInto()
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
