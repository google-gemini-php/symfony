<?php

declare(strict_types=1);

namespace Gemini\Symfony\DependencyInjection;

use Gemini\Factory;
use InvalidArgumentException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @internal
 */
final class GeminiExtension extends Extension
{
    /**
     * @param  array<int, array<int, mixed>>  $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $configuration = $this->getConfiguration($configs, $container);

        assert($configuration instanceof ConfigurationInterface);

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(Factory::class);
        $definition->addMethodCall('withApiKey', [$config['api_key']]);

        if (isset($config['base_url']) && ! is_string($config['base_url'])) {
            throw new InvalidArgumentException('Invalid Gemini API base URL.');
        }

        if (! empty($config['base_url'])) {
            $definition->addMethodCall('withBaseUrl', [$config['base_url']]);
        }
    }
}
