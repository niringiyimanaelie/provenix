const toggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('#navigation');
toggle?.addEventListener('click', () => {
  const open = toggle.getAttribute('aria-expanded') !== 'true';
  toggle.setAttribute('aria-expanded', String(open));
  toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
  nav.classList.toggle('is-open', open);
});
document.addEventListener('keydown', event => {
  if (event.key === 'Escape' && toggle?.getAttribute('aria-expanded') === 'true') {
    toggle.click(); toggle.focus();
  }
});
document.querySelectorAll('nav [data-page]').forEach(link => {
  if (link.dataset.page === document.body.dataset.page) link.setAttribute('aria-current', 'page');
});
document.querySelector('#contact-form')?.addEventListener('submit', event => {
  event.preventDefault();
  const data = new FormData(event.currentTarget);
  const body = `Name: ${data.get('name')}\nEmail: ${data.get('email')}\nCompany: ${data.get('company') || 'Not provided'}\n\n${data.get('message')}`;
  const subject = `Provenix enquiry: ${data.get('topic')}`;
  window.location.href = `mailto:info@getprovenix.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  const status = document.querySelector('#contact-status');
  status.hidden = false;
  status.textContent = 'Your email app has been requested. If nothing opens, email info@getprovenix.com directly. Your message has not been sent by this website.';
});
