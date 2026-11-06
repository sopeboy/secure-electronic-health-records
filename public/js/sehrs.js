// Small helpers. Kept in a file (not inline) so the Content-Security-Policy can block inline scripts.
document.addEventListener('submit', function (event) {
    var form = event.target;
    var message = form.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
