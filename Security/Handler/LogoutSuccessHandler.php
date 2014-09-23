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
namespace Gorg\Bundle\CasBundle\Security\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    public function onLogoutSuccess(Request $request)
    {
        /* Call CAS API to do authentication */
        require_once(dirname(__FILE__) . '/../../../../../../phpcas/CAS.php');

        \phpCAS::client($this->options['cas_protocol'], $this->options['cas_server'], $this->options['cas_port'], $this->options['cas_path'], false);   

        \phpCAS::logoutWithRedirectService($request->getScheme().'://'.$request->getHttpHost());
        return null;
    }
}
