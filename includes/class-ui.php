<?php

function ajdt_list_api() { ?>
  <section class="ajdt-message-wrapper">
    <div class="ajdt-inbox-message">
        <div >
          <h5 class="ajdt-message-title">Ajax Table creates REST API for the selected database tables. 
            It enables user to perform CRUD operations in the table using generated REST API</h5>
          <div class="woocommerce-inbox-message__text">
              <span>Simple Table Manager enables editing table records and exporting them to CSV files through a minimal database interface from your dashboard.
              <ul>
                <li>Simply CRUD table contents on your wp-admin screen</li>
                <li>Search and sort table records</li>
                <li>No knowledge of MySQL or PHP required</li>
                <li>Export table records to a CSV file</li>
                <li>Does not allow users to change the structure of the table</li>
                <li>Unlike ‘full featured’ database management plugins, it does not allow users to 
                alter the structure of the table but requires no knowledge on MySQL or PHP.</li>
              </ul>              
              </span>
          </div>
        </div>
    </div>
  </section>
<div id='toolbar'>
  <div class="form-inline" role="form">
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#saveApiModal" id="btnCreate"><i class='fa fa-plus-square'></i> Create</button>
    <button type="button" class="btn btn-info" onclick='fetchBsApiList(this)'><i class="fa fa-retweet"></i> Refresh</button>
  </div>
  <table id='tblUtility'></table>
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
            <select class="form-control" id="http-method">  
                <option value='GET'>GET</option>
            </select>
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="btnSaveApi" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
<?php }

function ajdt_list_api1() {  ?>
 <div class="wrap">
  <?php 
  // print "Checking SCRIPT_DEBUG: ".defined( 'SCRIPT_DEBUG' ).", Value: ".SCRIPT_DEBUG;
  // print "Checking AJDT_INCLUDES: ".defined( 'AJDT_INCLUDES' ).", Value: ".AJDT_INCLUDES;
  // echo do_shortcode("[AJDT api='sha1']");
   ?>
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
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#saveApiModal" id="btnCreate">Create</button>
        <button type="button" class="btn btn-info" onclick='fetchApiList(this)'>Refresh</button>
        <div class="form-horizontal">
            <div class="table-wrapper">
              <table id="tbApiList" class="widefat fixed striped">
                <thead><tr><th>ShortCode/Name</th><th>Method</th><th>Table</th>
                <th>Columns</th><th>URL</th><th>Action</th></tr></thead>
                <tbody>
                <?php //data-backdrop="" 
                foreach (get_option(APILISTNAME) as $key => $Api) {
                    $method = $Api['MethodName'];
                    $table = $Api['TableName'];
                    $cols = $Api['SelectedColumn'];
                    $url = $Api['Url'];
                    $fullURL = get_site_url().'/wp-json/'.$url;
                    echo "<tr><td class='apiname'>[$key]</td>
                    <td class='method'>$method</td>
                    <td class='table'>$table</td>
                    <td class='cols'>$cols</td>
                    <td class='url'><a class='fas fa-user-edit' href='$fullURL' target='_blank'>$url</a></td>
                    <td><button class='btn btn-warning' style='font-size: 12px;' onclick='deleteApi(this)'>Delete</button></td></tr>";
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
            <select class="form-control" id="http-method">  
                <option value='GET'>GET</option>
            </select>
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="btnSaveApi" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
<?php 
ajdt_list_api1();
} ?>
