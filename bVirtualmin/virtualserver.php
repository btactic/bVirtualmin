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
        $bean = self::get_virtualserver_bean($virtualserver->virtualserver);
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
        self::relate_virtualserver_with_ip($bean, $virtualserver->ip);
        self::relate_virtualserver_with_vm($bean, $virtualserver->ip);
    }

    static private function relate_virtualserver_with_ip($virtualserver_bean, $ip) {
        $keys_values = array();
        $keys_values['name'] = $ip;
        $ip_bean = retrieve_record_bean('btc_IP', $keys_values);
        if (empty($ip_bean->id)) {
            $ip_bean->name = $ip;
            $ip_bean->save();
        }
        $ip_bean->load_relationship('btc_hosting_btc_ip');
        $ip_bean->btc_hosting_btc_ip->add($virtualserver_bean);
    }

    static private function relate_virtualserver_with_vm($virtualserver_bean, $ip) {
        $select = "SELECT mv.id";
        $from = "FROM btc_ip i, btc_maquinas_virtuales_btc_ip_c mvip, btc_maquinas_virtuales mv";
        $where = "WHERE i.name = '".$ip."' AND i.id = mvip.btc_maquinas_virtuales_btc_ipbtc_ip_idb "
                ."AND mvip.btc_maquinas_virtuales_btc_ipbtc_maquinas_virtuales_ida = mv.id "
                ."AND i.deleted = 0 AND mv.deleted = 0 AND mvip.deleted = 0";
        $sql = $select." ".$from." ".$where;
        $mv_id = $GLOBALS['db']->getOne($sql);
        if (!empty($mv_id)) {
            $keys_values = array();
            $keys_values['id'] = $mv_id;
            $mv_bean = retrieve_record_bean('btc_Maquinas_virtuales', $keys_values);
            $mv_bean->load_relationship('btc_hosting_btc_maquinas_virtuales');
            $mv_bean->btc_hosting_btc_maquinas_virtuales->add($virtualserver_bean);
        }
    }

    static private function get_virtualserver_bean($virtualserver_name) {
        $select = "SELECT h.id";
        $from = "FROM btc_hosting h, btc_hosting_btc_ip_c hip, btc_ip ip";
        $where = "WHERE h.name = '".$virtualserver_name."' "
                ."AND ip.id = hip.btc_hosting_btc_ipbtc_ip_ida "
                ."AND hip.btc_hosting_btc_ipbtc_hosting_idb = h.id "
                ."AND h.deleted = 0 AND hip.deleted = 0 AND ip.deleted = 0";
        $sql = $select." ".$from." ".$where;
        $virtualserver_id = $GLOBALS['db']->getOne($sql);
        if (empty($virtualserver_id)) {
            return BeanFactory::newBean('btc_Hosting');
        } else {
            return BeanFactory::getBean('btc_Hosting', $virtualserver_id);
        }
    }

}

?>
