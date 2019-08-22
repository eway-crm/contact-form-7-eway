<?php

/*
 * Process updates in Contact form 7 Eway extension plugin
 */


if (isset($_POST[SUBMIT_FIELD])) {
    global $wpdb;
    $table = $wpdb->prefix . "" . SERVICE_TABLE;
    
    $url = addslashes ($_POST[URL_FIELD]);
    $user = addslashes ($_POST[USER_FIELD]);
    $password = addslashes ($_POST[PWD_FIELD]);

    try{
        $connector = new eWayConnector($url, $user, $password);
        
        $permission = false;
        $permissions = $connector->getMyModulePermissions()->Data;
        foreach($permissions as $module)
        {
            if($module->FolderName == 'Leads' && $module->CanCreate == true)
            {
                $permission = true;
            }
        }
        
        if($permission == false)
        {
            throw new Exception('This user does not have permission to create Leads!');
        }
        
        $sql = "SELECT * FROM " . $table;
    
        $r = $wpdb->get_row($sql, ARRAY_A);
    
        if ($wpdb->num_rows == 0) {                
            $data = array(URL_FIELD => $url, USER_FIELD => $user, PWD_FIELD => $password);        
            $query = $wpdb->insert($table, $data);        
            if (!$query) {
                EchoError('Error when creating Contact form 7 eway extension parameters. Please try again.');
            } else {
                EchoInfo('Contact form 7 eway extension parameters were succesfully created.');
            }
        } else {
            EchoError('Error when updating Contact form 7 eway extension parameters. Please check database consistency.');
        }
    }
    catch(Exception $e){
        EchoError($e->getMessage());
    }
   
}

if (isset($_POST[LOGOUT_FIELD]))
{
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE ".$wpdb->prefix . "" . SERVICE_TABLE );
}

function EchoError($msg) {
    echo '<div style="margin: 10px; border: 2px solid red; padding: 10px;">';
    echo $msg;
    echo '</div>';
}

function EchoInfo($msg) {
    echo '<div style="margin: 10px; border: 2px solid green; padding: 10px;">';
    echo $msg;
    echo '</div>';
}

?>
