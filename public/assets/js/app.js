document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (alertEl) {
    setTimeout(function () {
      var alert = bootstrap.Alert.getOrCreateInstance(alertEl);
      alert.close();
    }, 5000);
  });
});
