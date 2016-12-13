<?php

require_once('bVirtualmin/virtualmin_api.php');
require_once('bVirtualmin/bean_utils.php');

abstract class Virtualserver {

    static public function sync_all_virtualservers() {
        foreach(VirtualminAPI::get_virtualmin_servers() as $server) {
            self::sync_all_virtualservers_of_virtualmin($server);
        }
    }

    static public function sync_all_virtualservers_of_virtualmin($virtualmin_server) {
        $virtualservers = VirtualminAPI::get_virtualservers_of_virtualmin($virtualmin_server);
        foreach($virtualservers as $virtualserver) {
            self::sync_virtualserver($virtualserver);
        }
    }

    static public function sync_virtualserver($virtualserver) {
        $keys_values = array();
        $keys_values['name'] = $virtualserver->virtualserver;
        $bean = retrieve_record_bean('btc_Hosting', $keys_values);
        $bean->name = $virtualserver->virtualserver;
        $bean->description = $virtualserver->description;
        //$bean->fecha_creacion = $virtualserver->created_on;
        $bean->activo = isset($virtualserver->disabled) ? '0' : '1';
        $bean->estado_host = 'Vigente';
        $bean->disabled_description = isset($virtualserver->disabled_description) ?
                $virtualserver->disabled_description : '';
        $bean->used_quota = $virtualserver->used_quota;
        $bean->databases_size = $virtualserver->databases_size;
        $bean->quota = $virtualserver->quota;
        $bean->save();
    }

}

?>
