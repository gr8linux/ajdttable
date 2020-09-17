jQuery('#tblUtility').bootstrapTable({
    toggle:"table",
    height:"460",
    ajax:"utilAjaxRequest", ajaxOptions: "ajaxOptions",
    buttonsClass: 'success',
    showColumns: true,  showRefresh: true, showFullscreen: true, showToggle: true,
    pagination: true,  search: true, customSort: "customSort",
    columns: [
        { field: 'state', checkbox: true },
        { field: "Key", title: "ApiName" },
        { field: "TableName", title: "TableName" },
        { field: "MethodName", title: "MethodName" },
        { field: "SelectedColumn", title: "SelectedColumn" },
        { 
            field: "Url", title: "Url",
            formatter: function (value, row, index) {
              return "<a href='" + ajdt.rest.root + value +"' target='_blank'>"+ value +'</a>';
            },
        },
        {
            field: 'action',
            title: 'Actions',
            align: 'center',
            formatter: function () {
              return '<a href="javascript:" class="viewData" title="View Data"><i class="fa fa-eye"></i></a> | '+
              '<a href="javascript:" class="delData" title="Remove"><i class="fa fa-trash"></i></a>';
            },
            events: {
              'click .viewData': function (e, value, row) {
                alert(JSON.stringify(row))
              },
              'click .delData': function (e, value, row) {
                //alert(JSON.stringify(row))
                var toDelete = confirm("Do you want to delete API: " + row.Key + "?");
                if(toDelete){
                    jQuery.ajax({
                        type: "DELETE",
                        url: ajdt.rest.root + 'ajdt/v1/utility/' + row.Key,
                        success: function(data, textStatus, jqXHR) {
                            jQuery('#tblUtility').bootstrapTable('refresh');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('Failed to delete API '+jqXHR+textStatus+errorThrown);
                        }
                    });
                }
              }
            }
          }
    ]         
});

function utilAjaxRequest(params) { 
    var url = ajdt.rest.root + 'ajdt/v1/utility';
    tableData = [];
    jQuery.get(url + '?' + jQuery.param(params.data)).then(function (res) {
        jQuery.each( res, function( key, val ) {
             tableData.push({Key: key, TableName: val.TableName, MethodName: val.MethodName, 
                 SelectedColumn: val.SelectedColumn, Url: val.Url});
        });
        params.success(tableData);
    })
}

window.ajaxOptions = {
    beforeSend: function (xhr) {
        //xhr.setRequestHeader('Custom-Auth-Token', 'custom-auth-token')
        //console.log(xhr);
        xhr.setRequestHeader('X-WP-Nonce', ajdt.rest.nonce);
        //if (override) {
            //xhr.setRequestHeader('X-HTTP-Method-Override', override);
        //}
        if (beforeSend) {
            return beforeSend.apply(this, arguments);
        }
    }
}

function fetchBsApiList(ctrl){
    var button = jQuery(ctrl);
    var buttonText = button.html();
    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="false"></span>Loading...');
    jQuery('#tblUtility').bootstrapTable('refresh');
    button.html(buttonText);
}

jQuery( "#btnSaveApi" ).click(function() {
    var target = jQuery(this).closest('.modal');
    var apiname = target.find('#api-name').val();
    var httpMethod = target.find('#http-method').val();
    var tableName = target.find('#table-name').val();

    jQuery.ajax({
        type: "POST",
        url: ajdt.rest.root + 'ajdt/v1/utility',
        data: {
            'name' : apiname,
            'table' : tableName,
            'method' : httpMethod,
        },
        success: function(data, textStatus, jqXHR) {
            target.modal('hide');
            jQuery('#tblUtility').bootstrapTable('refresh');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Failed to save API '+jqXHR+textStatus+errorThrown);
        }
    });
});