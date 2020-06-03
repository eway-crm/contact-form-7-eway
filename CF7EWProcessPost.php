<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( "CF7EWFunctions.php" );

/*
 * Process updates in Contact form 7 Eway extension plugin
 */

if ( isset( $_POST[CF7EW_SUBMIT_FIELD] ) && isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'login') && current_user_can( 'manage_options' ) ) {
    global $wpdb;
    $table = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;
    
    $url = addslashes( $_POST[CF7EW_URL_FIELD] );
    $user = addslashes( $_POST[CF7EW_USER_FIELD] );
    $password = addslashes( $_POST[CF7EW_PWD_FIELD] );

    try
    {
        $connector = new eWayConnector( $url, $user, $password );
        
        $permission = false;
        $permissions = $connector->getMyModulePermissions()->Data;
        foreach ( $permissions as $module )
        {
            if ( $module->FolderName == 'Leads' && $module->CanCreate == true )
            {
                $permission = true;
            }
        }
        
        if ( $permission == false )
        {
            throw new Exception( 'This user does not have permission to create Leads!' );
        }
        
        $sql = "SELECT * FROM " . $table;
    
        $r = $wpdb->get_row( $sql, ARRAY_A );
    
        if ( $wpdb->num_rows == 0 ) {                
            $data = array( CF7EW_URL_FIELD => $url, CF7EW_USER_FIELD => $user, CF7EW_PWD_FIELD => $password );        
            $query = $wpdb->insert( $table, $data );        
            if ( !$query ) {
                CF7EWEchoError( 'Error when creating Contact form 7 eWay-CRM extension parameters. Please try again.' );
            }
            else {
                CF7EWEchoInfo( 'Contact form 7 eway extension parameters eWay-CRM succesfully created.' );
            }
        }
        else {
            CF7EWEchoError( 'Error when updating Contact form 7 eWay-CRM extension parameters. Please check database consistency.' );
        }
    }
    catch( Exception $e ){
        CF7EWEchoError( $e->getMessage() );
    }
   
}

if (isset( $_POST[CF7EW_LOGOUT_FIELD] ) && isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'logout') && current_user_can( 'manage_options' ) )
{
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE ".$wpdb->prefix . "" . CF7EW_SETTINGS_TABLE );   
}

if ( isset( $_POST[CF7EW_ADD_FIELD] ) && isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'fields') && current_user_can( 'manage_options' ) )
{
    global $wpdb;
    $table = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = $wpdb->insert( $table, array( CF7EW_FIELD_KEY => sanitize_text_field( $_POST["wordpress"] ), CF7EW_FIELD_VALUE => sanitize_text_field( $_POST["eway"] ) ) );
    if ( !$query ) {
        CF7EWEchoError( 'Error when creating Contact form 7 eWay-CRM extension custom field. Please try again.' );
        CF7EWLogMsg( "Error when creating Contact form 7 eWay-CRM extension custom field.\n" );
    }
    else {
        CF7EWEchoInfo( 'Contact form 7 eWay-CRM extension custom field was succesfully created.' );
        CF7EWLogMsg( "Contact form 7 eWay-CRM extension custom field was succesfully created.\n" );
    }
}

if ( isset( $_POST[CF7EW_RESTORE_DEFAULT] ) && isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'fields') && current_user_can( 'manage_options' ) )
{
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE ".$wpdb->prefix . "" . CF7EW_FIELDS_TABLE );
    $wpdb->insert( $wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array( CF7EW_FIELD_KEY => "your-email",     CF7EW_FIELD_VALUE => "Email" ) );
    $wpdb->insert( $wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array( CF7EW_FIELD_KEY => "your-subject",   CF7EW_FIELD_VALUE => "FileAs" ) );
    $wpdb->insert( $wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array( CF7EW_FIELD_KEY => "your-message",   CF7EW_FIELD_VALUE => "Note" ) );
    CF7EWLogMsg( "Custom fields were restored to default state.\n" );
}

if ( isset( $_POST[CF7EW_DELETE_FIELD] ) && isset( $_POST['nonce'] ) && wp_verify_nonce($_POST['nonce'], 'delete_field') && current_user_can( 'manage_options' ) )
{
    global $wpdb;
    $table = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = $wpdb->delete( $table, array( CF7EW_ID_FIELD => $_POST[CF7EW_ID_FIELD] ) );
    if ( !$query ) {
        CF7EWEchoError( 'Error when deleting Contact form 7 eWay-CRM extension custom field. Please try again.' );
        CF7EWLogMsg( "Error when deleting Contact form 7 eWay-CRM extension custom field.\n" );
    }
    else {
        CF7EWEchoInfo( 'Contact form 7 eWay-CRM extension custom field was succesfully deleted.' );
        CF7EWLogMsg( "Contact form 7 eWay-CRM extension custom field was succesfully deleted.\n" );
    }
}

function CF7EWEchoError( $msg ) {
    echo '<div style="margin: 10px; border: 2px solid red; padding: 10px;">';
    echo $msg;
    echo '</div>';
}

function CF7EWEchoInfo( $msg ) {
    echo '<div style="margin: 10px; border: 2px solid green; padding: 10px;">';
    echo $msg;
    echo '</div>';
}

?>
