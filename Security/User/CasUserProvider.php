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
namespace Gorg\Bundle\CasBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\ProviderNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

use Gorg\Bundle\CasBundle\Entity\User;

/**
 * Class for retreive user object from cas-information
 *
 * @category Authentication
 * @package  GorgCasBundle
 * @author   Mathieu GOULIN <mathieu.goulin@gadz.org>
 * @license  GNU General Public License
 */
class CasUserProvider implements UserProviderInterface
{
    private $entityManager;
    private $logger;
    private $casMappingRoleAttribute;
    private $casMappingUsernameAttribute;
    private $userClass;

    /**
     * Return the user from cas
     * @param string $username the name of the user searched
     *
     * @return User the user returned by the CAS
     */
    public function loadUserByUsername($username) 
    {
        $userRepo = $this->entityManager->getRepository($this->userClass);
        $user = $userRepo->findOneByUsername($username);

	if(class_exists('\phpCAS'))
        {
            if($this->casMappingUsernameAttribute) {
                $attributes = \phpCAS::getAttributes();
                if (!$attributes[$this->casMappingUsernameAttribute]) {
                    throw new UsernameNotFoundException();
                }
		if($user == null)
		{
                    $user = new $this->userClass($username, $attributes, $this->casMappingRoleAttribute, $this->casMappingUsernameAttribute, $attributes);
                } else {
                    $user->setAttributes($attributes);
                }
            } else {
                if(\phpCAS::getUser() != $username) {
                    throw new ProviderNotFoundException(sprintf("You can't retrive other user via this provider."));
                }
                if($user == null)
                {
                    $user = new $this->userClass($username, $attributes, $this->casMappingRoleAttribute);
                } else {
                    $user->setAttributes($attributes);
                }
            }
        }
        $this->entityManager->persist($user);
	$this->entityManager->flush();
       
	return $user;
    }

    /**
     * Fetch informations from a user
     * @param UserInterface $user the user
     *
     * @return User the user to load
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }
 
    /**
     * Test if a class could be loaded from this User Provider
     * @param string $class the name of the class to test
     *
     * @return boolean true if the class is suported
     */
    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }

    /**
     * Cas Provider constructor.
     * @param type $entityManager the associated entity manager
     * @param type $logger the logger
     * @param type $securityEncoderFactory the encoder factory
     * @param String $userClass the class for user
     * @param String $casMappingRoleAttribute the attribute name for user role
     * @param String $casMappingUsernameAttribute the attribute name for username 
     */
    public function __construct($entityManager, $logger, $securityEncoderFactory, $userClass, $casMappingRoleAttribute, $casMappingUsernameAttribute = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->securityEncoderFactory = $securityEncoderFactory;
        $this->casMappingUsernameAttribute = $casMappingUsernameAttribute;
        $this->casMappingRoleAttribute = $casMappingRoleAttribute;
        $this->userClass = $userClass;
    }
 
}
/* vim:set et sw=4 sts=4 ts=4: */
