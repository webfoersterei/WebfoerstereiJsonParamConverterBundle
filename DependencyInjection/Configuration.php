<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 18.05.21
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CONFIG_THROW_EXCEPTION = 'exception_on_constraint_violation';
    public const CONFIG_CONVERT_EXCEPTION_HTTP_CODE = 'convert_exception_to_http_code';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(WebfoerstereiJsonParamConverterExtension::EXTENSION_ALIAS);

        $treeBuilder->getRootNode()
                    ->children()
                        ->booleanNode(self::CONFIG_THROW_EXCEPTION)->defaultFalse()->end()
                        ->scalarNode(self::CONFIG_CONVERT_EXCEPTION_HTTP_CODE)->end()
                    ->end()
                    ->end();

        return $treeBuilder;
    }
}
