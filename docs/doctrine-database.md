# DoctrineDatabaseContext

## Usage
Enable the context in your `behat.yml.dist`:

```yaml
default:
    suites:
        default:
            contexts:
                - OStark\Behat\Context\Doctrine\DoctrineDatabaseContext:
                    em: '@doctrine.orm.default_entity_manager'
                    schemaTool: '@Doctrine\ORM\Tools\SchemaTool'
```

## Available steps

```gherkin
Given the database is empty     

Given I empty the database
```

```gherkin
Then I should have 2 App\Entity\Category entities
```
