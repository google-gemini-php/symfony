<?php

declare(strict_types=1);

namespace Gemini\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('gemini');
        $rootNode = $treeBuilder->getRootNode();

        assert($rootNode instanceof ArrayNodeDefinition);

        $children = $rootNode->children();

        assert($children instanceof NodeBuilder);

        $children->scalarNode('api_key')->defaultValue('%env(GEMINI_API_KEY)%')->end();

        return $treeBuilder;
    }
}
