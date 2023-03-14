<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

require_once("CF7EWFunctions.php");

global $wpdb;

/*
 * Process updates in Contact form 7 Eway extension plugin
 */

if (isset($_POST[CF7EW_SUBMIT_FIELD]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'login') && current_user_can('manage_options')) {
    $table = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;

    $url = addslashes($_POST[CF7EW_URL_FIELD]);
    $user = addslashes($_POST[CF7EW_USER_FIELD]);
    $clientid = addslashes($_POST[CF7EW_CLIENTID_FIELD]);
    $clientsecret = addslashes($_POST[CF7EW_CLIENTSECRET_FIELD]);

    try {
        $connector = new eWayConnector($url, $user, null, false, false, true, CF7EW_VERSION, $clientid, $clientsecret);
        $codeVerifier = $connector->generateCodeVerifier();
        $codeChallenge = $connector->getCodeChallenge($codeVerifier);

        // Delete any previous settings
        deleteSettings($wpdb);

        $sql = "SELECT * FROM " . $table;
        $r = $wpdb->get_row($sql, ARRAY_A);
        if ($wpdb->num_rows == 0) {
            $data = array(CF7EW_URL_FIELD => $url, CF7EW_USER_FIELD => $user, CF7EW_CLIENTID_FIELD => $clientid, CF7EW_CLIENTSECRET_FIELD => $clientsecret, CF7EW_CODEVERIFIER_FIELD => $codeVerifier);
            $query = $wpdb->insert($table, $data);
            if (!$query) {
                CF7EWEchoError('Unable to save credentials: ' . $wpdb->last_error);
                return;
            }
        }

        $redirectUrl = $connector->getAuthorizationUrl(getRedirectUrl(), $codeChallenge, $user, true);

        if (empty($redirectUrl)) {
            CF7EWEchoError('Unable to get authorization URL.');
            return;
        }

        CF7EWEchoInfo("Redirecting...<script>window.location.href = \"" . $redirectUrl . "\"</script>");
    } catch (Exception $e) {
        CF7EWEchoError($e->getMessage());
    }
}

if (isset($_GET[CF7EW_CODE_PARAM]) && isset($_GET[CF7EW_WSURL_PARAM])) {
    try {
        $table = $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE;
        $sql = "SELECT * FROM " . $table;
        $row = $wpdb->get_row($sql, ARRAY_A);

        $connector = new eWayConnector($row[CF7EW_URL_FIELD], $row[CF7EW_USER_FIELD], null, false, false, true, CF7EW_VERSION, $row[CF7EW_CLIENTID_FIELD], $row[CF7EW_CLIENTSECRET_FIELD]);
        $response = $connector->finishAuthorization(getRedirectUrl(), $row[CF7EW_CODEVERIFIER_FIELD], $_GET[CF7EW_CODE_PARAM]);

        if (empty($response->refresh_token)) {
            CF7EWEchoError('There is not refresh token in the response.');
            return;
        }

        if (!$connector->getUserGuid()) {
            CF7EWEchoError('Unable to check connection.');
            return;
        }

        CF7EWLogMsg("Connection successful.\n");

        $wpdb->query("UPDATE " . $table . " SET " . CF7EW_REFRESHTOKEN_FIELD . " = '" . $response->refresh_token . "', " . CF7EW_CODEVERIFIER_FIELD . " = NULL");
    } catch (Exception $e) {
        CF7EWEchoError($e->getMessage());
        CF7EWLogMsg($e);
    }
}

if (isset($_POST[CF7EW_LOGOUT_FIELD]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'logout') && current_user_can('manage_options')) {
    deleteSettings($wpdb);
}

if (isset($_POST[CF7EW_ADD_FIELD]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'fields') && current_user_can('manage_options')) {
    $table = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = $wpdb->insert($table, array(CF7EW_FIELD_KEY => sanitize_text_field($_POST["wordpress"]), CF7EW_FIELD_VALUE => sanitize_text_field($_POST["eway"])));
    if (!$query) {
        CF7EWEchoError('Unable to add new field: ' . $wpdb->last_error);
    } else {
        CF7EWEchoInfo('Contact form 7 eWay-CRM extension custom field was succesfully created.');
    }
}

if (isset($_POST[CF7EW_RESTORE_DEFAULT]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'fields') && current_user_can('manage_options')) {
    $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "" . CF7EW_FIELDS_TABLE);
    $wpdb->insert($wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array(CF7EW_FIELD_KEY => "your-email", CF7EW_FIELD_VALUE => "Email"));
    $wpdb->insert($wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array(CF7EW_FIELD_KEY => "your-subject", CF7EW_FIELD_VALUE => "FileAs"));
    $wpdb->insert($wpdb->prefix . "" . CF7EW_FIELDS_TABLE, array(CF7EW_FIELD_KEY => "your-message", CF7EW_FIELD_VALUE => "Note"));
    CF7EWLogMsg("Custom fields were restored to default state.\n");
}

if (isset($_POST[CF7EW_DELETE_FIELD]) && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'delete_field') && current_user_can('manage_options')) {
    $table = $wpdb->prefix . "" . CF7EW_FIELDS_TABLE;
    $query = $wpdb->delete($table, array(CF7EW_ID_FIELD => $_POST[CF7EW_ID_FIELD]));
    if (!$query) {
        CF7EWEchoError('Unable to delete field: ' . $wpdb->last_error);
    } else {
        CF7EWEchoInfo('Contact form 7 eWay-CRM extension custom field was succesfully deleted.');
    }
}

function CF7EWEchoError($msg)
{
    echo '<div style="margin: 10px; border: 2px solid red; padding: 10px;">';
    echo $msg;
    echo '</div>';

    CF7EWLogMsg($msg . "\n");
}

function CF7EWEchoInfo($msg)
{
    echo '<div style="margin: 10px; border: 2px solid green; padding: 10px;">';
    echo $msg;
    echo '</div>';

    CF7EWLogMsg($msg . "\n");
}

function getRedirectUrl()
{
    return admin_url() . "options-general.php?page=" . CF7EW_ADMIN_PAGE;
}

function deleteSettings($wpdb)
{
    $wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "" . CF7EW_SETTINGS_TABLE);
}

?>