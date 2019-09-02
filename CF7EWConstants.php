<?php
/*
 * plugin constants
 */ 
 
//Contact form params
define( "CF_MAIL",          "your-email" ); //req
define( "CF_SUBJECT",       "your-subject" ); //req
define( "CF_MESSAGE",       "your-message" );

//Application params
define( "LOG_FILE",         "C:/wamp64/www/WordPress/wordpress/wp-content/plugins/contact-form-7-eway/log.txt" );

//DB constants
define( "SERVICE_TABLE",    "eway_cf7_settings" );
define( "FIELDS_TABLE",     "eway_cf7_fields" );

define( "ID_FIELD",         "id" );

define( "URL_FIELD",        "url" );
define( "USER_FIELD",       "eway_user" );
define( "PWD_FIELD",        "eway_password" );

define( "FIELD_KEY",        "field_key" );
define( "FIELD_VALUE",      "field_value" );

//Labels
define( "TITLE",            "eWay-CRM® Extension for Contact Form 7" );
define( "SHORT_TITLE",      "eWay-CRM CF7 extension" );

define( "ADMIN_PAGE",       "manage_eway_cf7_ext" );

//Administration form params
define( "SUBMIT_FIELD",     "sumbmit_ewayext_changes" );
define( "LOGOUT_FIELD",     "log_out" );
define( "ADD_FIELD",        "add_field" );
define( "DELETE_FIELD",     "delete_field" );
define( "RESTORE_DEFAULT",  "restore_default" );
?>