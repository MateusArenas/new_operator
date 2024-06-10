var htmlEl = $('html');

var themeDarkBtn = $('#theme-dark');
var themeLightBtn = $('#theme-light');
var themeAutoBtn = $('#theme-auto');

var theme = localStorage.getItem('theme-schema');

if (!theme) {
  var attrTheme = htmlEl.attr('data-bs-theme')
  theme = attrTheme ? attrTheme : 'auto';
}

function clearAllThemeActiveClass () {
  themeDarkBtn.removeClass('active');
  themeLightBtn.removeClass('active');
  themeAutoBtn.removeClass('active');
}

function activeThemeDark () {
    htmlEl.attr('data-bs-theme', 'dark');
    localStorage.setItem('theme-schema', 'dark');
    clearAllThemeActiveClass();
    themeDarkBtn.addClass('active');
}

themeDarkBtn.on('click', function (e) {
    activeThemeDark();
});

function activeThemeLight () {
    htmlEl.attr('data-bs-theme', 'light');
    localStorage.setItem('theme-schema', 'light');
    clearAllThemeActiveClass();
    themeLightBtn.addClass('active');
}

themeLightBtn.on('click', function (e) {
    activeThemeLight();
});

function activeThemeAuto () {
    htmlEl.removeAttr('data-bs-theme');
    localStorage.setItem('theme-schema', 'auto');
    clearAllThemeActiveClass();
    themeAutoBtn.addClass('active');
}

themeAutoBtn.on('click', function (e) {
    activeThemeAuto();
});

switch (theme) {
  case 'dark':
    activeThemeDark();
    break;
  case 'light':
    activeThemeLight();
    break;
  default:
    activeThemeAuto();
    break;
}
