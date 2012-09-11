<?php
/***************************************************************************
 * Copyright (C) 1999-2011 Gadz.org                                        *
 * http://opensource.gadz.org/                                             *
 *                                                                         *
 * This program is free software; you can redistribute it and/or modify    *
 * it under the terms of the GNU General Public License as published by    *
 * the Free Software Foundation; either version 2 of the License, or       *
 * (at your option) any later version.                                     *
 *                                                                         *
 * This program is distributed in the hope that it will be useful,         *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of          *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the            *
 * GNU General Public License for more details.                            *
 *                                                                         *
 * You should have received a copy of the GNU General Public License       *
 * along with this program; if not, write to the Free Software             *
 * Foundation, Inc.,                                                       *
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA                   *
 ***************************************************************************/

namespace Gorg\Bundle\CasBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * Create the factory for build security listner and provider Cas Authentication
 
 * @category Authentication
 * @package  GorgCasBundle
 * @author   Mathieu GOULIN <mathieu.goulin@gadz.org>
 * @license  GNU General Public License
 */
class CasFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        
        $provider = 'security.authentication.provider.pre_authenticated.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.pre_authenticated'))
            ->replaceArgument(0, new Reference($userProvider))
            ->addArgument($id)
        ;

        $listenerId = 'security.authentication.listener.cas.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('cas.security.authentication.listener'))
                    ->replaceArgument(2, $id)
	            ->replaceArgument(3, $config['cas_server'])
                    ->replaceArgument(4, $config['cas_port'])
                    ->replaceArgument(5, $config['cas_path'])
                    ->replaceArgument(6, $config['ca_cert_path'])
                    ->replaceArgument(7, $config['cas_protocol'])
                    ->replaceArgument(8, $config['cas_mapping_attribute']);

        return array($provider, $listenerId, $defaultEntryPoint);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'cas';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
		/* Load the configuration */
        $node
            ->children()
		->scalarNode('cas_server')->end()
		->variableNode('cas_port')->end()
		->scalarNode('cas_path')->end()
		->scalarNode('ca_cert_path')->end()
		->scalarNode('cas_protocol')->defaultValue('S1')->end() /* S1 for SAML_VERSION_1, 1.0 for CAS 1, 2.0 for CAS 2.0, See CAS.php for more information */
		->scalarNode('cas_mapping_attribute')->defaultValue("###CAS_USER_NAME###")->end() /* default value reprensent the username returned by cas (not an attribute) */
		->end()
			;
        ;
	}
}
/* vim:set et sw=4 sts=4 ts=4: */
