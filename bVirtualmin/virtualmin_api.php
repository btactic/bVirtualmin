<?php

abstract class VirtualminAPI {

    private static $get_virtualservers_script = 'bVirtualmin/scripts/get_virtualservers.sh';
    private static $virtualmin_servers_config_file = 'bVirtualmin/config/virtualmin_servers.ini';

    static public function get_virtualservers_of_virtualmin($virtualmin_server) {
        $virtualservers_info = self::execute_command_on_server($virtualmin_server,
                "$(cat ".self::$get_virtualservers_script.")");
        $virtualservers = array();
        foreach ($virtualservers_info as $virtualserver) {
            $virtualserver = json_decode($virtualserver);
            if (!empty($virtualserver)) $virtualservers[] = $virtualserver;
        }
        return $virtualservers;
    }

    static private function execute_command_on_server($virtualmin_server, $cmd) {
        exec("ssh -p ".$virtualmin_server['port']." ".$virtualmin_server['user']
                ."@".$virtualmin_server['host']." \"".$cmd."\"", $result);
        return $result;
    }

    static public function get_virtualmin_servers() {
        if (file_exists(self::$virtualmin_servers_config_file)) {
            return parse_ini_file(self::$virtualmin_servers_config_file, true);
        } else {
            $GLOBALS['log']->fatal("[bVirtualmin] Impossible to access '$config_file'.");
        }
    }

}

?>
