<?php

namespace Gorg\Bundle\CasBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Gorg\Bundle\CasBundle\DependencyInjection\Security\Factory\CasFactory;

class GorgCasBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new CasFactory());
    }
}
