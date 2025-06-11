document.addEventListener('DOMContentLoaded', function () {
  // Auto-hide flash messages after 5 seconds
  const flashMessages = document.querySelectorAll('.alert');

  flashMessages.forEach(function (message) {
      // Add fade-in animation
      message.style.opacity = '0';
      message.style.display = 'flex';

      setTimeout(function () {
          message.style.transition = 'opacity 0.5s ease';
          message.style.opacity = '1';
      }, 100);

      // Auto-dismiss after 5 seconds
      setTimeout(function () {
          dismissMessage(message);
      }, 5000);
  });

  // Add click event to close buttons
  const closeButtons = document.querySelectorAll('.alert-close');
  closeButtons.forEach(function (button) {
      button.addEventListener('click', function () {
          const message = this.closest('.alert');
          dismissMessage(message);
      });
  });

  // Function to dismiss messages with animation
  function dismissMessage(message) {
      message.style.opacity = '0';
      setTimeout(function () {
          message.style.display = 'none';
      }, 500);
  }
});