const settings = document.querySelector("script[data-provenix]")?.dataset || {};
const toggle = document.querySelector(".menu-toggle");
const nav = document.querySelector("#navigation");
toggle?.addEventListener("click", () => {
  const open = toggle.getAttribute("aria-expanded") !== "true";
  toggle.setAttribute("aria-expanded", String(open));
  toggle.setAttribute("aria-label", open ? settings.menuClose : settings.menuOpen);
  nav.classList.toggle("is-open", open);
});
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && toggle?.getAttribute("aria-expanded") === "true") {
    toggle.click();
    toggle.focus();
  }
});
document.querySelectorAll("nav [data-page]").forEach((link) => {
  if (link.dataset.page === document.body.dataset.page) link.setAttribute("aria-current", "page");
});
document.querySelector("#contact-form")?.addEventListener("submit", (event) => {
  event.preventDefault();
  const data = new FormData(event.currentTarget);
  const body = `${settings.emailName} ${data.get("name")}\n${settings.emailAddress} ${data.get("email")}\n${settings.emailCompany} ${data.get("company") || settings.emailEmptyCompany}\n\n${data.get("message")}`;
  const subject = `${settings.emailSubject} ${data.get("topic")}`;
  window.location.href = `mailto:${encodeURIComponent(settings.contactEmail)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  const status = document.querySelector("#contact-status");
  status.hidden = false;
  status.textContent = settings.emailStatus.replaceAll("{email}", settings.contactEmail);
});
