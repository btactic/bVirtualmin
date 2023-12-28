<?php

abstract class VirtualminAPI {

    private static $virtualmin_servers_config_file = 'bVirtualmin/config/virtualmin_servers.ini';

    static public function get_virtualservers_of_virtualmin($virtualmin_server_definition) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $virtualmin_server_definition['host'] . ':' . $virtualmin_server_definition['port'] . '/virtual-server/remote.cgi?program=list-domains&multiline&json=1');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $virtualmin_server_definition['user'] . ":" . $virtualmin_server_definition['pass']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $virtualmin_json_txt = curl_exec($ch);
        curl_close($ch);

        $virtualmin_json = json_decode($virtualmin_json_txt);

        $virtualservers = array();

        if (!(is_null($virtualmin_json))) {

            $virtualmin_json_data = $virtualmin_json->data;

            foreach ($virtualmin_json_data as $virtualmin_server) {
                $virtualserver = array();

                // Default empty values
                $virtualserver["virtualserver"] = "";
                $virtualserver["description"] = "";
                $virtualserver["created_on"] = "";
                $virtualserver["ip"] = "";
                $virtualserver["quota"] = "";
                $virtualserver["used_quota"] = "";
                $virtualserver["databases_size"] = "";

                if (isset($virtualmin_server->name)) {
                    $virtualserver["virtualserver"] = $virtualmin_server->name;
                }
                if (isset($virtualmin_server->values->description)) {
                    $virtualserver["description"] = $virtualmin_server->values->description[0];
                }
                if (isset($virtualmin_server->values->created_on)) {
                    $virtualserver["created_on"] = $virtualmin_server->values->created_on[0];
                }
                if (isset($virtualmin_server->values->ip_address)) {
                    $virtualserver_ip = $virtualmin_server->values->ip_address[0];
                    $virtualserver_ip_exploded = explode(" ", $virtualserver_ip);
                    $virtualserver["ip"] = $virtualserver_ip_exploded[0];
                }
                if (isset($virtualmin_server->values->server_quota)) {
                    $virtualserver["quota"] = $virtualmin_server->values->server_quota[0];
                }
                if (isset($virtualmin_server->values->server_quota_used)) {
                    $virtualserver["used_quota"] = $virtualmin_server->values->server_quota_used[0];
                }
                if (isset($virtualmin_server->values->databases_size)) {
                    $virtualserver["databases_size"] = $virtualmin_server->values->databases_size[0];
                }

                $virtualservers[] = $virtualserver;

            }
        }

        return $virtualservers;
    }

    static public function get_virtualmin_ips($virtualmin_server_definition) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $virtualmin_server_definition['host'] . ':' . $virtualmin_server_definition['port'] . '/virtual-server/remote.cgi?program=list-shared-addresses&name-only&json=1');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $virtualmin_server_definition['user'] . ":" . $virtualmin_server_definition['pass']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $virtualmin_json_txt = curl_exec($ch);
        curl_close($ch);

        $virtualmin_json = json_decode($virtualmin_json_txt);

        $ip = '';

        if (!(is_null($virtualmin_json))) {
            $ip_data = $virtualmin_json->data;
            $ip_object = $ip_data[0];
            $ip = $ip_object->name;
        }

        return($ip);
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
