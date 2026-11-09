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

// Follow actual card geometry when text wraps, fonts load, or the viewport changes.
document.querySelectorAll("[data-lineage-demo]").forEach((demo) => {
  const stages = demo.querySelector(".lineage-stages");
  const connector = demo.querySelector(".lineage-arrows");
  const path = connector?.querySelector("path");
  const sources = [...demo.querySelectorAll(".lineage-source-stage .data-node")];
  const destination = demo.querySelector(".lineage-transform-stage .data-node");
  if (!stages || !connector || !path || sources.length === 0 || !destination) return;

  const updateConnectors = () => {
    const bounds = stages.getBoundingClientRect();
    if (!bounds.width || !bounds.height) return;
    const target = destination.getBoundingClientRect();
    const targetX = target.left - bounds.left;
    const targetY = target.top - bounds.top + target.height / 2;
    const nodes = sources.map((node) => node.getBoundingClientRect());
    const sourceX = Math.max(...nodes.map((node) => node.right)) - bounds.left;
    const joinX = (sourceX + targetX) / 2;
    const sourceYs = nodes.map((node) => node.top - bounds.top + node.height / 2);
    connector.setAttribute("viewBox", `0 0 ${bounds.width} ${bounds.height}`);
    path.setAttribute("d", [
      ...sourceYs.map((y) => `M ${sourceX} ${y} H ${joinX}`),
      `M ${joinX} ${Math.min(...sourceYs, targetY)} V ${Math.max(...sourceYs, targetY)}`,
      `M ${joinX} ${targetY} H ${targetX}`,
      `M ${targetX - 5} ${targetY - 4} L ${targetX} ${targetY} L ${targetX - 5} ${targetY + 4}`,
    ].join(" "));
  };

  updateConnectors();
  if (window.ResizeObserver) {
    const observer = new ResizeObserver(updateConnectors);
    [stages, ...sources, destination].forEach((node) => observer.observe(node));
  } else {
    window.addEventListener("resize", updateConnectors);
  }
  document.fonts?.ready.then(updateConnectors);
});
