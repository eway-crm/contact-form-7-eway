<?php

/*
Plugin Name: eWay-CRM Extension for Contact Form 7
Plugin URI: https://github.com/eway-crm/contact-form-7-eway
Description: Plugin provides ability to create Deals in eWay-CRM from Contact Form 7 forms.
Version: 1.1.12
Author: eWay System s.r.o.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( "CF7EWConstants.php" );

require_once( "CF7EWFunctions.php" );

// Add link to wp admin menu
add_action( 'admin_menu', 'CF7EWMenu' );

// Process eWay-CRM lead record 
add_action( 'wpcf7_mail_sent', 'CF7EWProcessLead' );

// Register install plugin hook
register_activation_hook( __FILE__, 'CF7EWInstall' );

add_action( 'activated_plugin', 'CF7EWRedirect' );

//Register deactivation plugin hook
register_deactivation_hook( __FILE__, 'CF7EWDeactivate' );

function CF7EWProcessLead( $cf7 ) {
    // Process eWay-CRM lead recoring        
    CF7EWCreateLead( $cf7 );
}

// Manage plugin adnimnistration page and menu item
function CF7EWMenu() {
    //add options page to wp admin section
    add_options_page( CF7EW_TITLE, CF7EW_SHORT_TITLE, 'manage_options', CF7EW_ADMIN_PAGE, 'CF7EOptions' );
}

// Render plugin administration page
function CF7EOptions() {
    include( "CF7EWAdminPage.php" );
}

function CF7EWInstall() {
    // Add to db on install
    global $wpdb;
    $serviceTable = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;
    $fieldsTable = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;

    // Operation tables creation
    $createServiceTable = "CREATE TABLE IF NOT EXISTS " . $serviceTable . "
                    (
                    " . CF7EW_ID_FIELD . " INT NOT NULL AUTO_INCREMENT,			
                    " . CF7EW_URL_FIELD . " NVARCHAR(256),			
                    " . CF7EW_USER_FIELD . " NVARCHAR(256),
                    " . CF7EW_PWD_FIELD . " NVARCHAR(256),
                    UNIQUE KEY(" . CF7EW_ID_FIELD . ")
                    )";
                    
    $createFieldsTable = "
                    CREATE TABLE IF NOT EXISTS " . $fieldsTable . "
                    (
                    " . CF7EW_ID_FIELD . " INT NOT NULL AUTO_INCREMENT,					
                    " . CF7EW_FIELD_KEY . " NVARCHAR(256),
                    " . CF7EW_FIELD_VALUE . " NVARCHAR(256),
                    UNIQUE KEY(" . CF7EW_ID_FIELD . ")
                    )";
                    
    $wpdb->query( $createServiceTable );
    $wpdb->query( $createFieldsTable );
    
    // Fill default fields
    $wpdb->insert( $fieldsTable, array( CF7EW_FIELD_KEY => "your-email",    CF7EW_FIELD_VALUE => "Email" ) );
    $wpdb->insert( $fieldsTable, array( CF7EW_FIELD_KEY => "your-subject",  CF7EW_FIELD_VALUE => "FileAs" ) );
    $wpdb->insert( $fieldsTable, array( CF7EW_FIELD_KEY => "your-message",  CF7EW_FIELD_VALUE => "Note" ) );
    
    CF7EWLogMsg( "Log file created. \n" );
}

function CF7EWRedirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'options-general.php?page"='.CF7EW_ADMIN_PAGE.'' ) ) );
    }
}

function CF7EWDeactivate() {
    $options = get_option( 'CF7EWOptions' );

    if ( true === $options['do_uninstall'] ) {
        delete_option( 'CF7EWOptions' );
    }

    //remove database
    global $wpdb;
    $table = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;
    $drop_table = "DROP TABLE " . $table;
    $wpdb->query( $drop_table );
    
    $table = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $drop_table = "DROP TABLE " . $table;
    $wpdb->query( $drop_table );
}

?>