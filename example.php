<?php

ini_set( 'memory_limit', -1 );

require("phpols.php");


try {
    $phpOLS = new phpOLS( "binaryFile", "maps.csv" );
    $phpOLS->getMapById( "PQAV" )->display();
    $phpOLS->getMapById( "PQAV" )->Change(0,0,500)->display();
}
catch ( exception $e ) {

}
