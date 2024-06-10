
/*------------------------------------------
  Resize textarea based on content  
------------------------------------------*/
function correctTextareaHight(element)
{
  var self = $(element),
      outerHeight = self.outerHeight(),
      innerHeight = self.prop('scrollHeight'),
      borderTop = parseFloat(self.css("borderTopWidth")),
      borderBottom = parseFloat(self.css("borderBottomWidth")),
      combinedScrollHeight = innerHeight + borderTop + borderBottom;
  
  if(outerHeight < combinedScrollHeight )
  {
    self.height(combinedScrollHeight);
  }
}



/*------------------------------------------
  Run syntax hightlighter  
------------------------------------------*/
function hightlightSyntax(editor){ 
    var me  = $(editor);
    var content = me.val();

    var parent = me.parent().first();

    var textareaHolder = parent.find('textarea');

    var codeHolder = parent.find('code');

    
    var escaped = escapeHtml(content);
    
    codeHolder.html(escaped);
    
    codeHolder.removeAttr('data-highlighted');
    
    hljs.highlightBlock(codeHolder.get(0));
    
    // $('.syntax-highight').each(function(i, block) {
      //   hljs.highlightBlock(block);
      // });

    setTimeout(() => {
      // correção para texto maior do que o normal
      let codeWidth = codeHolder.outerWidth();
      if (!codeWidth) codeWidth = codeHolder.width();
      
      textareaHolder.width(codeWidth);
    }, 300);
}


/*------------------------------------------
  String html characters
------------------------------------------*/
function escapeHtml(unsafe) {
  return unsafe
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

/*------------------------------------------
  Render existing code
------------------------------------------*/
$(document).on('ready', function(){
  $('.editor').each(function () {
    hightlightSyntax(this);
  })
  
  emmet.require('textarea').setup({
    pretty_break: true,
    use_tab: true
  });
});


/*------------------------------------------
  Enable tabs in textarea
------------------------------------------*/
$(document).delegate('.allow-tabs', 'keydown', function(e) {
  var keyCode = e.keyCode || e.which;

  if (keyCode == 9) {
    e.preventDefault();
    var start = $(this).get(0).selectionStart;
    var end = $(this).get(0).selectionEnd;

    // set textarea value to: text before caret + tab + text after caret
    $(this).val($(this).val().substring(0, start)
                + tabCharacter
                + $(this).val().substring(end));

    // put caret at right position again
    $(this).get(0).selectionStart =
    $(this).get(0).selectionEnd = start + tabOffset;
  }
});


/*------------------------------------------
Capture text updates
------------------------------------------*/
$(document).on('ready load keyup keydown change', '.editor', function(){

  hightlightSyntax(this);

  setTimeout(() => {
    correctTextareaHight(this);
  }, 300);
});

