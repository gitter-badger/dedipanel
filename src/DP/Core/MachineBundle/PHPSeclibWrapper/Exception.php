<?php
namespace PHPSeclibWrapper\Exception;
use DP\Core\MachineBundle\PHPSeclibWrapper\PHPSeclibWrapper;

class FileNotFoundException extends \Exception
{
    public function __construct($message, PHPSeclibWrapper $srv)
    {
        parent::__construct('The private key file for ' . $srv->getUser() . '@' .
            $srv->getHost() . ' ' . $message);
    }  
}
class EmptyKeyfileException extends \Exception
{
    public function __construct(PHPSeclibWrapper $srv)
    {
        parent::__construct('The private key file for ' . $srv->getUser() . '@' .
             $srv->getHost() . ':' . $srv->getPort() . ' is empty.');
    }
}

class ConnectionErrorException extends \Exception
{
    public function __construct(PHPSeclibWrapper $srv)
    {
        parent::__construct('Connection to ' . $srv->getUser() . '@' . 
            $srv->getHost() .':' . $srv->getPort() . ' failed.');
    }
}

class IncompleteLoginIDException extends \Exception
{
    public function __construct(PHPSeclibWrapper $srv)
    {
        parent::__construct('Incomplete login IDs for ' . $srv->getUser() . 
            '@' . $srv->getHost() . ':' . $srv->getPort());
    }
}
?>