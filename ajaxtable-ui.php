<?php

function custom_api_wp_register_ui()
{
    if (isset($_GET)) {
        // $tab = $_GET['action'];
        if (isset($_GET['action'])) {
                $act = $_GET['action'];
print("action: $act<br>");
print_r($_GET);
            print("$action = CleanUp($ _GET['action'])");
            if ($action == 'addapi') {
// print("<br>action: $act<br>");
                print("CreateApi()");
            } elseif ($action == 'delete') {
                if (isset($_GET['api'])) {
                    print("$api = CleanUp($ _GET['api']))");
                    // print("<br>action: $act<br>");
                }
                print("custom_api_wp_delete_api($api)");
            } elseif ($action == 'edit') {
                if (isset($_GET['api'])) {
                    $api = CleanUp($_GET['api']);
                    // print("<br>action: $act<br>"); 
                }
                print("custom_api_wp_edit_api($api)");
            }
        } else
            //custom_api_wp_list_api1();
            custom_api_wp_list_api();
    }
}


function custom_api_wp_list_api1(){
    //print("custom_api_wp_list_api1 : AJDT_API_LIST <br>");
    $list["sha1"] = array(
                        "TableName" => 'wp55_ajdt_utils',
                        "MethodName" => 'GET',
                        "SelectedColumn" => 'name,age,email',
                        "ConditionColumn" => '',
                        "SelectedCondtion" => 'no condition',
                        "SelectedParameter" => 1,
                        "query" => 'Select name,age,email from wp55_ajdt_utils;'
                );
    $list["sha2"] = array(
                        "TableName" => 'wp55_wc_admin_notes',
                        "MethodName" => 'GET',
                        "SelectedColumn" => 'name,type,title',
                        "ConditionColumn" => '',
                        "SelectedCondtion" => 'no condition',
                        "SelectedParameter" => 1,
                        "query" => 'Select name,type,title from wp55_wc_admin_notes;'
                );

    

    update_option('AJDT_API_LIST', $list);
    //print("Added to API_List <br>");
    $list = get_option('AJDT_API_LIST');
    //print("Getting from API_List <br>");
    //print_r($list);
}

function custom_api_wp_list_api()
{ ?>
    <div class="wrap" style=" margin:15px 20px 0 2px;">
        <h3 class="HeadName">CUSTOM API PLUGIN</h3>
        <hr>
        <!-- </div> -->
        <div class="box-body" style="margin-top: 15px;">
        <?php
        $url = admin_url('/admin.php?page=ajdtsettings&action=addapi');
        echo "<a href ={$url}>";
        ?>
            <button class='btn btn-success' style="margin-bottom: 15px; margin-left: 10px;">
                <span style="color:white"> Create API</span><i class='fas fa-user-edit'></i></button></a>
        </div>
        <div class="box-body">
            <div class="form-horizontal">
                <div class="box-body" style="margin-top:10px;margin-right:0px">
                    <div class="row" style="padding: unset;margin-left: 5px;">
                        <div class="col-md-7" style="background-color: white;">
                            <div class="box-body table-responsive sm" style="overflow-y: auto">
                                <table id="tbldata" class="table table-hover" style="width: 400px;margin: 5px;">
                                    <thead>
                                        <tr class="header">
                                            <th style="display:none">RowId</th>
                                            <th>API NAME</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbodyid">
                                        <?php
                                        if (get_option('AJDT_API_LIST')) {
                                            $list = get_option('AJDT_API_LIST');
                                            //print_r($list);
                                            foreach ($list as $key => $value) {
                                                echo "<tr>";
                                                echo " <td>" . $key . "</td>";
                                                echo "<td> <button class='btn btn-primary' style='font-size: 12px;' onclick = 'custom_api_wp_edit(this)'>Edit<i class='fas fa-user-edit'></i></button>&nbsp
                        <button class='btn btn-warning' style='font-size: 12px;' onclick ='custom_api_wp_delete(this)'>Delete<i class='fas fa-user-edit'></i></button>
                        </td>";
                                            }
                                        }
                                        ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
