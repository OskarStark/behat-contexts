<?php

declare(strict_types=1);

namespace OStark\Behat\Context\Doctrine;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Webmozart\Assert\Assert;

class DoctrineDatabaseContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var ClassMetadata[]
     */
    private $classes;

    public function __construct(EntityManagerInterface $em, SchemaTool $schemaTool)
    {
        $this->em = $em;
        $this->schemaTool = $schemaTool;
        $this->classes = $this->em->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createSchema()
    {
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @BeforeScenario @dropSchema
     */
    public function dropSchema()
    {
        $this->schemaTool->dropSchema($this->classes);
    }

    /**
     * @Given the database is empty
     * @Given I empty the database
     */
    public function emptyDatabase()
    {
        $this->dropSchema();
        $this->createSchema();
    }

    /**
     * @Then I should have :count :class entities
     */
    public function iShouldHaveEntities($count, $class)
    {
        Assert::classExists($class);

        $query = $this->em->createQuery(sprintf('SELECT COUNT(r.id) FROM %s r', $class));

        Assert::eq($count, $query->getSingleScalarResult());
    }
}
