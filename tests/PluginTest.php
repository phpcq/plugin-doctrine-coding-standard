<?php

declare(strict_types=1);

namespace Phpcq\CodingStandardPluginTest;

use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationBuilderInterface;
use Phpcq\PluginApi\Version10\Configuration\PluginConfigurationInterface;
use Phpcq\PluginApi\Version10\EnricherPluginInterface;
use Phpcq\PluginApi\Version10\EnvironmentInterface;
use Phpcq\PluginApi\Version10\ProjectConfigInterface;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
final class PluginTest extends TestCase
{
    public function testDescribeConfiguration(): void
    {
        $plugin  = $this->loadPlugin();
        $builder = $this->getMockForAbstractClass(PluginConfigurationBuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('describeEnumOption')
            ->with(
                'phpcs_standard',
                'Activates the doctrine coding standard. Otherwise only the coding standard is registered.'
            );

        $plugin->describeConfiguration($builder);
    }

    public function testPhpcsEnrichment(): void
    {
        $plugin        = $this->loadPlugin();
        $configuration = $this->getMockForAbstractClass(PluginConfigurationInterface::class);
        $configuration
            ->expects($this->once())
            ->method('getString')
            ->with('phpcs_standard')
            ->willReturn('override');

        $environment   = $this->getMockForAbstractClass(EnvironmentInterface::class);

        $projectConfiguration = $this->getMockForAbstractClass(ProjectConfigInterface::class);
        $projectConfiguration
            ->expects($this->once())
            ->method('getProjectRootPath')
            ->willReturn('/installed');

        $environment
            ->expects($this->once())
            ->method('getProjectConfiguration')
            ->willReturn($projectConfiguration);

        $environment
            ->expects($this->once())
            ->method('getInstalledDir')
            ->willReturn('/installed/.phpcq/coding-standard');

        $configuration = $plugin->enrich('phpcs', '1.0.0', [], $configuration, $environment);

        self::assertEquals(
            [
                'standard_paths' => [
                    '.phpcq/coding-standard/vendor/slevomat/coding-standard',
                    '.phpcq/coding-standard/vendor/doctrine/coding-standard/lib'
                ],
                'standard'       => 'Doctrine',
                'autoload_paths' => [
                    '.phpcq/coding-standard/vendor/autoload.php'
                ]
            ],
            $configuration
        );
    }

    public function testConfiguredPhpcsEnrichment(): void
    {
        $plugin        = $this->loadPlugin();
        $configuration = $this->getMockForAbstractClass(PluginConfigurationInterface::class);
        $configuration
            ->expects($this->once())
            ->method('getString')
            ->with('phpcs_standard')
            ->willReturn('ignore');

        $environment   = $this->getMockForAbstractClass(EnvironmentInterface::class);

        $projectConfiguration = $this->getMockForAbstractClass(ProjectConfigInterface::class);
        $projectConfiguration
            ->expects($this->once())
            ->method('getProjectRootPath')
            ->willReturn('/installed');

        $environment
            ->expects($this->once())
            ->method('getProjectConfiguration')
            ->willReturn($projectConfiguration);

        $environment
            ->expects($this->once())
            ->method('getInstalledDir')
            ->willReturn('/installed/.phpcq/coding-standard');

        $configuration = $plugin->enrich('phpcs', '1.0.0', [], $configuration, $environment);

        self::assertSame(
            [
                'standard_paths' => [
                    '.phpcq/coding-standard/vendor/slevomat/coding-standard',
                    '.phpcq/coding-standard/vendor/doctrine/coding-standard/lib'
                ],
                'autoload_paths' => [
                    '.phpcq/coding-standard/vendor/autoload.php'
                ]
            ],
            $configuration
        );
    }

    private function loadPlugin(): EnricherPluginInterface
    {
        $plugin = require __DIR__ . '/../src/plugin.php';
        self::assertInstanceOf(EnricherPluginInterface::class, $plugin);

        return $plugin;
    }
}
