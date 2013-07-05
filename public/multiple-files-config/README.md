Using Respect\Validation on Forms

=================================

General Instructions on [the main README.md](https://github.com/Respect/samples/blob/master/README.md)

About
-----

This sample describes a common pattern for managing application config using
mulitple .ini files and Respect\Config.

Respect\Config is able to configure not only values and flags but entire object
instances. It is designed to provide really flexible dependency injection.

Respect\Rest and Respect\Relational are used on this sample to show how declaring
dependencies work, but Respect\Config works with projects outside the Respect
umbrella as well. See [the Respect\ed repo](https://github.com/Respect/ed/tree/master/config) 
for samples on how to configure Doctrine, Twig, etc. 

The [ConfigSymfony2HttpKernel sample](https://github.com/alganet/ConfigDoctrineSymfony2HttpKernel)
also shows a proof of concept of the Symfony 2 HttpKernel dependency injection.

How It Works
------------

The `index.php` file loads a first container with the configuration loaded by `start.ini`. The start
file is a clean way to define which other config files are loaded and their order in a decoupled way.

Once loaded, the Container from `start.ini` exposes a single configured instance (the application), 
which is another Container. Respect\Config can configure itself.

The application container loads then all other config files in order. Respect\Config is lazy and only
creates instances as you retrieve them.

globals.ini
-----------

The `globals.ini` holds a sample on how to configure basic aspects of an application runtime. It does
not declare any instances, just standard settings to be used in another files.

mapper.ini
----------

The sample does not use a relational database. This file only demonstrates how to load PDO and Respect\Relational 
using Respect\Config. It uses the connection_dsn from globals.ini. 

router.ini
----------

This sample uses Respect\Rest to display the dumps and also configures all the routing aspects from the
component using Respect\Config. 

In the file, requests are routed to a `Dumper` class which is declared in `index.php`. You can switch 
those routes to point to another classes in your application.

Experimenting
-------------

Instead of running the sample with `php -S localhost:80` try changing the port and configuring it in the
globals.ini file.