(function($) {

  function WP_Steem_Editor(args) {
    var args = $.extend({
      parentContainerSelector: null,
      containerSelector: null,
      editorContainerSelector: null,
      editorSelector: null,
      editorStatusSelector: null,
      tabSelector: null,
      tabText: 'Markdown'
    }, args);

    var $parentContainer = null;
    var $container =  null;
    var $editorContainerSelector = null;
    var $editor = null;
    var $editorSelectorId = args.editorSelector.replace('#', '');
    var $toolbar = $('<div />', {
      class: 'quicktags-toolbar'
    });
    var $tabs = null;
    var $tab = $('<button />', {
      type: 'button',
      id: 'content-markdown',
      class: 'wp-switch-editor switch-markdown',
      text: args.tabText,
      'data-wp-editor-id': $editorSelectorId
    });

    var init = function() {
      $parentContainer = $(args.parentContainerSelector);
      $container = $(args.containerSelector);
      $tabs = $parentContainer.find('.wp-editor-tabs');

      $container.children().css('padding', '5px 15px');
      $container.prepend($toolbar);

      $tabs.append($tab);
    };

    $(document).ready(function() {
      init();
    });

    $(document).on('click', args.tabSelector, function(event) {
      event.preventDefault();

      editor = tinyMCE.get(args.editorSelectorId);

      $(args.editorContainerSelector).hide();
      $(args.editorStatusSelector).hide();

      tinyMCE.DOM.removeClass('html-active');
      tinyMCE.DOM.removeClass('tmce-active');

      tinyMCE.DOM.addClass('wp-content-wrap', 'markdown-active');

      $container.show();
    });

    $(document).on('click', '#content-tmce, #content-html', function(event) {
      event.preventDefault();

      $container.hide();

      $(args.editorContainerSelector).show();
      $(args.editorStatusSelector).show();

      $parentContainer.removeClass('markdown-active');
    });
  }

  new WP_Steem_Editor({
    parentContainerSelector: '#wp-content-wrap',
    containerSelector: '#wp-steem-editor-container',
    editorContainerSelector: '#wp-content-editor-container',
    editorSelector: '#content',
    editorStatusSelector: '#post-status-info',
    tabSelector: '#content-markdown'
  });

})(jQuery);