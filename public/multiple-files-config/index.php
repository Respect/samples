<?php

// Using multiple Respect\Config files

// Additional comments are available after the 80th column of text per line.

require '../../vendor/autoload.php';                                            // Load PHP files from Composer

use Respect\Config\Container;                                                   // We're gonna use this Container to load configuraton
use Respect\Rest\Routable;                                                      // We need this interface to bind classes to Respect\Rest

chdir(__DIR__);                                                                 // Makes sure we're on the right folder

class Dumper implements Routable                                                // A class that dumps things. See router.ini
{
    public $toBeDamped;                                                         // Property which holds the data to be dumped.

    public function __construct()
    {
        $this->toBeDamped = func_get_args();
    }

    public function get() 
    {
        return print_r($this->toBeDamped, true);
    }
}

$config      = new Container('config/start.ini');                               // Loads the configuration to rule them all
$application = $config->application;                                            // Then loads the properly ordered configured application

$dump = $application->router->run();                                            // Retrieve the Router from the application and run it!

?><!doctype html>
<title>Using multiple Respect\Config files</title>
<h1>Using multiple Respect\Config files</h1>
<p>
    Below is a <code>print_r</code> dump of some configured
    instances and values in a single container managed
    by <a href="http://github.com/Respect/Config">Respect\Config</a>.
</p>
<nav>
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/var-dumps/mapper">Mapper</a></li>
    <li><a href="/var-dumps/base">App Base</a></li>
  </ul>
</nav>
<pre><?php echo $dump ?></pre>