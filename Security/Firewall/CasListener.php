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

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener;

/**
 * Class for wait the authentication event and call the CAS Api to throw the authentication process
 *
 * @category Authentication
 * @package  GorgCasBundle
 * @author   Mathieu GOULIN <mathieu.goulin@gadz.org>
 * @license  GNU General Public License
 */
class CasListener extends AbstractPreAuthenticatedListener
{
    private   $cas_server;
    private   $cas_port;
    private   $cas_path;
    private   $ca_cert;
    private   $cas_protocol;
    private   $cas_mapping_attribute;
	
    /**
	 * Build a CasListener object
	 * @param SecurityContextInterface $securityContext
	 * @param Logger $logger the logger
	 * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, $cas_server, $cas_port, $cas_path, $ca_cert, $cas_protocol, $cas_mapping_attribute, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $providerKey, $logger, $dispatcher);

        $this->cas_server            = $cas_server;
        $this->cas_port              = $cas_port;
        $this->cas_path              = $cas_path;
        $this->ca_cert               = $ca_cert;
        $this->cas_protocol          = $cas_protocol;
        $this->cas_mapping_attribute = $cas_mapping_attribute;
    }

    protected function getPreAuthenticatedData(Request $request)
    {
 	/* Call CAS API to do authentication */
        require_once(dirname(__FILE__) . '/../../../../../../vendor/CAS/CAS.php');
        \phpCAS::client($this->cas_protocol, $this->cas_server, $this->cas_port, $this->cas_path, false);
	if($this->ca_cert)
	{
	        \phpCAS::setCasServerCACert($this->ca_cert);
	} else {
		\phpCAS::setNoCasServerValidation();
	}
        \phpCAS::forceAuthentication();
	if($this->cas_mapping_attribute) {
	    $attributes = \phpCAS::getAttributes();

            if (!$attributes[$this->cas_mapping_attribute]) {
                return;
            }
	    return array($attributes[$this->cas_mapping_attribute], array('ROLE_USER'));
        } else {
	    return array($attributes[\phpCAS::getUser()], array('ROLE_USER'));
	}
	return;
    }
}

/* vim:set et sw=4 sts=4 ts=4: */
