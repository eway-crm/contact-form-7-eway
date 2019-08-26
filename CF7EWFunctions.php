<?php

/*
 * Functions implementing eway lead creation
 */

require_once('eway.class.php');

//Create lead in Eway database
function CreateEwayLead($cf7) {
    
	LogMsg("Sending form.\n");
	
    //Get contact form fields    
    $submission = WPCF7_Submission::get_instance();
     
    if ( $submission ) {
        $posted_data = $submission->get_posted_data();
    }

    global $wpdb;
    $table = $wpdb->prefix . "" . SERVICE_TABLE;
    $sql = "SELECT * FROM " . $table;
    $r = $wpdb->get_row($sql, ARRAY_A);
    
    $connector = new eWayConnector($r[URL_FIELD], $r[USER_FIELD], $r[PWD_FIELD]);
    
    $newLead = array(
                        'FileAs'    => $posted_data[CF_SUBJECT],
                        'Email'     => $posted_data[CF_MAIL],
                        'Note'      => $posted_data[CF_MESSAGE]
                    );
    
	global $wpdb;
    $fieldsTable = $wpdb->prefix . "" . FIELDS_TABLE;
    $query = "SELECT * FROM " . $fieldsTable;
	$fields = $wpdb->get_results($query, ARRAY_A);
	
	if($fields != null)
	{
		foreach($fields as $field)
		{
			$newLead += [$field['key'] => $field['value']];
		}
	}
	
    try
	{
        $result = $connector->saveLead($newLead);
		if ($result->ReturnCode == 'rcSuccess'){
			LogMsg("Website: Creation of lead: ". $posted_data[CF_SUBJECT] . " in eWay via API was successful.\n");
		}
    }
	catch (Exception $e) {
        LogMsg('Website: Creation of lead: '. $posted_data[CF_SUBJECT] . ' in eWay via API was unsuccessful.\n');
        return;
    }
}

//Process errors
function ProcessError($msg) {
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
    LogMsg($error);
}

function LogMsg($msg) {
    $fh = fopen(LOG_FILE, 'a') or die("can't open file");
    fwrite($fh, $msg);
    fclose($fh);
}

?>
