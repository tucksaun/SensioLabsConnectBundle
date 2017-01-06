<?php

/*
 * This file is part of the SensioLabsConnectBundle package.
 *
 * (c) SensioLabs <contact@sensiolabs.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\Bundle\ConnectBundle;

use SensioLabs\Bundle\ConnectBundle\DependencyInjection\CompilerPass\ApiPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * SensioLabsConnectBundle
 *
 * @author Marc Weistroff <marc.weistroff@sensiolabs.com>
 */
class SensioLabsConnectBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        if ($container->hasExtension('security')) {
            $container->getExtension('sensio_labs_connect')->setSecurityExtension($container->getExtension('security'));
        }
        $container->addCompilerPass(new ApiPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
