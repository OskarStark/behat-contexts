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

namespace Tests\OStark\Behat\Context\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use OStark\Behat\Context\Doctrine\DoctrineDatabaseContext;
use PHPUnit\Framework\TestCase;

/**
 * @group behat
 */
class DoctrineDatabaseContextTest extends TestCase
{
    private $classes;
    private $em;
    private $schemaTool;

    protected function setUp()
    {
        $this->classes = [new ClassMetadata(\stdClass::class)];

        $classMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $classMetadataFactory
            ->expects($this->once())
            ->method('getAllMetadata')
            ->willReturn($this->classes);

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em
            ->expects($this->once())
            ->method('getMetadataFactory')
            ->willReturn($classMetadataFactory);

        $this->schemaTool = $this->createMock(SchemaTool::class);
    }

    /**
     * @test
     */
    public function createSchema()
    {
        $this->schemaTool
            ->expects($this->once())
            ->method('createSchema')
            ->with($this->classes);

        $context = new DoctrineDatabaseContext($this->em, $this->schemaTool);
        $context->createSchema();
    }

    /**
     * @test
     */
    public function dropSchema()
    {
        $this->schemaTool
            ->expects($this->once())
            ->method('dropSchema')
            ->with($this->classes);

        $context = new DoctrineDatabaseContext($this->em, $this->schemaTool);
        $context->dropSchema();
    }

    /**
     * @test
     */
    public function emptyDatabase()
    {
        $this->schemaTool
            ->expects($this->once())
            ->method('dropSchema')
            ->with($this->classes);

        $this->schemaTool
            ->expects($this->once())
            ->method('createSchema')
            ->with($this->classes);

        $context = new DoctrineDatabaseContext($this->em, $this->schemaTool);
        $context->emptyDatabase();
    }
}
