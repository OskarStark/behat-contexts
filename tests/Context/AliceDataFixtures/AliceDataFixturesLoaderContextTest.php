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

namespace Tests\OStark\Behat\Context\AliceDataFixtures;

use Behat\Gherkin\Node\TableNode;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use OStark\Behat\Context\AliceDataFixtures\AliceDataFixturesLoaderContext;
use PHPUnit\Framework\TestCase;

/**
 * @group behat
 */
class AliceDataFixturesLoaderContextTest extends TestCase
{
    private $loader;
    private $fixturesBasePath;

    protected function setUp()
    {
        $this->loader = $this->createMock(LoaderInterface::class);
        $this->fixturesBasePath = 'features/fixtures/';
    }

    /**
     * @test
     */
    public function thereAreFixtures()
    {
        $filename = 'file.yaml';

        $this->loader
            ->expects($this->once())
            ->method('load')
            ->with(['features/fixtures/file.yaml']);

        $context = new AliceDataFixturesLoaderContext($this->loader, $this->fixturesBasePath);
        $context->thereAreFixtures($filename);
    }

    /**
     * @test
     *
     * @dataProvider validPurgeModeProvider
     */
    public function thereAreSeveralFixtures($expected, $mode)
    {
        $files = [
            ['file.yaml'],
            ['file1.yaml'],
        ];

        $this->loader
            ->expects($this->once())
            ->method('load')
            ->with(
                [
                    'features/fixtures/file.yaml',
                    'features/fixtures/file1.yaml',
                ],
                [],
                [],
                $expected
            );

        $context = new AliceDataFixturesLoaderContext($this->loader, $this->fixturesBasePath);
        $context->thereAreSeveralFixtures(new TableNode($files), $mode);
    }

    /**
     * @return array
     */
    public function validPurgeModeProvider()
    {
        return [
            [PurgeMode::createNoPurgeMode(), 'append'],
            [PurgeMode::createTruncateMode(), 'truncate'],
            [PurgeMode::createDeleteMode(), 'delete'],
            [PurgeMode::createDeleteMode(), ''],
            [PurgeMode::createDeleteMode(), null],
        ];
    }

    /**
     * @test
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid purge mode
     */
    public function invalidPurgeMode()
    {
        $files = [
            ['file.yaml'],
            ['file1.yaml'],
        ];

        $context = new AliceDataFixturesLoaderContext($this->loader, $this->fixturesBasePath);
        $context->thereAreSeveralFixtures(new TableNode($files), 'FOOO');
    }
}
