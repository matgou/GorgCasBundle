README FILE
===========

1) Introduction
---------------
This file will help you to install and configure the project.

2) Installation
---------------

### a) Clone the repository to your symfony2 installation

create and jump to the src/Gorg/Bundle/ directory and run the following commands:

	git clone https://git.gadz.org/GorgCasBundle.git CasBundle

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

        open_alumni_socle_cas:
            user_class: Gorg\Bundle\CasBundle\Entity\User
            mapping_role_attribute: type
            mapping_username_attribute: username


### c) Parameters Symfony sandbox for use Gorg CAS Authentication

Please edit app/config/security.yml file to update symfony security policy

	security:
	    factories:
	        - "%kernel.root_dir%/../src/Gorg/Bundle/CasBundle/Resources/config/security_factories.xml"
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

