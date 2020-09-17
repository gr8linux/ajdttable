jQuery("div[id^='mount_']").each(function( i, elem ) {
  var apis = jQuery(elem).find('input').val().split(',');
  var defColumns = [];
  jQuery(elem).find('input[name=colNames]').val().split(',').forEach(function(col){
    defColumns.push({ field: col, title: col.toUpperCase() });
  });
  defColumns.push({ field: 'operate', title: 'Action', 
   clickToSelect: false, events: window.operateEvents, formatter: operateFormatter});

  jQuery(elem).find('table').bootstrapTable({
    toggle:"table",
    height:"460",
    ajax:"ajaxRequest", //ajaxOptions: "ajaxOptions",
    buttonsClass: 'warning',
    showColumns: true, pagination: true, search: true,
    columns: defColumns         
  });

});

function ajaxRequest(params) {
    var apiname = this.$el.attr('id').split('_')[1]; 
    var url = ajdt.rest.root + 'ajdt/v1/' + apiname;
    jQuery.get(url + '?' + jQuery.param(params.data)).then(function (res) {
        params.success(res)
    })
}
window.ajaxOptions = {
    beforeSend: function (xhr) {
      xhr.setRequestHeader('Custom-Auth-Token', 'custom-auth-token')
    }
  }

window.operateEvents = {
    // 'click .like': function (e, value, row, index) {
    //   alert('You click like action, row: ' + JSON.stringify(row))
    // },
    'click .remove': function (e, value, row, index) {
      $table.bootstrapTable('remove', {
        field: 'id',
        values: [row.id]
      })
    }
  }

function operateFormatter(value, row, index) {
    return [
      // '<a class="like" href="javascript:void(0)" title="Like">',
      // '<i class="fa fa-heart"></i>',
      // '</a>  ',
      '<a class="remove" href="javascript:void(0)" title="Remove">',
      '<i class="fa fa-trash"></i>',
      '</a>'
    ].join('')
  }