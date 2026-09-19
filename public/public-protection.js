(function () {
  document.addEventListener('contextmenu', function (event) {
    event.preventDefault();
  });

  document.addEventListener('keydown', function (event) {
    const key = event.key.toLowerCase();
    const blockedShortcut = event.key === 'F12'
      || (event.ctrlKey && event.shiftKey && ['i', 'j', 'c'].includes(key))
      || (event.ctrlKey && key === 'u');

    if (blockedShortcut) {
      event.preventDefault();
      event.stopPropagation();
    }
  }, true);
})();
