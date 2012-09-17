README FILE
===========

1) Introduction
---------------
This file will help you to install and configure the project.

2) Installation
---------------

### a) Add the bundle to your composer.json file at the project root

    "require": {
        ...
        "gorg/casbundle": "master"
    },
    "repositories": {
        "gorg": {
            "type": "package",
            "package": {
                "name": "gorg/casbundle",
                "version": "master",
                "source": {
                    "url": "ssh://git@gofannon.gorgu.net:7999/GRAM/gorgcasbundle.git",
                    "type": "git",
                    "reference": "master"
                }
            }
        }
    },

### b) Parameters Symfony sandbox for use GorgCasBundle

Please edit app/AppKernel.php file to update symfony Kernel

        $bundles = array(
            /*
             ...
            */
            new Gorg\Bundle\CasBundle\GorgCasBundle(),
        );

### b) Parameters Symfony sandbox for use GorgCasBundle

Please edit app/config/config.yml and add the following line

    gorg_cas:
        user_class: Gorg\Bundle\UserBundle\Entity\User
        mapping_role_attribute: type
        mapping_username_attribute: username

### c) Parameters Symfony sandbox for use Gorg CAS Authentication

Please edit app/config/security.yml file to update symfony security policy

        # ...
        # If you want you can use a custom user provider
        # ...
        providers:
            CAS:
                id: cas.user_provider
	# ...
	    firewalls:
	        dev:
	            pattern:  ^/(_(profiler|wdt)|css|images|js)/
	            security: false
	
	        secured_area:
	            pattern:    ^/demo/secured/
                    cas:
                        cas_server: auth.gadz.org
                        cas_port: 443
                        cas_path: /cas/
                        ca_cert_path: ~
                        cas_protocol: S1
                        cas_mapping_attribute: username

