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
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
