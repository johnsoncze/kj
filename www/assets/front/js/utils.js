// Form validation function
function validateForm(formId) {
  var submit = true;
  var found = false;
  $('#'+formId).find('input[required]').each(function(el){
    if(!Boolean(($(this).val() || '').trim())) {
      const content = $(this).attr('data-nette-rules') || ''
      if(!$(this).parent().find('.Form-label.Form-label--info').length) {
        // Extract message from data-nette-rules
        const message = content
          .replace(/},{.*$/, '')
          .replace(/^.*filled"/, '')
          .replace(/}.*/, '')
          .replace(/^.*:/,'')
          .replace(/\"/g, '')
        var temp = $('<div class="Form-label Form-label--info u-desktop-hide">'+message+'</div>').insertBefore($(this))
        if (!found) {
          found = true;
          temp[0].scrollIntoView();
        }
      } else {
        $(this).parent().find('.Form-label.Form-label--info')[0].scrollIntoView();
      }
      submit = false;
    } else {
      $(this).parent().find('.Form-label.Form-label--info').remove()
    }
  })

  return submit;
}
