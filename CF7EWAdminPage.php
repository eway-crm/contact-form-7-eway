<?php

require_once("CF7EWProcessPost.php");

/*
 * Render admin page form
 */

$urlField = "";
$userField = "";
$pwdField = "";

global $wpdb;

function getFields()
{
    global $wpdb;
    $fieldsTable = $wpdb->prefix . "" . FIELDS_TABLE;
    $query = "SELECT * FROM " . $fieldsTable;
    $fields = $wpdb->get_results( $query, ARRAY_A );
    foreach ( $fields as $field )
    {
        $key = $field[FIELD_KEY];
        $value = $field[FIELD_VALUE];
        $id = $field[ID_FIELD];
        
        $ftable .= '
                    <tr>
                        <td>'.$key.'</td>
                        <td>'.$value.'</td>
                        <td><input  class="buttonStyle" style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.DELETE_FIELD.'" value="Delete Field" placeholder="Delete" /></td>
                        <input type="hidden" name="id" value="'.$id.'">
                    </tr>
                    ';
    }
    return $ftable;
}

$table = $wpdb->prefix . "" . SERVICE_TABLE;
$sql = "SELECT * FROM " . $table;
$r = $wpdb->get_row( $sql, ARRAY_A );

if ( $r != null ) {        
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
                            padding: 25px;
                            overflow: auto;
                        }
                    
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                          
                        th, td {
                            text-align: left;
                            padding: 8px;
                        }
                        
                        .buttonStyle:hover {
                            background-color: #004C89 !important;
                            cursor: pointer !important;
                        }
                          
                        tr:nth-child(even) {background-color: #f2f2f2;}
                    
                    </style>
                    
                    <div style="background-color: white;width: 100%;min-height: 100vh;">
                    
                        <form action="?page='.ADMIN_PAGE.'" method="post" >
                    
                        <div style="padding-left: 48px;padding-top: 25px;padding-right: 20px;display: flex;vertical-align: center;"> 
                            <div style=""><object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" height="60px"></object></div>
                            <div style="align-self: center;padding-left: 30px;color: #E43025;font-family: Segoe UI;font-size: 28px;font-weight: bold ;">'.TITLE.'</div>
                            <div style="align-self: center;padding-right: 30px;margin-left: auto;font-family: Segoe UI;font-size: 15px;">You are logged in as '.$r[USER_FIELD].'</div>
                            <div style="align-self: center;float: right;"><input class="buttonStyle" style="background-color: #0062AF;height: 32px;width: 108px;color: white;border: none;" type="submit" name="'.LOGOUT_FIELD.'" value="Log Out" /></div>
                        </div>
                        
                        </form>
                        
                        <div class="tab">
                            <button class="tablinks active" id="btnHistory" onclick="openTab(\'History\')"><div class="bottom active">History</div></button>
                            <button class="tablinks" id="btnMapping" onclick="openTab(\'Mapping\')"><div class="bottom">Mapping</div></button>
                        </div>
                        
                        <div id="History" class="tabcontent" style="display: block;">
                            <div>Below, find all attempts to save data into eWay-CRM.</div>
                            <div class="content">
                                '.nl2br(file_get_contents(LOG_FILE)).'
                            </div>
                            <div style="min-height: 25px !important;"></div>
                        </div>
                        
                        <div id="Mapping" class="tabcontent">
                            <form onload="" method="post" >
                            Here you can map your fields for the form.
                            <div style="min-height: 60px !important;padding-top: 25px;display: flex;vertical-align: center;">
                                <div style="align-self: center;">WordPress Field <input name="wordpress" type="text"/></div>
                                <div style="align-self: center;padding-left: 30px;">eWay Field <input name="eway" type="text"/></div>
                                <div style="align-self: center;float: right;margin-left: 30px;"><input class="buttonStyle" style="float:right; background-color: #0062AF;height:32px;width:108px; color: white; border: none;" type="submit" name="'.ADD_FIELD.'" value="Add Field"/></div>
                                <div style="align-self: center;margin-left: auto;"><input class="buttonStyle" style="background-color: #0062AF;height:32px;width:150px; color: white; border: none;" type="submit" name="'.RESTORE_DEFAULT.'" value="Restore to Default"/></div>
                            </div>
                            <div class="content">
                                <table>
                                    <tr>
                                        <th>WordPress Field</th>
                                        <th>eWay Field</th>
                                    </tr>
                                '.getFields().'
                                </table>
                            </div>
                            <div style="min-height: 25px !important;"></div>
                            </form>
                        </div>
                            
                    </div>
                    
                    <script>
                        function openTab(tabName) {
                            var i, tabcontent, tablinks, btm;
                            btm = document.getElementsByClassName("bottom active");
                            btm[0].className = btm[0].className.replace(" active", "");
                            tabcontent = document.getElementsByClassName("tabcontent");
                            for (i = 0; i < tabcontent.length; i++){
                                tabcontent[i].style.display = "none";
                            }
                            tablinks = document.getElementsByClassName("tablinks");
                            for (i = 0; i < tablinks.length; i++) {
                                tablinks[i].className = tablinks[i].className.replace(" active", "");
                                if (tablinks[i].id == "btn" + tabName) {
                                    tablinks[i].className += " active";
                                    tablinks[i].childNodes[0].className += " active";
                                }
                            }
                            document.getElementById(tabName).style.display = "block";
                            location.hash = tabName;
                        }

                        jQuery(document).ready(function() {
                            if (location.hash != "") {
                                openTab(location.hash.substring(1));
                            }
                        });
                    </script>
                    ';
    
    echo $htmlResult;
}
else
{
     $htmlResult = '
                    <style>
                    
                    .buttonStyle {
                        float: right;
                        background-color: #0062AF;
                        height: 32px;
                        width: 108px;
                        color: white;
                        border: none;
                    }
                    
                    .buttonStyle:hover {
                        background-color: #004C89 !important;
                        cursor: pointer;
                    }
                    
                    .input {
                        padding: 0;
                        width: 100%;
                        height: 40px;
                        box-shadow: none !important;
                        border: 0 !important;
                        background: transparent !important;
                        border-bottom: 1px solid black !important;
                    }
                    
                    </style>
                    
                    <form action="?page='.ADMIN_PAGE.'" method="post" >   
                    <div style="display: flex;align-items: center;justify-content: center;">    
                    <div style="padding: 20px;display: inline-block;background-color: white;">
                    
                    <table>
                        <tbody>
                            <tr>
                                <td style="height: 55px; width: 94px; padding: 10px;">
                                    <object data="./../wp-content/plugins/contact-form-7-eway/eWayCRM-Logo-Red.svg" type="image/svg+xml" width="100%"></object>
                                </td>
                                <td>
                                    <h2 style="color: #E43025; padding-left: 30px;font-family: Segoe UI;"> '.TITLE.' </h2>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px;">
                                    <input class="input" type="text" name='.URL_FIELD.' placeholder="Web Service URL" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px;">
                                    <input class="input" type="text" name='.USER_FIELD.' placeholder="Username" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 10px;">
                                    <input class="input" type="password" name='.PWD_FIELD.' placeholder="Password" />
                                </td>
                            </tr>
                            <tr style="padding: 20px;">
                                <td>                   
                                </td>
                                <td style="padding: 10px;">
                                    <input class="buttonStyle" type="submit" name='.SUBMIT_FIELD.' value="Log In" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    </div>    
                    </div>
                    </form>';
    
    echo $htmlResult;
}
?>