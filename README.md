# Doctrine coding standard plugin for phpcq

This plugin integrates the [Doctrine coding standard](https://www.doctrine-project.org/projects/coding-standard.html) into phpcq.

## Configuration

Extend your `.phpcq.yaml.dist` configuration by adding the plugin and configuring the task:

```yaml
phpcq:
  plugins:
    doctrine-coding-standard:
      version: ^1.0
      signed: false

tasks:
  phpcs:
    uses:
      doctrine-coding-standard:
        # Optional define how standard option of phpcs configuration should be handled
        # Valid options are override, extend or ignore. It defaults to override
        phpcs_standard: extend
```
