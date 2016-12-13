<?php

function retrieve_record_bean($module, $keys) {
    $bean = BeanFactory::newBean($module);
    $bean->retrieve_by_string_fields($keys);
    if (!empty( $bean->id )) {
        $bean = BeanFactory::getBean($module, $bean->id);
    }
    return $bean;
}

?>
