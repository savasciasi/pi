document.addEventListener('DOMContentLoaded', () => {
  const navbar = document.querySelector('.navbar');
  const updateNavbarState = () => {
    if (!navbar) return;
    if (window.scrollY > 50) {
      navbar.classList.add('navbar-scrolled');
    } else {
      navbar.classList.remove('navbar-scrolled');
    }
  };

  updateNavbarState();
  document.addEventListener('scroll', updateNavbarState);

  const yearHolder = document.querySelector('[data-current-year]');
  if (yearHolder) {
    yearHolder.textContent = new Date().getFullYear().toString();
  }
});
