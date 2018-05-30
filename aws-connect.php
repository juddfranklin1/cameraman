<?php
require 'vendor/autoload.php';

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class S3Connection {
    public $connection;
    
    function __construct(){
        $this->connection = new Aws\S3\S3Client([
            'profile' => 'default',
            'version' => 'latest',
            'region'  => 'us-west-1',
            // If you omit the following line, credentials are looked for in the ~/.aws/credentials file.
            'credentials' => CredentialProvider::env()
        ]);
    }    
}



