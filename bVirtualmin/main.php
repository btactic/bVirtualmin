<?php

require_once('bVirtualmin/virtualserver.php');

function main() {
    $GLOBALS['log']->fatal("[bVirtualmin] Entering bVirtualmin synchronization.");
    Virtualserver::sync_all_virtualservers();
    $GLOBALS['log']->fatal("[bVirtualmin] bVirtualmin synchronization finished.");
    return true;
}

?>
