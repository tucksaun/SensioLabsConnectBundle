<?php

/*
 * This file is part of the SensioLabsConnectBundle package.
 *
 * (c) SensioLabs <contact@sensiolabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\Bundle\ConnectBundle\DependencyInjection;

use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use SensioLabs\Bundle\ConnectBundle\DependencyInjection\Security\Factory\ConnectFactory;
use SensioLabs\Bundle\ConnectBundle\DependencyInjection\Security\UserProvider\ConnectInMemoryFactory;

/**
 * SensioLabsConnectExtension.
 *
 * @author Marc Weistroff <marc.weistroff@sensiolabs.com>
 */
class SensioLabsConnectExtension extends Extension
{
    private $securityExtension;

    public function setSecurityExtension(SecurityExtension $extension)
    {
        $this->securityExtension = $extension;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('connect.xml');
        if ($config['enable_security']) {
            if (!$this->securityExtension) {
                throw new \LogicException("SecurityBundle does not seem registered.");
            }

            $loader->load('security.xml');
            $this->securityExtension->addSecurityListenerFactory(new ConnectFactory());
            $this->securityExtension->addUserProviderFactory(new ConnectInMemoryFactory());
        }

        $container->getDefinition('sensiolabs_connect.oauth_consumer')
            ->replaceArgument(0, $config['app_id'])
            ->replaceArgument(1, $config['app_secret'])
            ->replaceArgument(2, $config['scope'])
            ->replaceArgument(3, $config['oauth_endpoint'])
        ;

        $container->getDefinition('sensiolabs_connect.oauth_consumer')
            ->addMethodCall('setStrictChecks', array($config['strict_checks']))
        ;

        $container->getDefinition('sensiolabs_connect.api')
            ->replaceArgument(0, $config['api_endpoint'])
        ;

        $container->setParameter('sensiolabs_connect.oauth.session_callback_path', $config['oauth_callback_path']);
        $container->setParameter('sensiolabs_connect.api.app_id', $config['app_id']);
        $container->setParameter('sensiolabs_connect.api.app_secret', $config['app_secret']);

        $container->getDefinition('sensiolabs_connect.buzz.client')
            ->addMethodCall('setTimeout', array($config['timeout']))
        ;
    }
}
