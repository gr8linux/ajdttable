//https://examples.bootstrap-table.com/#
jQuery("div[id^='mount_']").each(function( i, elem ) {
  var apis = jQuery(elem).find('input').val().split(',');
  var table = jQuery(elem).find('table');
  var defColumns = [  { field: 'id', title: 'Item ID' }, 
              { field: 'name', title: 'Item Name' }, 
              { field: 'price', title: 'Item Price' },
              {
                  field: 'operate',
                  title: 'Item Operate',
                  align: 'center',
                  clickToSelect: false,
                  events: window.operateEvents,
                  formatter: operateFormatter
              }
          ];

  jQuery(table).bootstrapTable({
  toggle:"table",
  height:"460",
  ajax:"ajaxRequest",
  //ajaxOptions: "ajaxOptions",
  buttonsClass: 'success',
  showColumns: true,
  pagination: true,
  search: true,
  customSort: "customSort",
  columns: defColumns         
  });

});

function ajaxRequest(params) {
    var apiname = this.$el.attr('id').split('_')[1]; 
    alert(apiname);
    var url = 'https://examples.wenzhixin.net.cn/examples/bootstrap_table/data';
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
    'click .like': function (e, value, row, index) {
      alert('You click like action, row: ' + JSON.stringify(row))
    },
    'click .remove': function (e, value, row, index) {
      $table.bootstrapTable('remove', {
        field: 'id',
        values: [row.id]
      })
    }
  }

function operateFormatter(value, row, index) {
    return [
      '<a class="like" href="javascript:void(0)" title="Like">',
      '<i class="fa fa-heart"></i>',
      '</a>  ',
      '<a class="remove" href="javascript:void(0)" title="Remove">',
      '<i class="fa fa-trash"></i>',
      '</a>'
    ].join('')
  }
//   const fetchItems = () => {
//                 this.isBusy = true;
//                 var url = window.location.href.split("wp-admin")[0] + 'wp-json/ajdt/v1/' + 'sha4';
//                 fetch(url).then((response) => {
//                     //console.log(response);
//                     this.isBusy = false;
//                     return response.json()
//                 }).then((data)=>{
//                     //console.log(data);
//                     this.dataValues = data;
//                 });
//             };