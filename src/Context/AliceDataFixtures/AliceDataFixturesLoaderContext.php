<?php

declare(strict_types=1);

namespace OStark\Behat\Context\AliceDataFixtures;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\Yaml\Yaml;

class AliceDataFixturesLoaderContext implements Context
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $fixturesBasePath;

    public function __construct(LoaderInterface $loader, string $fixturesBasePath)
    {
        $this->loader = $loader;
        $this->fixturesBasePath = $fixturesBasePath;
    }

    /**
     * @Given the following YAML fixtures where loaded:
     */
    public function yamlString(PyStringNode $fixturesYaml): void
    {
        // validate
        Yaml::parse($fixturesYaml->getRaw());

        file_put_contents(
            $filepath = sprintf(
                '%s/%s.yaml',
                sys_get_temp_dir(),
                uniqid('yaml_fixtures')
            ),
            $fixturesYaml->getRaw()
        );

        $this->loader->load([$filepath]);
    }

    /**
     * @Given the fixtures :fixturesFile are loaded
     * @Given the fixtures file :fixturesFile is loaded
     *
     * @param string $fixturesFile Path to the fixtures
     */
    public function thereAreFixtures($fixturesFile): void
    {
        $this->loader->load([$this->fixturesBasePath.$fixturesFile]);
    }

    /**
     * @Given the following fixtures are loaded:
     * @Given the following fixtures files are loaded:
     * @Given /^the following fixtures are loaded using the (append|delete|truncate) purger:$/
     *
     * @param TableNode $fixtures Path to the fixtures
     */
    public function thereAreSeveralFixtures(TableNode $fixtures, $purgeMode = null): void
    {
        $fixturesFiles = [];

        foreach ($fixtures->getRows() as $fixturesFileRow) {
            $fixturesFiles[] = $this->fixturesBasePath.$fixturesFileRow[0];
        }

        switch ((string) $purgeMode) {
            case 'append':
                $purgeMode = PurgeMode::createNoPurgeMode();
                break;
            case 'truncate':
                $purgeMode = PurgeMode::createTruncateMode();
                break;
            case 'delete':
            case '':
                $purgeMode = PurgeMode::createDeleteMode();
                break;
            default:
                throw new \RuntimeException('Invalid purge mode');
        }

        $this->loader->load($fixturesFiles, $parameters = [], $objects = [], $purgeMode);
    }
}
