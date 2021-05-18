<?php

declare(strict_types=1);

/**
 * @author Timo Förster <tfoerster@webfoersterei.de>
 * @date 06.03.18
 */

namespace Webfoersterei\Bundle\JsonParamConverterBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class WebfoerstereiJsonParamConverterExtension extends Extension
{
    public const EXTENSION_ALIAS = 'wf_jsoninputdto';

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('webfoersterei_jsonparamconverter.errorlist_param_converter');
        $definition->replaceArgument(2, (bool)$config[Configuration::CONFIG_THROW_EXCEPTION]);

        $definition = $container->getDefinition('webfoersterei_jsonparamconverter.errorlist_handler');
        $definition->replaceArgument(0, (int)$config[Configuration::CONFIG_CONVERT_EXCEPTION_HTTP_CODE]);
    }

    public function getAlias()
    {
        return self::EXTENSION_ALIAS;
    }
}
