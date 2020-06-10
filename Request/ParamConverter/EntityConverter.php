<?php

namespace FLE\JsonHydratorBundle\Request\ParamConverter;

use FLE\JsonHydrator\Entity\IdEntityInterface;
use FLE\JsonHydrator\Entity\UuidEntityInterface;
use FLE\JsonHydrator\Repository\IdRepository;
use FLE\JsonHydrator\Repository\LogicException;
use FLE\JsonHydrator\Repository\NotFoundException;
use FLE\JsonHydrator\Repository\RepositoryFactory;
use FLE\JsonHydrator\Repository\UuidRepository;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function is_numeric;

class EntityConverter implements ParamConverterInterface
{
    private RepositoryFactory $repositoryFactory;

    /**
     * EntityConverter constructor.
     *
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct(RepositoryFactory $repositoryFactory)
    {
        $this->repositoryFactory = $repositoryFactory;
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     *
     * @throws ReflectionException
     */
    public function supports(ParamConverter $configuration)
    {
        if ($configuration->getClass() === null) {
            return false;
        }
        $reflectionClass = new ReflectionClass($configuration->getClass());

        return $reflectionClass->implementsInterface(UuidEntityInterface::class) || $reflectionClass->implementsInterface(IdEntityInterface::class);
    }

    /**
     * Stores the object in the request.
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name  = $configuration->getName();
        $class = $configuration->getClass();

        if ($request->attributes->has($name) || $request->query->has($name)) {
            $value      = $request->attributes->get($name) ?? $request->query->get($name);
            $repository = $this->repositoryFactory->getRepository($class);
            if (Uuid::isValid($value) && $repository instanceof UuidRepository) {
                try {
                    $entity = $repository->findById($value);
                } catch (NotFoundException $e) {
                    throw new NotFoundHttpException("Entity \"$class\" with uuid \"$value\" not found");
                }
                $request->attributes->set($name, $entity);

                return true;
            } elseif (is_numeric($value) && $repository instanceof IdRepository) {
                try {
                    $entity = $repository->findById((int) $value);
                } catch (NotFoundException $e) {
                    throw new NotFoundHttpException("Entity \"$class\" with id \"$value\" not found");
                }
                $request->attributes->set($name, $entity);

                return true;
            } else {
                throw new LogicException('Repository must implement "UuidRepository" or "IdRepository" interface');
            }
        }

        return false;
    }
}
