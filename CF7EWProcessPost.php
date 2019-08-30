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

if (isset($_POST[ADD_FIELD]))
{
    global $wpdb;
    $table = $wpdb->prefix . "" . FIELDS_TABLE;
    $query = $wpdb->insert($table, array(FIELD_KEY => $_POST["wordpress"], FIELD_VALUE => $_POST["eway"]));
    if (!$query) {
        EchoError('Error when creating Contact form 7 eway extension custom field. Please try again.');
        LogMsgAdmin("Error when creating Contact form 7 eway extension custom field.\n");
    } else {
        EchoInfo('Contact form 7 eway extension custom field with name was succesfully created.');
        LogMsgAdmin("Contact form 7 eway extension custom field with name was succesfully created.\n");
    }
}

if (isset($_POST[RESTORE_DEFAULT]))
{
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE ".$wpdb->prefix . "" . FIELDS_TABLE );
    $wpdb->insert($wpdb->prefix . "" . FIELDS_TABLE, array(FIELD_KEY => "your-email", FIELD_VALUE => "Email"));
    $wpdb->insert($wpdb->prefix . "" . FIELDS_TABLE, array(FIELD_KEY => "your-subject", FIELD_VALUE => "FileAs"));
    $wpdb->insert($wpdb->prefix . "" . FIELDS_TABLE, array(FIELD_KEY => "your-message", FIELD_VALUE => "Note"));
    LogMsgAdmin("Custom fields were restored to default state.\n");
}

if (isset($_POST[DELETE_FIELD]))
{
    global $wpdb;
    $table = $wpdb->prefix . "" . FIELDS_TABLE;
    $query = $wpdb->delete($table, array(ID_FIELD => $_POST[ID_FIELD]));
    if (!$query) {
        EchoError('Error when deleting Contact form 7 eway extension custom field. Please try again.');
        LogMsgAdmin("Error when deleting Contact form 7 eway extension custom field.\n");
    } else {
        EchoInfo('Contact form 7 eway extension custom field with name was succesfully deleted.');
        LogMsgAdmin("Contact form 7 eway extension custom field with name was succesfully deleted.\n");
    }
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

function LogMsgAdmin($msg) {
    $msg = date('Y-m-d h:i:s', time()).': '.$msg;
    $fh = fopen('C:/wamp64/www/WordPress/wordpress/'.LOG_FILE, 'a') or die("can't open file");
    fwrite($fh, $msg);
    fclose($fh);
}

?>
