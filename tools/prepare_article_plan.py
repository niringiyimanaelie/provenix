"""Describe the native article structure; values are read from the live CMS at migration time."""

from pathlib import Path
import json
import urllib.request

root = Path(__file__).resolve().parents[1]
articles = []


def keys(prefix, *numbers):
    return [f"{prefix}_{number:03}" for number in numbers]


def article(role, section, label, fields):
    articles.append(
        {
            "role": role,
            "section": section,
            "label": label,
            "fields": {
                name: {
                    "keys": value if isinstance(value, list) else [value],
                    "separator": "\n\n" if name == "texte" else "\n",
                }
                for name, value in fields.items()
            },
        }
    )


def k(prefix, n):
    return keys(prefix, n)[0]


article(
    "home_intro",
    "home",
    "Homepage introduction",
    dict(
        surtitre=k("sommaire", 2),
        titre=k("sommaire", 3),
        soustitre=k("sommaire", 4),
        descriptif=k("sommaire", 5),
        chapo=k("sommaire", 6),
        ps=k("sommaire", 9),
    ),
)
article(
    "home_capabilities",
    "home",
    "Capabilities introduction",
    dict(
        surtitre=k("sommaire", 41),
        titre=k("sommaire", 42),
        soustitre=k("sommaire", 43),
        descriptif=k("sommaire", 44),
        chapo=k("sommaire", 45),
    ),
)
article(
    "home_story",
    "home",
    "Our approach",
    dict(
        surtitre=k("sommaire", 61),
        titre=k("sommaire", 62),
        soustitre=k("sommaire", 63),
        texte=keys("sommaire", 64, 65),
        nom_site=k("sommaire", 66),
    ),
)
article(
    "home_process",
    "home",
    "Process introduction",
    dict(surtitre=k("sommaire", 67), titre=k("sommaire", 68), nom_site=k("sommaire", 69)),
)
for role, n in [("connect", 70), ("understand", 73), ("explore", 76)]:
    article(
        "home_" + role,
        "home",
        "Process: " + role,
        dict(surtitre=k("sommaire", n), titre=k("sommaire", n + 1), texte=k("sommaire", n + 2)),
    )

article(
    "about_intro",
    "about",
    "About us introduction",
    dict(surtitre=k("about", 2), titre=k("about", 3), soustitre=k("about", 4), chapo=k("about", 5)),
)
article(
    "about_mission",
    "about",
    "Why we are building",
    dict(
        surtitre=k("about", 6),
        titre=k("about", 7),
        soustitre=k("about", 8),
        texte=keys("about", 9, 10, 11),
    ),
)
article(
    "about_values",
    "about",
    "Values introduction",
    dict(surtitre=k("about", 12), titre=k("about", 13)),
)
for role, n in [("evidence", 14), ("readability", 16), ("questions", 18)]:
    article(
        "about_" + role,
        "about",
        "Value: " + role,
        dict(titre=k("about", n), texte=k("about", n + 1)),
    )
article(
    "about_today",
    "about",
    "Where we are today",
    dict(
        surtitre=k("about", 20),
        titre=k("about", 21),
        soustitre=k("about", 22),
        texte=keys("about", 23, 24),
        nom_site=k("about", 25),
    ),
)

article(
    "services_intro",
    "services",
    "Services introduction",
    dict(
        surtitre=k("services", 2),
        titre=k("services", 3),
        soustitre=k("services", 4),
        chapo=k("services", 5),
    ),
)
for role, n, h in [("lineage", 6, 46), ("analysis", 15, 49), ("exploration", 27, 52)]:
    article(
        role,
        "service_catalogue",
        "Service: " + role,
        dict(
            surtitre=k("services", n),
            titre=k("services", n + 1),
            chapo=k("services", n + 2),
            texte=keys("services", n + 3, n + 4, n + 5),
            soustitre=k("sommaire", h),
            descriptif=k("sommaire", h + 1),
            nom_site=k("sommaire", h + 2),
        ),
    )
    articles[-1]["fields"]["texte"]["list"] = True
article(
    "services_closing",
    "services",
    "Services closing message",
    dict(
        surtitre=k("services", 36),
        titre=k("services", 37),
        texte=k("services", 38),
        nom_site=k("services", 39),
    ),
)

article(
    "contact_intro",
    "contact",
    "Contact introduction",
    dict(
        surtitre=k("contact", 2),
        titre=k("contact", 3),
        soustitre=k("contact", 4),
        chapo=k("contact", 5),
    ),
)
article(
    "contact_details",
    "contact",
    "Contact information",
    dict(
        surtitre=k("contact", 6),
        titre=k("contact", 7),
        soustitre=k("contact", 8),
        chapo=keys("contact", 10, 11),
    ),
)
article(
    "contact_question",
    "contact",
    "Conversation starter",
    dict(titre=k("contact", 12), texte=k("contact", 13)),
)

plan = {
    "sections": {
        "home": "Home",
        "about": "About Us",
        "services": "Services",
        "contact": "Contact",
        "service_catalogue": "Our services",
    },
    "articles": articles,
}
(root / "plugins/provenix_site/article-plan.json").write_text(
    json.dumps(plan, ensure_ascii=False, indent=2), encoding="utf8"
)
for page in ("home", "about", "services", "contact"):
    url = "http://localhost:8080/provenix/" + ("" if page == "home" else "spip.php?page=" + page)
    (root / "tmp" / f"articles-before-{page}.html").write_bytes(urllib.request.urlopen(url).read())
print(f"Prepared {len(articles)} native articles and captured current public pages.")
