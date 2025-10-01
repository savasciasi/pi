document.querySelectorAll('[data-modal]').forEach((trigger) => {
  trigger.addEventListener('click', () => {
    const modal = document.getElementById(trigger.dataset.modal);
    if (modal) {
      modal.classList.add('active');
    }
  });
});

document.querySelectorAll('.modal').forEach((modal) => {
  modal.addEventListener('click', (event) => {
    if (event.target === modal || event.target.hasAttribute('data-close')) {
      modal.classList.remove('active');
    }
  });
});

const nav = document.querySelector('.main-nav');
const hero = document.querySelector('.hero');
if (nav && hero) {
  const toggleShadow = () => {
    if (window.scrollY > hero.offsetHeight - nav.offsetHeight) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  };
  document.addEventListener('scroll', toggleShadow);
  toggleShadow();
}
