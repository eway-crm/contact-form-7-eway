<?php

/*
  Plugin Name: eWay-CRM® Extension for Contact Form 7
  Plugin URI: http://www.memos.cz
  Description: Plugin provides ability to record leads to eWay-CRM® database from contact form 7
  Version: 1.0
  Author: Jan Pavlovský for Memos Software s.r.o.
  License: Property of Memos Software s.r.o.
 */

require_once( "CF7EWConstants.php" );

require_once( "CF7EWFunctions.php" );

// Add link to wp admin menu
add_action( 'admin_menu', 'CF7EWMenu' );

// Manage plugin adnimnistration page and menu item
function CF7EWMenu() {
    //add options page to wp admin section
    add_options_page( TITLE, SHORT_TITLE, administrator, ADMIN_PAGE, 'CF7EOptions' );
}

// Render plugin administration page
function CF7EOptions() {
    include( "CF7EWAdminPage.php" );
}

// Process eWay-CRM lead record 
add_action( 'wpcf7_mail_sent', 'CF7EWProcessLead' );

function CF7EWProcessLead( $cf7 ) {
    // Process eWay-CRM lead recoring        
    CreateLead( $cf7 );
}

// Register install plugin hook
register_activation_hook( __FILE__, 'CF7EWInstall' );

function CF7EWInstall() {
    // Add to db on install
    global $wpdb;
    $serviceTable = $wpdb->prefix . "" . SERVICE_TABLE;
    $fieldsTable = $wpdb->prefix . "" . FIELDS_TABLE;

    // Operation tables creation
    $createServiceTable = "CREATE TABLE IF NOT EXISTS " . $serviceTable . "
					(
					" . ID_FIELD . " INT NOT NULL AUTO_INCREMENT,			
					" . URL_FIELD . " NVARCHAR(256),			
                    " . USER_FIELD . " NVARCHAR(256),
                    " . PWD_FIELD . " NVARCHAR(256),
                    UNIQUE KEY(" . ID_FIELD . ")
					)";
                    
    $createFieldsTable = "
                    CREATE TABLE IF NOT EXISTS " . $fieldsTable . "
                    (
					" . ID_FIELD . " INT NOT NULL AUTO_INCREMENT,					
                    " . FIELD_KEY . " NVARCHAR(256),
                    " . FIELD_VALUE . " NVARCHAR(256),
                    UNIQUE KEY(" . ID_FIELD . ")
					)";
                    
    $wpdb->query( $createServiceTable );
    $wpdb->query( $createFieldsTable );
    
    // Fill default fields
    $wpdb->insert( $fieldsTable, array( FIELD_KEY => "your-email", FIELD_VALUE => "Email" ) );
    $wpdb->insert( $fieldsTable, array( FIELD_KEY => "your-subject", FIELD_VALUE => "FileAs" ) );
    $wpdb->insert( $fieldsTable, array( FIELD_KEY => "your-message", FIELD_VALUE => "Note" ) );
}

//Register deactivation plugin hook
register_deactivation_hook( __FILE__, 'CF7EWDeactivate' );

function CF7EWDeactivate() {
    $options = get_option( 'CF7EWOptions' );

    if ( true === $options['do_uninstall'] ) {
        delete_option( 'CF7EWOptions' );
    }

    //remove database
    global $wpdb;
    $table = $wpdb->prefix . "" . SERVICE_TABLE;
    $drop_table = "DROP TABLE " . $table;
    $wpdb->query( $drop_table );
    
    $table = $wpdb->prefix . "" . FIELDS_TABLE;
    $drop_table = "DROP TABLE " . $table;
    $wpdb->query( $drop_table );
}

?>