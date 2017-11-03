(function($) {
  console.log( "ready!" );
  var details = $('#private-key-details'),
      detailsButton = $('#posting-key-link');
  // initalise the dialog
  details.dialog({
    title: 'Private Hosting Key',
    dialogClass: 'wp-dialog',
    autoOpen: false,
    draggable: false,
    width: 'auto',
    modal: true,
    resizable: false,
    closeOnEscape: true,
    position: {
      my: "center",
      at: "center",
      of: window
    },
    create: function(){
      $('.ui-dialog-titlebar-close').addClass('ui-button');
    },
    open: function(){
      $('.ui-widget-overlay').bind('click',function(){
        details.dialog('close');
      });
    }
  });
  // bind a button or a link to open the dialog
  detailsButton.click(function(e) {
    e.preventDefault();
    details.dialog('open');
  });
})(jQuery);
