"""One-time migration from the original website templates to SPIP CMS fields.

Run only against the original templates. Content is seeded separately by the
CLI-only PHP installer; public templates never fall back to these seed values.
"""
from pathlib import Path
from html.parser import HTMLParser
import html
import json
import re
import urllib.request

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugins' / 'provenix_site'
PLUGIN.mkdir(parents=True, exist_ok=True)
schema = {}
seed = {}
groups = {'sommaire': 'home', 'about': 'about', 'services': 'services', 'contact': 'contact'}
section_names = {'hero': 'Hero', 'page-hero': 'Page introduction', 'compatibility': 'SQL platforms', 'story-section': 'Our approach', 'section': 'Page content', 'values-section section': 'Our values', 'note-section': 'Closing message', 'cta-section': 'Call to action', 'section contact-section': 'Contact details and form'}

def add(key, value, group, section, label, field_type='text'):
    if key not in schema:
        schema[key] = {'key': key, 'group': group, 'section': section,
                       'label': label, 'type': field_type}
        seed[key] = value
    return f'[(#VAL{{{key}}}|provenix_texte)]'

add('contact_email', 'info@getprovenix.com', 'shared', 'Contact address', 'Public contact email', 'email')

class Extractor(HTMLParser):
    def __init__(self, source, name):
        super().__init__(convert_charrefs=True)
        self.source = source
        self.name = name
        self.group = groups.get(name, 'shared')
        self.offsets = [0]
        for match in re.finditer('\n', source): self.offsets.append(match.end())
        self.stack = []
        self.edits = []
        self.count = 0

    def position(self):
        line, col = self.getpos()
        return self.offsets[line - 1] + col

    def section(self):
        if self.name.startswith('inc-'):
            return {'inc-head': 'Search engine description', 'inc-header': 'Navigation',
                    'inc-footer': 'Footer', 'inc-cta': 'Call to action'}.get(self.name, self.name)
        for tag, attrs in reversed(self.stack):
            if tag == 'section': return section_names.get(attrs.get('class', ''), 'Page content')
        return 'Page title and accessibility'

    def field(self, raw, context='Text'):
        self.count += 1
        value = html.unescape(raw)
        if 'info@getprovenix.com' in value:
            return raw.replace('info@getprovenix.com', '[(#VAL{contact_email}|provenix_texte)]')
        key = f'{self.name.replace("-", "_")}_{self.count:03}'
        label = f'{context}: {value[:85]}'
        return add(key, value, self.group, self.section(), label,
                   'textarea' if len(value) > 100 else 'text')

    def handle_starttag(self, tag, attrs):
        attrs_dict = dict(attrs)
        raw = self.get_starttag_text()
        offset = self.position()
        for match in re.finditer(r'([\w-]+)="([^"]*)"', raw):
            attr, value = match.groups()
            if attr in ('placeholder', 'aria-label') or (tag == 'meta' and attrs_dict.get('name') == 'description' and attr == 'content'):
                replacement = self.field(value, attr.replace('-', ' ').capitalize())
                self.edits.append((offset + match.start(2), offset + match.end(2), replacement))
            elif attr == 'href' and value == 'mailto:info@getprovenix.com':
                self.edits.append((offset + match.start(2), offset + match.end(2), 'mailto:[(#VAL{contact_email}|provenix_texte)]'))
        if tag not in ('br', 'meta', 'input', 'img', 'link', 'hr', 'inclure'):
            self.stack.append((tag, attrs_dict))

    def handle_endtag(self, tag):
        for index in range(len(self.stack)-1, -1, -1):
            if self.stack[index][0] == tag:
                self.stack = self.stack[:index]
                break

    def handle_data(self, data):
        start = self.position()
        end = self.source.find('<', start)
        if end < 0: end = len(self.source)
        raw = self.source[start:end]
        # Existing SPIP date/URL tags remain executable template instructions.
        parts = re.split(r'(\[\(#.*?\)\]|#[A-Z_]+(?:\{[^}]*\})?)', raw)
        offset = start
        context = {'h1': 'Main heading', 'h2': 'Section heading', 'h3': 'Heading',
                   'p': 'Paragraph', 'a': 'Link label', 'label': 'Form label',
                   'option': 'Topic option', 'title': 'Browser title'}.get(self.stack[-1][0] if self.stack else '', 'Text')
        for part in parts:
            if not part.startswith(('[(', '#')) and re.search('[A-Za-z]', html.unescape(part)):
                left = len(part) - len(part.lstrip())
                text = part.strip()
                if text:
                    self.edits.append((offset + left, offset + left + len(text), self.field(text, context)))
            offset += len(part)

templates = list((ROOT / 'squelettes').glob('*.html'))
if any('|provenix_texte' in path.read_text(encoding='utf-8') for path in templates):
    raise SystemExit('Templates already migrated; refusing to overwrite CMS fields.')

for page in ('', 'spip.php?page=about', 'spip.php?page=services', 'spip.php?page=contact'):
    name = 'home' if not page else page.split('=')[1]
    (ROOT / 'tmp' / f'cms-before-{name}.html').write_bytes(urllib.request.urlopen('http://localhost:8080/provenix/' + page).read())

for path in templates:
    source = path.read_text(encoding='utf-8')
    parser = Extractor(source, path.stem)
    parser.feed(source)
    for start, end, value in sorted(parser.edits, reverse=True):
        source = source[:start] + value + source[end:]
    path.write_text(source, encoding='utf-8')

runtime = {
    'menu_open': ('Open navigation', 'Open-menu accessible label'),
    'menu_close': ('Close navigation', 'Close-menu accessible label'),
    'email_subject': ('Provenix enquiry:', 'Email subject prefix'),
    'email_name': ('Name:', 'Email name label'),
    'email_address': ('Email:', 'Email address label'),
    'email_company': ('Company:', 'Email company label'),
    'email_empty_company': ('Not provided', 'Empty company text'),
    'email_status': ('Your email app has been requested. If nothing opens, email {email} directly. Your message has not been sent by this website.', 'Contact form status message ({email} uses your public email)'),
}
for key, (value, label) in runtime.items():
    add(key, value, 'contact' if key.startswith('email_') else 'shared', 'Form behavior' if key.startswith('email_') else 'Navigation', label, 'textarea' if len(value)>100 else 'text')

(PLUGIN / 'content-schema.json').write_text(json.dumps(list(schema.values()), ensure_ascii=False, indent=2), encoding='utf-8')
(PLUGIN / 'initial-content.json').write_text(json.dumps(seed, ensure_ascii=False, indent=2), encoding='utf-8')
print(f'Prepared {len(schema)} CMS fields from {len(templates)} templates.')
