"""Compare public DOM/text against the captured pre-CMS pages (no browser needed)."""
from pathlib import Path
from html.parser import HTMLParser
import urllib.request
import difflib
import sys

ROOT = Path(__file__).resolve().parents[1]
baseline = sys.argv[1] if len(sys.argv) > 1 else 'cms'
class Snapshot(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.events = []
        self.script = False
    def handle_starttag(self, tag, attrs):
        if tag == 'script':
            self.script = True
            # CMS configuration attributes do not affect visual output.
            attrs = [(k,v) for k,v in attrs if not k.startswith('data-')]
        self.events.append(('start', tag, sorted(attrs)))
    def handle_endtag(self, tag):
        if tag == 'script': self.script = False
        self.events.append(('end', tag))
    def handle_data(self, text):
        if text.strip() and not self.script:
            self.events.append(('text', text.strip()))

failed = False
for name in ('home', 'about', 'services', 'contact'):
    url = 'http://localhost:8080/provenix/' + ('' if name == 'home' else f'spip.php?page={name}')
    current = urllib.request.urlopen(url + ('&' if '?' in url else '?') + 'var_mode=recalcul').read().decode('utf-8')
    (ROOT / 'tmp' / f'cms-after-{name}.html').write_text(current, encoding='utf-8')
    before = Snapshot(); before.feed((ROOT / 'tmp' / f'{baseline}-before-{name}.html').read_text(encoding='utf-8'))
    after = Snapshot(); after.feed(current)
    if before.events != after.events:
        failed = True
        print(f'FAIL {name}:')
        print('\n'.join(list(difflib.unified_diff([str(x) for x in before.events], [str(x) for x in after.events]))[:45]))
    else: print(f'PASS {name}: identical visible text, markup, and styling hooks')
if failed: raise SystemExit(1)
