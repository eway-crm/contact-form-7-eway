<?php

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

/*
 * Functions implementing eWay-CRM lead creation
 */

if (!class_exists('eWayConnector')) {
	require_once('eway.class.php');
}

//Create lead in eWay-CRM database
function CF7EWCreateLead( $cf7 ) {
    
	CF7EWLogMsg( "Sending form.\n" );
	
    //Get contact form fields    
    $submission = WPCF7_Submission::get_instance();
     
    if ( $submission ) {
        $posted_data = $submission->get_posted_data();
    }

    global $wpdb;
    $table = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;
    $sql = "SELECT * FROM " . $table;
    $row = $wpdb->get_row( $sql, ARRAY_A );

    if ( empty( $row[CF7EW_URL_FIELD] ) || empty( $row[CF7EW_USER_FIELD] ) )
    {
        CF7EWLogMsg( "Connection has not yet been defined.\n" );
        return;
    }
    
    $connector = new eWayConnector( $row[CF7EW_URL_FIELD], $row[CF7EW_USER_FIELD], $row[CF7EW_PWD_FIELD], false, false, true, CF7EW_VERSION, $row[CF7EW_CLIENTID_FIELD], $row[CF7EW_CLIENTSECRET_FIELD], $row[CF7EW_REFRESHTOKEN_FIELD] );
    
    $newLead = array();
    
    $fieldsTable = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = "SELECT * FROM " . $fieldsTable;
	$fields = $wpdb->get_results( $query, ARRAY_A );
	
	if( $fields != null )
	{
		foreach( $fields as $field )
		{
			$field_data = $posted_data[$field['field_key']];
			
			if (is_array($field_data))
			{
				$field_data = implode(", ", $field_data);
			}
			
			$newLead[$field['field_value']] = $field_data;
		}
	}
	
    try
	{
        $result = $connector->saveLead( $newLead );
		if ( $result->ReturnCode == 'rcSuccess' ) {
			CF7EWLogMsg( "Website: Creation of lead: ". $result->Guid ." in eWay-CRM via API was successful.\n" );
		} else {
            CF7EWLogMsg( "Website: Creation of lead in eWay-CRM via API failed: " . $result->Description . ".\n" );
        }
    }
	catch ( Exception $e ) {
		$data = json_encode($newLead);
        CF7EWLogMsg( "Website: Creation of lead: ".$data." in eWay-CRM via API was unsuccessful:\n".$e."\n" );
        return;
    }
}

//Process errors
function CF7EWProcessError( $msg ) {
    global $wpdb;

    //Get web host
    $web = "";
    if ( substr( $_SERVER['HTTP_HOST'], 0, 4 ) == "www." ) {
        $web = substr( $_SERVER['HTTP_HOST'], 4 );
    }
	else {
        $web = $_SERVER['HTTP_HOST'];
    }

    $q = "SELECT user_email FROM " . $wpdb->prefix . "users WHERE ID = 1";
    $admin_email = $wpdb->get_var( $q );

    $error = $web . " - " . date( "d.m.Y G:i:s", Time() ) . ": " . $msg . "\n";

    //create error log
    CF7EWLogMsg( $error );
}

function CF7EWLogMsg( $msg ) {
	file_put_contents(CF7EW_LOG_FILE, date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ).': '.$msg . file_get_contents(CF7EW_LOG_FILE));
}

?>
