<?php

declare(strict_types=1);

/*
 * This file is part of the behat-contexts package.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @Then /^I should have ([^"]*) "([^"]*)" entities$/
     */
    public function iShouldHaveEntities($count, $class)
    {
        Assert::classExists($class);

        $query = $this->em->createQuery(sprintf('SELECT COUNT(r.id) FROM %s r', $class));

        Assert::eq($count, $query->getSingleScalarResult());
    }
}
