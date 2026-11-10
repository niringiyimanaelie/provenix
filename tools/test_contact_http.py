"""Local-only end-to-end test of SPIP CVT and the loopback Mailpit inbox."""
import http.cookiejar
import json
import time
import uuid
from email import policy
from email.parser import BytesParser
import urllib.parse
import urllib.request
from html.parser import HTMLParser

BASE = "http://localhost:8080/provenix/contact"
INBOX = "http://127.0.0.1:8025/api/v1/messages"


class ContactForm(HTMLParser):
    def __init__(self):
        super().__init__()
        self.active = False
        self.action = None
        self.fields = {}

    def handle_starttag(self, tag, attributes):
        attributes = dict(attributes)
        if tag == "form" and attributes.get("id") == "contact-message-form":
            self.active = True
            self.action = attributes.get("action", "")
        elif self.active and tag == "input" and "name" in attributes:
            self.fields[attributes["name"]] = attributes.get("value", "")

    def handle_endtag(self, tag):
        if tag == "form":
            self.active = False


def inbox():
    with urllib.request.urlopen(INBOX, timeout=10) as response:
        return json.load(response)


def get_form(client):
    with client.open(BASE, timeout=20) as response:
        html = response.read().decode("utf-8")
    form = ContactForm()
    form.feed(html)
    assert form.action and "formulaire_action_args" in form.fields
    return form


def post(client, form, changes):
    values = dict(form.fields)
    values.update({
        "contact_name": "Browser Integration Visitor",
        "contact_email": "browser-visitor@example.invalid",
        "contact_company": "Local HTTP Test",
        "contact_topic": "general",
        "contact_message": "A real HTTP form submission captured locally for verification.",
        "contact_website": "",
    })
    values.update(changes)
    destination = urllib.parse.urljoin(BASE, form.action)
    assert urllib.parse.urlparse(destination).hostname == "localhost"
    request = urllib.request.Request(destination, data=urllib.parse.urlencode(values).encode())
    with client.open(request, timeout=25) as response:
        return response.read().decode("utf-8")


def main():
    start = inbox()["total"]  # Refuse to submit unless local capture is available.
    client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    form = get_form(client)
    html = post(client, form, {"contact_email": "invalid", "contact_name": '"><script>alert(1)</script>'})
    assert "Enter a valid email address." in html
    assert '"><script>alert(1)</script>' not in html
    assert inbox()["total"] == start
    print("PASS native validation, safe redisplay, and no mail for invalid submission")
    form = get_form(client)
    html = post(client, form, {"contact_ticket": "tampered"})
    assert "Refresh the page" in html
    assert inbox()["total"] == start
    print("PASS native POST rejects a tampered ticket")
    form = get_form(client)
    content = {"contact_message": "Local HTTP test " + uuid.uuid4().hex}
    html = post(client, form, content)
    assert "Your message has been submitted." in html
    assert 'id="contact-message-form"' not in html
    assert 'id="contact-form"' in html
    assert inbox()["total"] == start + 1
    message = inbox()["messages"][0]
    assert message["ReplyTo"][0]["Address"] == "browser-visitor@example.invalid"
    assert message["To"][0]["Address"] == "info@getprovenix.com"
    assert message["From"]["Address"] != "browser-visitor@example.invalid"
    with urllib.request.urlopen("http://127.0.0.1:8025/api/v1/message/" + message["ID"] + "/raw", timeout=10) as response:
        parsed = BytesParser(policy=policy.default).parsebytes(response.read())
    assert not parsed.defects
    for header in ("From", "To", "Reply-To", "Content-Type", "MIME-Version", "Message-ID"):
        assert len(parsed.get_all(header, [])) == 1, header
        assert not parsed[header].defects, (header, parsed[header].defects)
    assert len(parsed["Reply-To"].addresses) == 1
    assert parsed["Reply-To"].addresses[0].addr_spec == "browser-visitor@example.invalid"
    assert parsed["From"].addresses[0].addr_spec == "info@getprovenix.com"
    print("PASS SMTP delivery and raw MIME headers: one visitor Reply-To, business From, separate valid headers")
    html = post(client, form, content)
    assert "already been submitted" in html
    assert inbox()["total"] == start + 1
    print("PASS repeated HTTP POST does not send a second message")


if __name__ == "__main__":
    main()
