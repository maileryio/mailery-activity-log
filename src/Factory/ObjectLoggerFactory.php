<?php

namespace Mailery\Activity\Log\Factory;

use Cycle\ORM\FactoryInterface;
use Spiral\Database\DatabaseInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\LoaderInterface;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\Relation\RelationInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\SourceInterface;
use Cycle\ORM\RepositoryInterface;
use Mailery\Activity\Log\Mapper\ObjectLoggerMapper;
use Mailery\Activity\Log\Service\ObjectLoggerService;

class ObjectLoggerFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;

    /**
     * @var ObjectLoggerService
     */
    private ObjectLoggerService $loggerService;

    /**
     * @param FactoryInterface $factory
     * @param ObjectLoggerService $loggerService
     */
    public function __construct(FactoryInterface $factory, ObjectLoggerService $loggerService)
    {
        $this->factory = $factory;
        $this->loggerService = $loggerService;
    }

    /**
     * @inheritdoc
     */
    public function database(string $database = null): DatabaseInterface
    {
        return $this->factory->database($database);
    }

    /**
     * @inheritdoc
     */
    public function loader(ORMInterface $orm, SchemaInterface $schema, string $role, string $relation): LoaderInterface
    {
        return $this->factory->loader($orm, $schema, $role, $relation);
    }

    /**
     * @inheritdoc
     */
    public function make(string $alias, array $parameters = [])
    {
        return $this->factory->make($alias, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function mapper(ORMInterface $orm, SchemaInterface $schema, string $role): MapperInterface
    {
        return new ObjectLoggerMapper(
            $orm,
            $this->factory->mapper($orm, $schema, $role),
            $this->loggerService->withOrm($orm)
        );
    }

    /**
     * @inheritdoc
     */
    public function relation(ORMInterface $orm, SchemaInterface $schema, string $role, string $relation): RelationInterface
    {
        return $this->factory->relation($orm, $schema, $role, $relation);
    }

    /**
     * @inheritdoc
     */
    public function repository(ORMInterface $orm, SchemaInterface $schema, string $role, ?Select $select): RepositoryInterface
    {
        return $this->factory->repository($orm, $schema, $role, $select);
    }

    /**
     * @inheritdoc
     */
    public function source(ORMInterface $orm, SchemaInterface $schema, string $role): SourceInterface
    {
        return $this->factory->source($orm, $schema, $role);
    }
}
