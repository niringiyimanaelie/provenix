"""Wire repeatable services and article bodies into the original layout."""

from pathlib import Path
import re

root = Path(__file__).resolve().parents[1] / "squelettes"
services = (root / "services.html").read_text(encoding="utf8")
if "<BOUCLE_services" in services:
    raise SystemExit("Article loops are already installed.")
illustrations = []
for role in ("lineage", "analysis", "exploration"):
    row = re.search(
        r'<article class="service-row" id="' + role + r'">.*?</article>', services
    ).group()
    visual = re.search(
        r'(<div class="(?:service-art[^"]*|formula-art)">.*</div>)</article>', row
    ).group(1)
    illustrations.append(f"[(#ENV{{role}}|=={{{role}}}|oui){visual}]")
(root / "inc-service-illustration.html").write_text("\n".join(illustrations), encoding="utf8")
loop = """<BOUCLE_services(ARTICLES){id_rubrique=#VAL{service_catalogue}|provenix_section}{par num titre,id_article}>
<article class="service-row" id="[(#ID_ARTICLE|provenix_service_role)]"><div class="service-index">[(#ID_ARTICLE|provenix_champ{surtitre})]</div><div><h2>[(#ID_ARTICLE|provenix_champ{titre})]</h2><p>[(#ID_ARTICLE|provenix_champ{chapo})]</p>[(#ID_ARTICLE|provenix_service_list)]</div><INCLURE{fond=inc-service-illustration,role=#ID_ARTICLE|provenix_service_role}></article>
</BOUCLE_services>"""
services = re.sub(
    r'<article class="service-row" id="lineage">.*?</article><article class="service-row" id="analysis">.*?</article><article class="service-row" id="exploration">.*?</article>',
    lambda _: loop,
    services,
)
(root / "services.html").write_text(services, encoding="utf8")

home = (root / "sommaire.html").read_text(encoding="utf8")
cards = """<BOUCLE_features(ARTICLES){id_rubrique=#VAL{service_catalogue}|provenix_section}{par num titre,id_article}>
<article class="feature"><span class="feature-icon">[(#COMPTEUR_BOUCLE|provenix_service_icon)]</span><span class="feature-number">[(#COMPTEUR_BOUCLE|provenix_service_number)]</span><h3>[(#ID_ARTICLE|provenix_champ{soustitre})]</h3><p>[(#ID_ARTICLE|provenix_champ{descriptif})]</p><a href="[(#URL_PAGE{services})]#[(#ID_ARTICLE|provenix_service_role)]">[(#ID_ARTICLE|provenix_champ{nom_site})] <span>↗</span></a></article>
</BOUCLE_features>"""
home = re.sub(
    r'<article class="feature">.*?</article><article class="feature">.*?</article><article class="feature">.*?</article>',
    lambda _: cards,
    home,
    count=1,
)
home = home.replace(
    "<p>[(#VAL{sommaire_064}|provenix_texte)]</p><p>[(#VAL{sommaire_065}|provenix_texte)]</p>",
    "[(#VAL{home_story}|provenix_article_body)]",
)
(root / "sommaire.html").write_text(home, encoding="utf8")
about = (root / "about.html").read_text(encoding="utf8")
for role, nums in [("about_mission", [9, 10, 11]), ("about_today", [23, 24])]:
    old = "".join(f"<p>[(#VAL{{about_{n:03}}}|provenix_texte)]</p>" for n in nums)
    about = about.replace(old, f"[(#VAL{{{role}}}|provenix_article_body)]")
(root / "about.html").write_text(about, encoding="utf8")
print("Connected native service loops and full article bodies.")
