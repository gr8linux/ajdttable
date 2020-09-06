<?php
function getTables(){
    global $wpdb;
    $sql = "SHOW TABLES LIKE '%%'";
    return $wpdb->get_results($sql);
}

// $val = $this->container['generalutil']->getTables();
// print_r($val);

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
            ajdt_list_api();
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

function ajdt_list_api() { 
    $apiList = get_option('AJDT_API_LIST'); ?>
 <div class="wrap">

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>
        <div  class="postbox"> 
            <div class="rowLayout">
                <p> Display the details about APIs </p>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#saveApiModal">Create</button>
                <button type="button" class="btn btn-info" onclick='fetchApiList()'>Refresh</button>

                <div class="form-horizontal">
                    <div class="box-body table-responsive sm" style="overflow-y: auto">
                        <table id="tbApiList" class="wp-list-table widefat fixed striped products tableStyle">
                            <thead><tr><th>Name</th><th>Method</th><th>Table</th><th>Columns</th><th>URL</th><th>Action</th></tr></thead>
                            <tbody>
                            <?php //data-backdrop="" 
                            foreach ($apiList as $key => $Api) {
                                $method = $Api['MethodName'];
                                $table = $Api['TableName'];
                                $cols = $Api['SelectedColumn'];
                                $url = $Api['Url'];
                                $fullURL = get_site_url().'/'.$url;
                                echo "<tr><td class='apiname'>$key</td><td class='method'>$method</td><td class='table'>$table</td><td class='cols'>$cols</td><td class='url'><a class='fas fa-user-edit' href='$fullURL' target='_blank'>$url</a></td>
                                <td><button type='button' style='font-size: 12px;' class='btn btn-success' data-toggle='modal' data-target='#saveApiModal' >Edit</button>&nbsp
                                    <button class='btn btn-warning' style='font-size: 12px;' onclick='deleteApi(this)'>Delete</button></td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
</div>
<div class="modal fade bd-example-modal-lg" id="saveApiModal" tabindex="-1" role="dialog" 
aria-labelledby="saveApiModalLabel" aria-hidden="true" >       
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="saveApiModalLabel">Create New API</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="api-name" class="col-form-label">Api Name:</label>
            <input type="text" class="form-control" id="api-name">
          </div>
          <div class="form-group">
            <label for="http-method" class="col-form-label">Http Method:</label>
            <input type="text" class="form-control" id="http-method">
          </div>
        <div class="form-group">
            <label for="table-name" class="col-form-label">Table Name:</label>
            <select class="form-control" id="table-name">
            <?php
                $tables = getTables();
                foreach ($tables as $index => $tableSet) {
                    foreach ($tableSet as $table) {
                        echo "<option value='$table'>$table</option>";
                    }
                }
             ?>
            </select>
          </div>
          <div class="form-group">
            <label for="cols" class="col-form-label">Columns:</label>
            <input type="text" class="form-control" id="cols">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="btnSaveApi" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
<?php  } ?>
