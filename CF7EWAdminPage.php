<?php

require_once("CF7EWProcessPost.php");

/*
 * Render admin page form
 */

$urlField = "";
$userField = "";
$pwdField = "";

global $wpdb;

$fieldsTable = $wpdb->prefix . "" . FIELDS_TABLE;
$query = "SELECT * FROM " . $fieldsTable;
$fields = $wpdb->get_results($query, ARRAY_A);

foreach($fields as $field)
{
    $key = $field[FIELD_KEY];
    $value = $field[FIELD_VALUE];
    
    $table .= '
                <tr>
                    <td>'.$key.'</td>
                    <td>'.$value.'</td>
                </tr>
                ';
}

$table = $wpdb->prefix . "" . SERVICE_TABLE;
$sql = "SELECT * FROM " . $table;
$r = $wpdb->get_row($sql, ARRAY_A);

if ($r != null) {        
    $urlField = $r[URL_FIELD];
    $userField = $r[USER_FIELD];
    $pwdField = $r[PWD_FIELD];
    
    $htmlResult = '
                    <style>
                        .tab{
                            max-height: 60px !important;
                            padding-left: 42px;
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
                            width: fit-content;
                        }
                        
                        .tab button.active {
                            height: 32px !important;
                            font-size: 20px !important;
                            color: black !important;
                            background-color: white !important;
                            border: none !important;
                            box-shadow: none !important;
                            cursor: pointer;
                            width: fit-content;
                            outline: none !important;
                        }
                        
                        .bottom{
                            border-bottom: none !important;
                        }
                        
                        .bottom.active{
                            border-bottom: 2px solid #0067B8 !important;
                        }
                        
                        .tabcontent{
                            display: none;
                            padding-left: 48px;
                            padding-top: 20px;
                            padding-right: 20px;
                            font-family: Segoe UI;
                            font-size: 15px;
                            min-height: 100vh;
                        }
                        
                        .content{
                            font-family: Segoe UI;
                            font-size: 15px;
                            border: 1px solid;
                            border-color: rgba(0, 0, 0, 0.6) !important;
                            margin-top: 20px !important;
                            height: 100vh;
                        }
                    
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                          
                        th, td {
                            text-align: left;
                            padding: 8px;
                        }
                          
                        tr:nth-child(even) {background-color: #f2f2f2;}
                    
                    </style>
                    
                    <div style="background-color: white;width: 100%;min-height: 100vh;">
                    
                        <form action="?page='.ADMIN_PAGE.'" method="post" >
                    
                        <div style="max-height: 60px !important;padding-left: 48px;padding-top: 25px;padding-right: 20px;display: flex;vertical-align: center;"> 
                            <div style="align-self: center;"><object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" height="60px"></object></div>
                            <div style="align-self: center;padding-left: 30px;color: #E43025;font-family: Segoe UI;font-size: 28px;font-weight: bold ;">'.TITLE.'</div>
                            <div style="align-self: center;padding-right: 30px;margin-left: auto;font-family: Segoe UI;font-size: 15px;">You are logged in as '.$r[USER_FIELD].'</div>
                            <div style="align-self: center;float: right;"><input style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.LOGOUT_FIELD.'" value="Log Out" /></div>
                        </div>
                        
                        </form>
                        
                        <form action="">
                        <div class="tab">
                            <button class="tablinks active" onclick="openTab(event, \'History\')"><div class="bottom active">History</div></button>
                            <button class="tablinks" onclick="openTab(event, \'Mapping\')"><div class="bottom">Mapping</div></button>
                        </div>
                        </form>
                        
                        <form action="">
                        <div id="History" class="tabcontent" style="display: block;">
                            <div>Below, find all attempts to save data into eWay-CRM.</div>
                            <div class="content">
                                '.file_get_contents("C:\wamp64\www\WordPress\wordpress\wp-content\plugins\contact-form-7-eway\log.txt").'
                            </div>
                            <div style="min-height: 25px !important;"></div>
                        </div>
                        </form>
                        
                        <form action="">
                        <div id="Mapping" class="tabcontent">
                            Here you can map your fields for the form.
                            <div class="content">
                                <table>
                                    <tr>
                                        <th>WordPress Field</th>
                                        <th>eWay Field</th>
                                    </tr>
                                '.$table.'
                                </table>
                            </div>
                            
                            <div style="min-height: 60px !important;padding-top: 25px;padding-right: 20px;display: flex;vertical-align: center;">
                                <div style="align-self: center;">WordPress Field <input name="wordpress" type="text"/></div>
                                <div style="align-self: center;padding-left: 30px;">eWay Field <input name="eway" type="text"/></div>
                                <div style="align-self: center;float: right;"><input style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.ADD_FIELD.'" value="Add Field" /></div>
                            </div>
                        </div>
                        </form>
                            
                    </div>
                    
                    <script>
                        function openTab(evt, tabName){
                            var i, tabcontent, tablinks, btm;
                            btm = document.getElementsByClassName("bottom active");
                            btm[0].className = btm[0].className.replace(" active", "");
                            evt.currentTarget.childNodes[0].className += " active";
                            tabcontent = document.getElementsByClassName("tabcontent");
                            for (i = 0; i < tabcontent.length; i++){
                                tabcontent[i].style.display = "none";
                            }
                            tablinks = document.getElementsByClassName("tablinks");
                            for(i = 0; i < tablinks.length; i++){
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                            }
                            document.getElementById(tabName).style.display = "block";
                            evt.currentTarget.className += " active";
                        }
                    </script>
                    ';
    
    echo $htmlResult;
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