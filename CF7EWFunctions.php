<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/*
 * Functions implementing eWay-CRM lead creation
 */

if (!class_exists('eWayConnector')) {
    require_once('eway.class.php');
}

function CF7EWValidateFolder($folder)
{
    $possibleFolders = array('Leads', 'Contacts');
    return in_array($folder, $possibleFolders) ? $folder : 'Leads';
}

function CF7EWReadSettings()
{
    global $wpdb;
    $sql = "SELECT * FROM " . $wpdb->prefix . CF7EW_SETTINGS_TABLE;
    return $wpdb->get_row($sql, ARRAY_A);
}

function CF7EWCreateConnection()
{
    $row = CF7EWReadSettings();

    if (empty($row[CF7EW_URL_FIELD]) || empty($row[CF7EW_USER_FIELD]))
        return null;
    return new eWayConnector($row[CF7EW_URL_FIELD], $row[CF7EW_USER_FIELD], $row[CF7EW_PWD_FIELD], false, false, true, CF7EW_VERSION, $row[CF7EW_CLIENTID_FIELD], $row[CF7EW_CLIENTSECRET_FIELD], $row[CF7EW_REFRESHTOKEN_FIELD]);
}

function CF7EWCheckFolderSettingExistence()
{
    global $wpdb;
    $wpdb->get_results("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . $wpdb->dbname . "' AND TABLE_NAME = '" . $wpdb->prefix . CF7EW_SETTINGS_TABLE . "' AND COLUMN_NAME = '" . CF7EW_FOLDER_FIELD . "'");
    if ($wpdb->num_rows == 0) {
        $wpdb->query("ALTER TABLE " . $wpdb->prefix . CF7EW_SETTINGS_TABLE . " ADD " . CF7EW_FOLDER_FIELD . " VARCHAR(256);");
    }
}

//Create lead in eWay-CRM database
function CF7EWCreateRecord($cf7)
{
    //Get contact form fields    
    $submission = WPCF7_Submission::get_instance();

    $contact_form = $submission->get_contact_form();
    if ($contact_form->is_true( 'do_not_store_eway' )) {
        CF7EWLogMsg("Ignoring form ".$contact_form->title.", because of do_not_store_eway flag.\n");
        return;
    }

    if ($submission) {
        $posted_data = $submission->get_posted_data();
    }

    CF7EWLogMsg("Sending form.\n");

    $connector = CF7EWCreateConnection();
    if (!$connector) {
        CF7EWLogMsg("Connection has not yet been defined.\n");
        return;
    }

    $newRecord = array();

    global $wpdb;
    $settings = CF7EWReadSettings();
    $folder = CF7EWValidateFolder(@$settings[CF7EW_FOLDER_FIELD]);
    $fieldsTable = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = "SELECT * FROM " . $fieldsTable;
    $fields = $wpdb->get_results($query, ARRAY_A);
    $additional_fields = array();

    if ($fields != null) {
        foreach ($fields as $field) {
            $cf_field_name = $field['field_key'];
            $ew_field_name = $field['field_value'];
            
            $field_data = $posted_data[$cf_field_name];
            if (is_array($field_data)) {
                $field_data = implode(", ", $field_data);
            }

            if (stripos($ew_field_name, 'af_') === 0) {
                $additional_fields[$ew_field_name] = $field_data;
            } else {
                $newRecord[$ew_field_name] = $field_data;
            }
        }
    }

    // Add also additional fields
    $newRecord['AdditionalFields'] = $additional_fields;

    try {
        $result = $folder == 'Contacts' ? $connector->saveContact($newRecord) : $connector->saveLead($newRecord);
        if ($result->ReturnCode == 'rcSuccess') {
            CF7EWLogMsg("Website: Creation of '$folder' record: " . $result->Guid . " in eWay-CRM via API was successful.\n");
        } else {
            CF7EWLogMsg("Website: Creation of '$folder' record in eWay-CRM via API failed: " . $result->Description . ".\n");
        }
    } catch (Exception $e) {
        $data = json_encode($newRecord);
        CF7EWLogMsg("Website: Creation of record: " . $data . " in eWay-CRM via API was unsuccessful:\n" . $e . "\n");
        return;
    } finally {
        $connector->logOut();
    }
}

//Process errors
function CF7EWProcessError($msg)
{
    global $wpdb;

    //Get web host
    $web = "";
    if (substr($_SERVER['HTTP_HOST'], 0, 4) == "www.") {
        $web = substr($_SERVER['HTTP_HOST'], 4);
    } else {
        $web = $_SERVER['HTTP_HOST'];
    }

    $q = "SELECT user_email FROM " . $wpdb->prefix . "users WHERE ID = 1";
    $admin_email = $wpdb->get_var($q);

    $error = $web . " - " . date("d.m.Y G:i:s", Time()) . ": " . $msg . "\n";

    //create error log
    CF7EWLogMsg($error);
}

function CF7EWLogMsg($msg)
{
    file_put_contents(CF7EW_LOG_FILE, date('Y-m-d H:i:s', current_time('timestamp')) . ': ' . $msg . file_get_contents(CF7EW_LOG_FILE));
}

?>