<?php

require_once('bVirtualmin/virtualserver.php');

function main() {
    $GLOBALS['log']->fatal("[bVirtualmin] Entering bVirtualmin synchronization.");
    Virtualserver::set_all_hosts_to_disabled();
    Virtualserver::sync_all_virtualservers();
    $GLOBALS['log']->fatal("[bVirtualmin] bVirtualmin synchronization finished.");
    return true;
}

?>
