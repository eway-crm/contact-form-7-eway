<?php
/*
 * plugin constants
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Application params
define( "CF7EW_LOG_FILE",           dirname( __FILE__ )."\CF7EWLog.txt" );
define( "CF7EW_ICON_FILE",          plugin_dir_url( __FILE__ )."icon.svg" );
define( "CF7EW_VERSION",            "CF7EW-1.1.15" );

//DB constants
define( "CF7EW_SETTINGS_TABLE",     "eway_cf7_settings" );
define( "CF7EW_FIELDS_TABLE",       "eway_cf7_fields" );

define( "CF7EW_ID_FIELD",           "id" );

define( "CF7EW_URL_FIELD",          "url" );
define( "CF7EW_USER_FIELD",         "eway_user" );
define( "CF7EW_PWD_FIELD",          "eway_password" );
define( "CF7EW_CLIENTID_FIELD",     "eway_clientid" );
define( "CF7EW_CLIENTSECRET_FIELD", "eway_clientsecret" );
define( "CF7EW_CODEVERIFIER_FIELD", "eway_codeverifier" );
define( "CF7EW_REFRESHTOKEN_FIELD", "eway_refreshtoken" );

define( "CF7EW_FIELD_KEY",          "field_key" );
define( "CF7EW_FIELD_VALUE",        "field_value" );

//Labels
define( "CF7EW_TITLE",              "eWay-CRM Extension for Contact Form 7" );
define( "CF7EW_SHORT_TITLE",        "eWay-CRM CF7 Extension" );

define( "CF7EW_ADMIN_PAGE",         "manage_eway_crm_cf7_extension" );

//Administration form params
define( "CF7EW_SUBMIT_FIELD",       "sumbmit_ewayext_changes" );
define( "CF7EW_LOGOUT_FIELD",       "log_out" );
define( "CF7EW_ADD_FIELD",          "add_field" );
define( "CF7EW_DELETE_FIELD",       "delete_field" );
define( "CF7EW_RESTORE_DEFAULT",    "restore_default" );

define( "CF7EW_CODE_PARAM",         "code" );
define( "CF7EW_WSURL_PARAM",        "ws_url" );
?>