jQuery('#tblUtility').bootstrapTable({
    toggle:"table",
    height:"460",
    ajax:"fetchRecords", 
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
                var toDelete = confirm("Do you want to delete API: " + row.Key + "?");
                if(toDelete){
                    new AJDT_API('utility').delete(row.Key)
                        .done( ( res, status, xhr ) => {
                            jQuery('#tblUtility').bootstrapTable('refresh');
                        }).fail( response => {
                            alert(JSON.stringify(response));
                        });
                }
              }
            }
          }
    ]         
});

function fetchRecords(params) { 
    tableData = [];
    new AJDT_API('utility').get().done(( resp, status, xhr ) => {
            jQuery.each( resp, function( key, val ) {
            tableData.push({Key: key, TableName: val.TableName, MethodName: val.MethodName, 
                SelectedColumn: val.SelectedColumn, Url: val.Url});
        });
        params.success(tableData);
    }).fail( resp => {
            alert(JSON.stringify(resp));
    });
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
    new AJDT_API('utility').post( 
        {
            'name' : apiname,
            'table' : tableName,
            'method' : httpMethod,
        }).done( ( res, status, xhr ) => {
            target.modal('hide');
            jQuery('#tblUtility').bootstrapTable('refresh');
        }).fail( response => {
            alert(JSON.stringify(response));
        });
});