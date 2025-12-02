// Keep this tiny. Only progressive enhancements live here.
// Example: Add a focus style to the body when using keyboard for a11y clarity.
(function () {
  let hadKeyboard = false;
  function onFirstTab(e) {
    if (e.key === 'Tab') {
      document.documentElement.classList.add('user-is-tabbing');
      hadKeyboard = true;
      window.removeEventListener('keydown', onFirstTab);
    }
  }
  window.addEventListener('keydown', onFirstTab);
})();
