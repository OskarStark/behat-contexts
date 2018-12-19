# AliceDataFixturesLoaderContext

## Usage
Enable the context in your `behat.yml.dist`:

```yaml
default:
    suites:
        default:
            contexts:
                - App\Behat\Context\AliceDataFixtures\AliceDataFixturesLoaderContext:
                    loader: '@fidry_alice_data_fixtures.loader.doctrine'
                    fixturesBasePath: '%paths.base%/features/fixtures/'

                # make sure to enable MinkContext!
                - Behat\MinkExtension\Context\MinkContext
```

## Available steps

```gherkin
Given the following YAML fixtures where loaded:
    """
    App\Entity\Category:
        category_1:
            name: 'Foo'
    """
```

```gherkin
Given the fixtures "categories.yaml" are loaded

Given the fixtures file "categories.yaml" is loaded
```

```gherkin
Given the following fixtures are loaded:
    | categories.yaml |
    | users.yaml      |
    
Given the following fixtures files are loaded:
    | categories.yaml |
    | users.yaml      |    

Given the following fixtures are loaded using the append purger:
    | categories.yaml |
    | users.yaml      |
    
Given the following fixtures are loaded using the delete purger:
    | categories.yaml |
    | users.yaml      |    
    
Given the following fixtures are loaded using the truncate purger:
    | categories.yaml |
    | users.yaml      |        
```
