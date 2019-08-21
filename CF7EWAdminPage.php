<?php

require_once("CF7EWProcessPost.php");

/*
 * Render admin page form
 */

$urlField = "";
$userField = "";
$pwdField = "";

global $wpdb;
$table = $wpdb->prefix . "" . SERVICE_TABLE;
$sql = "SELECT * FROM " . $table;
$r = $wpdb->get_row($sql, ARRAY_A);

if ($wpdb->num_rows == 1) {        
    $urlField = $r[URL_FIELD];
    $userField = $r[USER_FIELD];
    $pwdField = $r[PWD_FIELD];
    
    $htmlResult = "";
    
    $htmlResult .= '<form action="?page='.ADMIN_PAGE.'" method="post" >';
    
    $htmlResult .= '<div style="display: flex;align-items: center;justify-content: center;" vertical-align="middle">';
    
    $htmlResult .= '<div style="padding: 20px;display: inline-block;background-color: white;">';
    
    $htmlResult .= '<table cellspacing="2">';
        $htmlResult .= "<tbody>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td style='height: 55px; width: 94px; padding: 10px;'>";
                    $htmlResult .= '<object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" width="100%"></object>';
                $htmlResult .= "</td>";
                $htmlResult .= "<td>";
                    $htmlResult .= '<h2 style="color: #E43025; padding-left: 30px;text-align:center;font-family: Segoe UI;">'.TITLE.'</h2>';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td colspan='2' style='padding: 10px;'>";
                    $htmlResult .= 'You are logged as: '.$r[USER_FIELD].' on url: '.$r[URL_FIELD].'';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr style='padding: 20px;'>";
                $htmlResult .= "<td>";                    
                $htmlResult .= "</td>";
                $htmlResult .= "<td style='padding: 10px;'>";
                    $htmlResult .= '<input style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.LOGOUT_FIELD.'" value="Log Out" />';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
        $htmlResult .= "</tbody>";
    $htmlResult .= "</table>";
    
    $htmlResult .= "</div>";
    
    $htmlResult .= "</div>";
    
    $htmlResult .= "</form>";
    
    $htmlResult2 = '
                    <style>
                        .tab{
                            max-height: 60px !important;
                            padding-left: 48px;
                            padding-top: 25px;
                            display: flex;
                        }
                        
                        .tab button {
                            height: 32px !important;
                            font-size: 20px !important;
                            color: rgba(0, 0, 0, 0.6) !important;
                            background-color: white !important;
                            border: none !important;
                            box-shadow: none !important;
                            cursor: pointer;
                        }
                        
                        .tab button.active {
                            height: 32px !important;
                            font-size: 20px !important;
                            color: black !important;
                            background-color: white !important;
                            border: none !important;
                            box-shadow: none !important;
                            cursor: pointer;
                        }
                        
                        .bottom{
                            border-bottom: 2px solid #0067B8;
                        }
                        
                        .tabcontent{
                            display: none;
                        }
                    
                    </style>
                    
                    <div style="background-color: white;width: 100%;min-height: 100vh;">
                        <div style="max-height: 60px !important;padding-left: 48px;padding-top: 25px;padding-right: 20px;display: flex;vertical-align: center;"> 
                            <div style="align-self: center;"><object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" height="60px"></object></div>
                            <div style="align-self: center;padding-left: 30px;color: #E43025;font-family: Segoe UI;font-size: 28px;font-weight: bold ;">'.TITLE.'</div>
                            <div style="align-self: center;padding-right: 30px;margin-left: auto;font-family: Segoe UI;font-size: 15px;">You are logged in as '.$r[USER_FIELD].'</div>
                            <div style="align-self: center;float: right;"><input style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.LOGOUT_FIELD.'" value="Log Out" /></div>
                        </div>
                        <div class="tab">
                            <button class="tablinks" onclick="openTab(event, \'History\')"><div class="bottom">History</div></button>
                            <button class="tablinks" onclick="openTab(event, \'Mapping\')"><div class="bottom">Mapping</div></button>
                        </div>
                    </div>
                    
                    <div id="History" class="tabcontent">
                    Below, find all attempts to save data into eWay-CRM.
                    </div>
                    
                    <div id="Mapping" class="tabcontent">
                    Here you can map your fields for the form.
                    </div>
                    
                    <script>
                        function openTab(evt, tabName){
                            var i, tabcontent, tablinks;
                            tabcontent = document.getElementsByClassName("tabcontent");
                            for (i = 0; i < tabcontent.length; i++){
                                tabcontent[i].style.display = "none";
                            }
                            tablinks = document.getElementsByClassName("tablinks");
                            for(i = 0; i < tablinks.length; i++){
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                            }
                            document.getElementById(tabName).style.display = "block";
                            evt.currentTarget.className += "active";
                        }
                    </script>
                    ';
    
    echo $htmlResult2;
}
else
{

    $htmlResult = "";
    
    $htmlResult .= '<form action="?page='.ADMIN_PAGE.'" method="post" >';
    
    $htmlResult .= '<div style="display: flex;align-items: center;justify-content: center;" vertical-align="middle">';
    
    $htmlResult .= '<div style="padding: 20px;display: inline-block;background-color: white;">';
    
    $htmlResult .= '<table cellspacing="2">';
        $htmlResult .= "<tbody>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td style='height: 55px; width: 94px; padding: 10px;'>";
                    $htmlResult .= '<object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" width="100%"></object>';
                $htmlResult .= "</td>";
                $htmlResult .= "<td>";
                    $htmlResult .= '<h2 style="color: #E43025; padding-left: 30px;text-align:center;font-family: Segoe UI;">'.TITLE.'</h2>';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td colspan='2' style='padding: 10px;'>";
                    $htmlResult .= '<input style="width: 100%;height: 40px;box-shadow: none;border: 0;outline: 0;background: transparent;border-bottom: 1px solid black;" type="text" name="'.URL_FIELD.'" placeholder="Web Service URL" />';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td colspan='2' style='padding: 10px;'>";
                    $htmlResult .= '<input style="width: 100%;height: 40px;box-shadow: none;border: 0;outline: 0;background: transparent;border-bottom: 1px solid black;" type="text" name="'.USER_FIELD.'" placeholder="Username" />';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr>";
                $htmlResult .= "<td colspan='2' style='padding: 10px;'>";
                    $htmlResult .= '<input style="width: 100%;height: 40px;box-shadow: none;border: 0;outline: 0;background: transparent;border-bottom: 1px solid black;" type="password" name="'.PWD_FIELD.'" placeholder="Password" />';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
            $htmlResult .= "<tr style='padding: 20px;'>";
                $htmlResult .= "<td>";                    
                $htmlResult .= "</td>";
                $htmlResult .= "<td style='padding: 10px;'>";
                    $htmlResult .= '<input style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.SUBMIT_FIELD.'" value="Log In" />';
                $htmlResult .= "</td>";
            $htmlResult .= "</tr>";
        $htmlResult .= "</tbody>";
    $htmlResult .= "</table>";
    
    $htmlResult .= "</div>";
    
    $htmlResult .= "</div>";
    
    $htmlResult .= "</form>";
    
    echo $htmlResult;
}
?>