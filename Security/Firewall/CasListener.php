<?php
/***************************************************************************
 * Copyright (C) 1999-2012 Gadz.org                                        *
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
namespace Gorg\Bundle\CasBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

/**
 * Class for wait the authentication event and call the CAS Api to throw the authentication process
 *
 * @category Authentication
 * @package  GorgCasBundle
 * @author   Mathieu GOULIN <mathieu.goulin@gadz.org>
 * @license  GNU General Public License
 */
class CasListener extends AbstractAuthenticationListener
{
    /**
     * {@inheritdoc}
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, $options, $logger, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        /* Call CAS API to do authentication */
        require_once(dirname(__FILE__) . '/../../../../../../phpcas/CAS.php');

        \phpCAS::client($this->options['cas_protocol'], $this->options['cas_server'], $this->options['cas_port'], $this->options['cas_path'], false);

        if($this->options['ca_cert_path'])
        {
            \phpCAS::setCasServerCACert($this->options['ca_cert_path']);
	} else {
            \phpCAS::setNoCasServerValidation();
	}
        \phpCAS::forceAuthentication();
        $attributes = \phpCAS::getAttributes();

        if($this->options['cas_mapping_attribute']) {
            if (!$attributes[$this->options['cas_mapping_attribute']]) {
                return;
            }
            $user = $attributes[$this->options['cas_mapping_attribute']];
            $credentials = array('ROLE_USER');
        } else {
            $user = $attributes[\phpCAS::getUser()];
            $credentials = array('ROLE_USER');
        }

        if (null !== $this->logger) {
            $this->logger->info(sprintf('Authentication success: %s', $user));
        }

        $newToken = new PreAuthenticatedToken($user, $credentials, %this->providerKey);
        $newToken->setAttributes($attributes);

        return $this->authenticationManager->authenticate($newToken);
    }
}
