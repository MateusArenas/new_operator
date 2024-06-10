var htmlEl = $('html');

var theme = localStorage.getItem('theme-schema');

if (!theme) {
  var attrTheme = htmlEl.attr('data-bs-theme')
  theme = attrTheme ? attrTheme : 'auto';
}

switch (theme) {
  case 'dark':
    htmlEl.attr('data-bs-theme', 'dark');
    localStorage.setItem('theme-schema', 'dark');
    break;
  case 'light':
    htmlEl.attr('data-bs-theme', 'light');
    localStorage.setItem('theme-schema', 'light');
    break;
  default:
    htmlEl.removeAttr('data-bs-theme');
    localStorage.setItem('theme-schema', 'auto');
    break;
}
