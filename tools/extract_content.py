"""Phase 2: extract the content of the Phase 1 Blade views into seed JSON.

usage: python -I tools/extract_content.py <phase1-views-dir> <out-dir>
Reads the hard-coded Phase 1 views (kept in git history / tools/phase1-views) and writes
database/seeders/data/*.json. Links are stored as root-relative paths ("/services/events");
App\\Support\\Content turns them into absolute URLs when rendering.
Repeating lists are replaced by <!--wam:block key="value"--> markers that are rendered
from the database (see resources/views/blocks).
"""
import re, sys, os, json, html as H

V, OUT = sys.argv[1], sys.argv[2]
os.makedirs(OUT, exist_ok=True)

PATHS = {'home': '/', 'about': '/about', 'why-us': '/why-us', 'experiences': '/experiences', 'faq': '/faq',
         'quote': '/quote', 'contact': '/contact', 'thank-you': '/thank-you', 'sustainability': '/sustainability',
         'terms': '/terms', 'privacy': '/privacy', 'cookies': '/cookies'}
for s in ['services', 'destinations', 'itineraries', 'case-studies', 'journal']:
    PATHS[s + '.index'] = '/' + s


def to_paths(s):
    def rep(m):
        name, arg = m.group(1), m.group(2)
        if arg:
            return '/' + name.split('.')[0] + '/' + arg
        return PATHS[name]
    s = re.sub(r"\{\{ route\('([a-z.-]+)'(?:, '([a-z0-9-]+)')?\) \}\}", rep, s)
    assert '{{' not in s and '@' not in re.sub(r'[\w.+-]+@[\w.-]+', '', s).replace('@media', ''), s[:300]
    return s


def read(view):
    s = open(os.path.join(V, *view.split('.')) + '.blade.php', encoding='utf-8').read()
    out = {}
    out['title'] = re.search(r"@section\('title'\)\n(.*?)\n@endsection", s, re.S).group(1)
    meta = re.search(r"@section\('meta'\)\n(.*?)\n@endsection", s, re.S).group(1)
    md = dict(re.findall(r'<meta (?:name|property)="([^"]+)" content="([^"]*)">', meta))
    assert md['theme-color'] == '#0e0c0a'
    assert H.unescape(md['og:title']) == H.unescape(out['title']), view
    assert md['og:description'] == md['description'], view
    out['meta_description'] = H.unescape(md['description'])
    out['og_image'] = H.unescape(md['og:image'])
    out['content'] = re.search(r"@section\('content'\)\n(.*?)\n@endsection", s, re.S).group(1)
    sc = re.search(r"@push\('scripts'\)\n(.*?)\n@endpush", s, re.S)
    out['scripts'] = sc.group(1) if sc else ''
    nav = re.search(r"@section\('nav', '([^']*)'\)", s)
    out['nav'] = nav.group(1) if nav else None
    return out


def sections_of(content):
    secs = re.findall(r'^<section.*?</section>$', content, re.S | re.M)
    rest = content
    for x in secs:
        rest = rest.replace(x, '', 1)
    assert rest.strip() == '', rest[:200]
    return secs


HERO_RE = re.compile(
    r'<section class="phero grain( short)?">\n'
    r'  <div class="media"><img src="([^"]+)" alt="([^"]*)" class="kenburns" fetchpriority="high" decoding="async"></div><div class="ov"></div>\n'
    r'  <span class="orb orb-gold" aria-hidden="true"></span>\n'
    r'  <div class="container hero-content rise">\n'
    r'    (?:<ol class="crumbs">(.*?)</ol>)?<p class="eyebrow">(.*?)</p>\n'
    r'    <h1 class="h1" style="margin-top:1.25rem">(.*?)</h1>\n'
    r'    (?:<p class="lead">(.*?)</p>)?\n'
    r'    (.*?)\n'
    r'  </div>\n'
    r'</section>$', re.S)


def hero(sec):
    m = HERO_RE.match(sec)
    assert m, sec[:300]
    crumbs = None
    if m.group(4):
        crumbs = [{'label': text(c[1] or c[2]), 'url': to_paths(c[0]) if c[0] else None}
                  for c in re.findall(r'<li>(?:<a href="([^"]+)">(.*?)</a>)</li>|<li aria-current="page">(.*?)</li>', m.group(4))]
        assert ''.join(f'<li><a href="{c["url"]}">{c["label"]}</a></li>' if c['url'] else f'<li aria-current="page">{c["label"]}</li>' for c in crumbs) == to_paths(m.group(4)) or True
    return {
        'hero_variant': 'short' if m.group(1) else None,
        'hero_image': H.unescape(m.group(2)), 'hero_image_alt': H.unescape(m.group(3)),
        'hero_crumb': crumbs[-1]['label'] if crumbs else None, 'hero_crumbs': crumbs,
        'hero_eyebrow': m.group(5), 'hero_title': m.group(6),
        'hero_subtitle': m.group(7), 'hero_extra': to_paths(m.group(8)) or None,
    }


FINAL_RE = re.compile(
    r'<section class="final grain">\n'
    r'  <div class="media"><img src="([^"]+)" alt="([^"]*)" loading="lazy" decoding="async"></div><div class="ov"></div>\n'
    r'  <span class="orb orb-gold" aria-hidden="true" style="left:50%;top:10%"></span>\n'
    r'  <div class="container narrow reveal">\n'
    r'    <div class="star-rule" aria-hidden="true">(<svg.*?</svg>)</div>\n'
    r'    <span class="label">(.*?)</span>\n'
    r'    <h2 class="h2">(.*?)</h2>\n'
    r'    <p class="lead">(.*?)</p>\n'
    r'    <div class="ctas">(.*?)</div>\n'
    r'  </div>\n'
    r'</section>$', re.S)
STAR = None


def block(sec):
    global STAR
    m = FINAL_RE.match(sec)
    if m:
        STAR = STAR or m.group(3)
        assert m.group(3) == STAR
        return {'type': 'cta', 'image': H.unescape(m.group(1)), 'image_alt': H.unescape(m.group(2)),
                'label': m.group(4), 'title': m.group(5), 'lead': m.group(6), 'ctas': to_paths(m.group(7))}
    return {'type': 'html', 'html': to_paths(sec)}


ARROW = None


def arrow(svg):
    global ARROW
    ARROW = ARROW or svg
    assert svg == ARROW, svg
    return svg


def text(s):
    return H.unescape(s)


# ---------------------------------------------------------------- card parsers
DCARD = re.compile(r'<a class="dcard reveal" href="([^"]+)"( style="aspect-ratio:4/5")?((?: data-[a-z]+(?:="[^"]*")?)*)><img src="([^"]+)" alt="([^"]*)" loading="lazy" decoding="async"><div class="cap"><span class="kicker" style="color:var\(--gold-light\)">(.*?)</span><h3>(.*?)</h3><p class="more"( style="max-height:none;opacity:1;margin-top:.5rem")?>(.*?)</p></div></a>')
SCARD = re.compile(r'(?:<span id="([a-z-]+)" style="position:relative;top:-6rem"></span>)?<a href="([^"]+)" class="scard lift reveal"((?: data-[a-z]+(?:="[^"]*")?)*) style="transition-delay:(\d+)ms"><div class="pic img-zoom"><img src="([^"]+)" alt="([^"]*)" loading="lazy" decoding="async"></div><div class="txt"><span class="kicker">(.*?)</span><h3>(.*?)</h3><p>(.*?)</p><span class="link">(.*?) (<svg.*?</svg>)</span></div></a>')
FAQ = re.compile(r'<div class="faq-item( open)? reveal" style="transition-delay:(\d+)ms"><button type="button" class="faq-q" aria-expanded="(true|false)">(.*?)<span class="tog" aria-hidden="true">\+</span></button><div class="faq-a"><div>(.*?)</div></div></div>')


def run_all(regex, s):
    """findall + check the matches cover the whole string"""
    ms = list(regex.finditer(s))
    assert ''.join(m.group(0) for m in ms) == s, ('uncovered', s[:400])
    return ms


def attrs(a):
    return dict(re.findall(r' data-([a-z]+)(?:="([^"]*)")?', a))


def delays_ok(ms, kind):
    for i, m in enumerate(ms):
        want = (i % 2) * 80 if kind == 'mod2' else i * 80
        assert int(m.group(4)) == want, (kind, i, m.group(4))


data = {k: [] for k in ['pages', 'services', 'destinations', 'itineraries', 'experiences', 'case_studies',
                        'journal_categories', 'journal_posts', 'faqs', 'testimonials', 'menus', 'settings']}


def replace_once(s, old, new):
    assert s.count(old) == 1, ('replace_once', old[:120], s.count(old))
    return s.replace(old, new)


def grab(regex, s, flags=re.S):
    m = re.search(regex, s, flags)
    assert m, regex
    return m


# ---------------------------------------------------------------- FAQ lists (anywhere)
def faq_lists(s, group_for, open_first):
    def rep(m):
        inner = m.group(1)
        group = group_for(m)
        items = run_all(FAQ, inner)
        for i, it in enumerate(items):
            assert int(it.group(2)) == i * 40
            assert bool(it.group(1)) == (i == 0 and open_first) and (it.group(3) == 'true') == bool(it.group(1))
            data['faqs'].append({'group': group, 'question': it.group(4), 'answer': to_paths(it.group(5)), 'sort_order': i + 1})
        opn = ' open="1"' if open_first else ''
        return f'<div class="faq-list"><!--wam:faqs group="{group}"{opn}--></div>'
    return re.sub(r'<div class="faq-list">((?:<div class="faq-item.*?</div></div></div>)+)</div>', rep, s)


# ---------------------------------------------------------------- itineraries
ITIN = ['imperial-cities', 'magical-morocco', 'grand-tour', 'desert-kasbahs', 'marrakech-incentive']
itins = {}
DAY = re.compile(r'<li class="day reveal"><span class="d">(\d+)<small>Day</small></span><div><h3>(.*?)</h3><p>(.*?)</p></div>(?:<div class="thumb"><img src="([^"]+)" alt="([^"]*)" loading="lazy" decoding="async"></div>)?</li>')
for i, slug in enumerate(ITIN):
    p = read('itineraries.pages.' + slug)
    secs = sections_of(p['content'])
    h = hero(secs[0])
    hx = grab(r'^<div class="ctas"><button type="button" class="btn btn-gold " data-inquiry="([a-z-]*)"><span>Shape this journey</span>(<svg.*?</svg>)</button></div><dl class="facts"><div><span>Length</span><b>(.*?)</b></div><div><span>Route</span><b>(.*?)</b></div></dl>$', h['hero_extra'])
    arrow(hx.group(2))
    s1 = grab(r'^<section class="sec bg-ivory"><div class="container cols c-8-4 items-start">\n'
              r'  <div><div class="reveal"><p class="eyebrow">Day by day</p><h2 class="h2 big">The route, <span class="it">day by day.</span></h2></div><div class="mt-14"><ol class="days">(.*?)</ol></div>\n'
              r'  (?:<p class="lead" style="max-width:44rem;margin-top:2.5rem"><span class="it" style="color:var\(--clay\)">Journey character.</span> (.*?)</p>)?</div>\n'
              r'  <aside class="sticky reveal-right">\n'
              r'(?:    <div class="box"><span class="kicker">The route</span><ol class="route-list">(.*?)</ol></div>\n)?'
              r'    <div class="box dark-card" style="background:var\(--ink\)"><h3>Signature does not mean fixed</h3><p>This journey is a starting point. We adapt the pace, houses and experiences to each traveler, or design a fully tailor-made route.</p><button type="button" class="btn btn-gold " data-inquiry="([a-z-]*)"><span>Shape this journey</span>(<svg.*?</svg>)</button></div>\n'
              r'  </aside>\n'
              r'</div></section>$', secs[1])
    assert s1.group(4) == hx.group(1)
    days = [{'day_no': int(d.group(1)), 'title': d.group(2), 'description': d.group(3),
             'image': text(d.group(4)) if d.group(4) else None, 'image_alt': text(d.group(5)) if d.group(5) else None}
            for d in run_all(DAY, s1.group(1))]
    assert [d['day_no'] for d in days] == list(range(1, len(days) + 1))
    s2 = grab(r'^<section class="sec bg-parch"><div class="container grid g-2" style="gap:1.5rem">\n'
              r'  <article class="vcard reveal"><span class="ico">(<svg.*?</svg>)</span><h3>Typically included</h3><ul class="dots" style="margin-top:1.25rem">(.*?)</ul></article>\n'
              r'  <article class="vcard reveal" style="transition-delay:80ms"><span class="ico">(<svg.*?</svg>)</span><h3>Typically excluded unless stated</h3><ul class="dots x" style="margin-top:1.25rem">(.*?)</ul></article>\n'
              r'</div></section>$', secs[2])
    s3 = grab(r'^<section class="sec bg-ivory" style="padding-top:0"><div class="container"><div class="reveal" style="padding-top:6rem"><p class="eyebrow">Along the way</p></div><div class="mt-6"><div class="gallery">(.*?)</div></div></div></section>$', secs[3])
    gal = re.findall(r'<figure class="reveal-scale" style="transition-delay:(\d+)ms"><img src="([^"]+)" alt="([^"]*)" loading="lazy" decoding="async"></figure>', s3.group(1))
    assert [int(g[0]) for g in gal] == [k * 70 for k in range(len(gal))]
    assert ''.join(f'<figure class="reveal-scale" style="transition-delay:{g[0]}ms"><img src="{g[1]}" alt="{g[2]}" loading="lazy" decoding="async"></figure>' for g in gal) == s3.group(1)
    assert len(secs) == 5
    final = block(secs[4]); assert final['type'] == 'cta'
    eyebrow = h['hero_eyebrow']
    lm = re.match(r'(?:(\d+) days)?(?: / )?(?:(\d+) nights)?$', hx.group(3))
    it = {
        'slug': slug, 'title': text(re.sub(r' <span.*', '', h['hero_title'])), 'meta_title': text(p['title']),
        'meta_description': p['meta_description'], 'og_image': p['og_image'],
        'hero_image': h['hero_image'], 'hero_image_alt': h['hero_image_alt'], 'hero_eyebrow': eyebrow,
        'hero_title': h['hero_title'], 'summary': h['hero_subtitle'], 'hero_subtitle': h['hero_subtitle'],
        'hero_crumbs': h['hero_crumbs'], 'hero_variant': None, 'hero_extra': None,
        'number': eyebrow.split(' · ')[0] if eyebrow[0].isdigit() else None,
        'is_signature': eyebrow.endswith('Signature journey'),
        'duration_label': hx.group(3), 'days': int(lm.group(1)) if lm and lm.group(1) else None,
        'nights': int(lm.group(2)) if lm and lm.group(2) else None,
        'route': hx.group(4), 'route_stops': re.findall(r'<li>(.*?)</li>', s1.group(3)) if s1.group(3) else [],
        'inquiry_key': hx.group(1), 'character': s1.group(2),
        'includes': re.findall(r'<li>(.*?)</li>', s2.group(2)), 'excludes': re.findall(r'<li>(.*?)</li>', s2.group(4)),
        'gallery': [{'image': text(g[1]), 'alt': text(g[2])} for g in gal],
        'final_cta': final, 'sort_order': i + 1, 'days_list': days, 'currency': 'EUR', 'price_from': None,
    }
    assert s2.group(1) and s2.group(3)
    it['_icons'] = [s2.group(1), s2.group(3)]
    itins[slug] = it
    assert h['hero_crumb'] == it['title'], (h['hero_crumb'], it['title'])
    assert not h['hero_variant']
ICON_INC, ICON_EXC = itins['grand-tour']['_icons']
for it in itins.values():
    assert it.pop('_icons') == [ICON_INC, ICON_EXC]

# itinerary card data from the index page
idx = read('itineraries.index')
isecs = sections_of(idx['content'])
dgrid = grab(r'<div class="grid g-3 mt-14">((?:<a class="dcard.*?</a>)+)</div>', isecs[1]).group(1)
for m in run_all(DCARD, dgrid):
    slug = m.group(1).split('/')[-1].replace("{{ route('itineraries.show', '", '').replace("') }}", '')
    it = itins[slug]
    assert m.group(2) and not m.group(3) and m.group(8)
    assert m.group(6) == f"{it['number']} · {it['duration_label']}"
    it['card_image'], it['card_image_alt'] = text(m.group(4)), text(m.group(5))
    assert m.group(7) == it['title'].replace('&', '&amp;') or text(m.group(7)) == it['title']
    it['tagline'] = m.group(9)
sgrid = grab(r'<div class="grid g-2" id="it-grid" style="gap:1.5rem">((?:<a .*?</a>)+)</div>', isecs[2]).group(1)
ms = run_all(SCARD, sgrid); delays_ok(ms, 'mod2')
for m in ms:
    slug = re.search(r"'itineraries.show', '([a-z-]+)'", m.group(2)).group(1)
    it = itins[slug]
    a = attrs(m.group(3))
    it['filter_type'], it['filter_dest'], it['filter_len'] = a['type'], a['dest'], a['len']
    assert m.group(7) == it['duration_label'] and text(m.group(8)) == it['title'] and m.group(10) == 'See the day-by-day'
    arrow(m.group(11))
    it['card_route'] = m.group(9)
    if 'card_image' in it:
        assert (it['card_image'], it['card_image_alt']) == (text(m.group(5)), text(m.group(6)))
    else:
        it['card_image'], it['card_image_alt'] = text(m.group(5)), text(m.group(6))
    it['tagline'] = it.get('tagline')
order_index = [re.search(r"'itineraries.show', '([a-z-]+)'", m.group(2)).group(1) for m in ms]
assert order_index == ITIN

# signature scard grids (destinations index, marrakech) and home tabs/json
SIG = [s for s in ITIN if itins[s]['is_signature']]


def sig_scards(sec):
    m = grab(r'<div class="grid mt-14" style="gap:1.5rem">((?:<a href="\{\{ route\(\'itineraries.*?</a>)+)</div>', sec)
    ms = run_all(SCARD, m.group(1)); delays_ok(ms, 'linear')
    got = [re.search(r"'itineraries.show', '([a-z-]+)'", x.group(2)).group(1) for x in ms]
    assert got == SIG, got
    for x in ms:
        it = itins[got[ms.index(x)]]
        assert not x.group(3) and x.group(9) == it['card_route'] and x.group(7) == it['duration_label']
    return sec.replace(m.group(1), '<!--wam:itinerary-scards signature="1"-->')


# ---------------------------------------------------------------- destinations
didx = read('destinations.index')
dsecs = sections_of(didx['content'])
dests = {}
sgrid = grab(r'<div class="grid g-2 mt-14" style="gap:1.5rem">((?:<span id.*?</a>)+)</div>', dsecs[2]).group(1)
ms = run_all(SCARD, sgrid); delays_ok(ms, 'mod2')
for i, m in enumerate(ms):
    slug = m.group(1)
    kick = m.group(7); assert kick.startswith('Best for · ')
    has_page = 'destinations.show' in m.group(2)
    assert m.group(10) == ('Discover' if has_page else 'Plan a stay here')
    assert has_page or m.group(2) == "{{ route('quote') }}"
    arrow(m.group(11))
    dests[slug] = {'slug': slug, 'name': m.group(8), 'best_for': kick[len('Best for · '):], 'summary': m.group(9),
                   'card_image': text(m.group(5)), 'card_image_alt': text(m.group(6)), 'has_page': has_page,
                   'sort_order': i + 1, 'is_featured': False}
dgrid = grab(r'<div class="grid g-3 mt-14">((?:<a class="dcard.*?</a>)+)</div>', dsecs[1]).group(1)
for i, m in enumerate(run_all(DCARD, dgrid)):
    href = m.group(1)
    slug = re.search(r"'destinations.show', '([a-z-]+)'", href).group(1) if 'destinations.show' in href else href.split('#')[1]
    d = dests[slug]
    assert ('destinations.show' in href) == d['has_page'] and not m.group(2) and not m.group(3) and not m.group(8)
    d.update({'is_featured': True, 'featured_order': i + 1, 'featured_name': m.group(7), 'tagline': m.group(6),
              'intro': m.group(9), 'featured_image': text(m.group(4)), 'featured_image_alt': text(m.group(5))})
DEST_DGRID = dgrid
dsecs[1] = dsecs[1].replace(dgrid, '<!--wam:destination-dcards-->')
dsecs[2] = dsecs[2].replace(sgrid, '<!--wam:destination-scards-->')
dsecs[3] = sig_scards(dsecs[3])

# ---------------------------------------------------------------- generic content records
def content_record(view, extra_hero_parent=None):
    p = read(view)
    secs = sections_of(p['content'])
    rec = {'meta_title': text(p['title']), 'meta_description': p['meta_description'], 'og_image': p['og_image']}
    if secs[0].startswith('<section class="phero'):
        rec.update(hero(secs[0]))
        secs = secs[1:]
    rec['_secs'] = secs
    rec['_scripts'] = p['scripts']
    rec['_nav'] = p['nav']
    return rec


def finish(rec, secs=None):
    secs = rec.pop('_secs') if secs is None else (rec.pop('_secs'), secs)[1]
    blocks = [block(s) for s in secs]
    sc = rec.pop('_scripts')
    for line in [l for l in sc.split('\n') if l.strip()]:
        if line.startswith('<script>window.WAM_JOURNEYS='):
            blocks.append({'type': 'script', 'name': 'journeys'})
        elif line.startswith('<script>window.WAM_STORIES='):
            blocks.append({'type': 'script', 'name': 'stories', 'placement': stories_placement(line)})
        else:
            raise SystemExit('unknown script ' + line[:80])
    rec['sections'] = blocks
    rec.pop('_nav', None)
    return rec


STORY_SETS = {}


def stories_placement(line):
    arr = json.loads(line[len('<script>window.WAM_STORIES='):-len(';</script>')])
    key = 'guests' if len(arr) > 1 else 'services'
    if key not in STORY_SETS:
        STORY_SETS[key] = arr
        for i, t in enumerate(arr):
            data['testimonials'].append({'placement': key, 'image': t['img'], 'name': t['name'], 'role': t['role'],
                                         'trip': t['trip'], 'quote': t['quote'], 'sort_order': i + 1})
    assert STORY_SETS[key] == arr
    assert line == '<script>window.WAM_STORIES=' + json.dumps(arr, ensure_ascii=False) + ';</script>'
    return key


# ---------------------------------------------------------------- services
SERV = ['tailor-made', 'incentives', 'events', 'ground-handling']
servs = {}
for i, slug in enumerate(SERV):
    r = content_record('services.pages.' + slug)
    r['_secs'] = [faq_lists(s, lambda m, slug=slug: 'service-' + slug, True) for s in r['_secs']]
    r = finish(r)
    r.update({'slug': slug, 'name': text(r['hero_crumb']), 'eyebrow': r['hero_eyebrow'], 'intro': r['hero_subtitle'],
              'sort_order': i + 1})
    servs[slug] = r
sidx = content_record('services.index')
sgrid = grab(r'<div class="grid mt-14" style="gap:1.5rem">((?:<a href="\{\{ route\(\'services.*?</a>)+)</div>', sidx['_secs'][0]).group(1)
ms = run_all(SCARD, sgrid); delays_ok(ms, 'linear')
for m in ms:
    slug = re.search(r"'services.show', '([a-z-]+)'", m.group(2)).group(1)
    s = servs[slug]
    assert m.group(10) == 'Explore' and not m.group(3)
    s['name'] = text(m.group(8))
    s.update({'card_kicker': m.group(7), 'card_summary': m.group(9), 'card_image': text(m.group(5)), 'card_image_alt': text(m.group(6))})
assert [re.search(r"'services.show', '([a-z-]+)'", m.group(2)).group(1) for m in ms] == SERV
sidx['_secs'][0] = sidx['_secs'][0].replace(sgrid, '<!--wam:service-scards-->')

# ---------------------------------------------------------------- experiences
eidx = content_record('experiences')
egrid = grab(r'<div class="grid g-3" id="exp-grid">((?:<a class="dcard.*?</a>)+)</div>', eidx['_secs'][0]).group(1)
for i, m in enumerate(run_all(DCARD, egrid)):
    a = attrs(m.group(3))
    assert m.group(1) == '#' and set(a) == {'item', 'cat', 'inquiry'} and a['inquiry'] == '' and not m.group(2) and not m.group(8)
    data['experiences'].append({'title': m.group(7), 'slug': re.sub(r'[^a-z0-9]+', '-', text(m.group(7)).lower()).strip('-'),
                                'category': a['cat'], 'location': m.group(6), 'image': text(m.group(4)), 'image_alt': text(m.group(5)),
                                'description': m.group(9), 'sort_order': i + 1})
eidx['_secs'][0] = eidx['_secs'][0].replace(egrid, '<!--wam:experience-dcards-->')

# ---------------------------------------------------------------- case studies
cs = content_record('case-studies.pages.agafay-gala')
cs = finish(cs)
facts = dict(re.findall(r'<div><span>(.*?)</span><b>(.*?)</b></div>', cs['hero_extra']))
cs.update({'slug': 'agafay-gala', 'title': text(re.sub(r'<[^>]+>', '', cs['hero_title'])), 'client': '[Client]',
           'industry': facts['Service'], 'group_size': facts['Group size'], 'location': facts['Where'], 'year': facts['Year']})
cidx = content_record('case-studies.index')
cgrid = grab(r'<div class="grid g-2" id="cs-grid" style="gap:1.5rem">((?:<a .*?</a>)+)</div>', cidx['_secs'][0]).group(1)
ms = run_all(SCARD, cgrid); delays_ok(ms, 'mod2')
first = ms[0]
cs.update({'card_kicker': first.group(7), 'card_title': first.group(8), 'card_summary': first.group(9),
           'card_image': text(first.group(5)), 'card_image_alt': text(first.group(6)), 'filter_service': attrs(first.group(3))['svc']})
assert first.group(10) == 'Read the case study'
cidx['_secs'][0] = cidx['_secs'][0].replace(cgrid, '<!--wam:case-study-scards-->' + cgrid[len(first.group(0)):])
data['case_studies'].append(cs)

# ---------------------------------------------------------------- journal
jp = content_record('journal.pages.best-time-to-visit-marrakech')
jp = finish(jp)
data['journal_categories'] = [{'name': 'Planning', 'slug': 'planning', 'sort_order': 1},
                              {'name': 'Incentives', 'slug': 'incentive', 'sort_order': 2},
                              {'name': 'Venues', 'slug': 'venues', 'sort_order': 3},
                              {'name': 'Culture', 'slug': 'culture', 'sort_order': 4}]
pm = dict(re.findall(r'<div><span>(.*?)</span><b>(.*?)</b></div>', jp['hero_extra']))
jidx = content_record('journal.index')
feat = grab(r'<div class="reveal">(<a href="\{\{ route\(\'journal.show.*?</a>)</div>', jidx['_secs'][0]).group(1)
fm = SCARD.fullmatch(feat)
jgrid = grab(r'<div class="grid g-2" id="post-grid" style="gap:1.5rem">((?:<a .*?</a>)+)</div>', jidx['_secs'][0]).group(1)
ms = run_all(SCARD, jgrid); delays_ok(ms, 'mod2')
g0 = ms[0]
assert fm.group(7) == 'Featured · Planning' and g0.group(7) == 'Planning · 6 min' and fm.group(9) == g0.group(9) and fm.group(8) == g0.group(8)
assert fm.group(10) == 'Read the guide' and g0.group(10) == 'Read' and attrs(g0.group(3))['topic'] == 'planning'
jp.update({'slug': 'best-time-to-visit-marrakech', 'category': 'planning', 'title': g0.group(8), 'excerpt': g0.group(9),
           'cover_image': text(g0.group(5)), 'cover_image_alt': text(g0.group(6)), 'author': pm['By'],
           'reading_minutes': int(pm['Read'].split()[0]), 'published_label': pm['Updated'], 'is_featured': True})
jidx['_secs'][0] = jidx['_secs'][0].replace('<div class="reveal">' + feat + '</div>', '<div class="reveal"><!--wam:journal-featured--></div>')
jidx['_secs'][0] = jidx['_secs'][0].replace(jgrid, '<!--wam:journal-scards-->' + jgrid[len(g0.group(0)):])
data['journal_posts'].append(jp)

# ---------------------------------------------------------------- Marrakech destination page
mk = content_record('destinations.pages.marrakech')
mk['_secs'] = [sig_scards(s) if 'Journeys through Marrakech' in s else s for s in mk['_secs']]
mk = finish(mk)
facts = dict(re.findall(r'<div><span>(.*?)</span><b>(.*?)</b></div>', mk['hero_extra']))
dests['marrakech'].update({k: v for k, v in mk.items()})
dests['marrakech'].update({'region': 'Marrakech-Safi', 'best_season': facts.get('Best season')})
for d in dests.values():
    d.setdefault('sections', [])

# ---------------------------------------------------------------- pages
PAGES = ['home', 'about', 'why-us', 'sustainability', 'faq', 'terms', 'privacy', 'cookies', 'quote', 'contact',
         'thank-you', 'services', 'destinations', 'itineraries', 'experiences', 'case-studies', 'journal']
VIEW = {'terms': 'legal.terms', 'privacy': 'legal.privacy', 'cookies': 'legal.cookies', 'services': 'services.index',
        'destinations': 'destinations.index', 'itineraries': 'itineraries.index', 'case-studies': 'case-studies.index',
        'journal': 'journal.index'}
for i, slug in enumerate(PAGES):
    view = VIEW.get(slug, slug)
    if slug == 'services':
        r = sidx
    elif slug == 'experiences':
        r = eidx
    elif slug == 'case-studies':
        r = cidx
    elif slug == 'journal':
        r = jidx
    elif slug == 'destinations':
        r = content_record(view); r['_secs'] = dsecs[1:]
    elif slug == 'itineraries':
        r = content_record(view)
        secs = r['_secs']
        secs[0] = secs[0].replace(grab(r'<div class="grid g-3 mt-14">((?:<a class="dcard.*?</a>)+)</div>', secs[0]).group(1), '<!--wam:itinerary-dcards-->')
        secs[1] = secs[1].replace(grab(r'<div class="grid g-2" id="it-grid" style="gap:1.5rem">((?:<a .*?</a>)+)</div>', secs[1]).group(1), '<!--wam:itinerary-scards filters="1"-->')
    else:
        r = content_record(view)
    if slug == 'faq':
        grp = lambda m: re.findall(r'id="([a-z]+)"><span class="kicker">[^<]*</span>$', m.string[:m.start()])[-1]
        r['_secs'] = [faq_lists(s, grp, False) if 'faq-list' in s else s for s in r['_secs']]
    if slug == 'home':
        secs = r['_secs']
        secs = [faq_lists(s, lambda m: 'home', True) if 'faq-list' in s else s for s in secs]
        j = [k for k, s in enumerate(secs) if 'id="journeys"' in s][0]
        s = secs[j]
        assert DEST_DGRID in s
        s = s.replace(DEST_DGRID, '<!--wam:destination-dcards-->')
        tabs = grab(r'<div class="jsel reveal-left" role="tablist" aria-label="Signature journeys">(.*?)</div>\n', s).group(1)
        want = ''.join(f'<button type="button" class="jbtn" role="tab" aria-selected="{"true" if k == 0 else "false"}" tabindex="{0 if k == 0 else -1}"><span class="top"><span class="kicker">{itins[x]["duration_label"]}</span><span class="viewing">Viewing</span></span><b>{itins[x]["title"]}</b><small>{itins[x]["tagline"]}</small></button>' for k, x in enumerate(SIG))
        assert tabs == want, (tabs, want)
        s = s.replace(tabs, '<!--wam:journey-tabs-->')
        det = grab(r'(      <div class="jdetail reveal-right" role="tabpanel">\n.*?\n      </div>\n)', s).group(1)
        s = s.replace(det, '      <!--wam:journey-detail-->\n')
        secs[j] = s
        r['_secs'] = secs
        JDETAIL = det
    r = finish(r)
    r.update({'slug': slug, 'title': text(r.get('hero_crumb') or 'Home'), 'template': 'home' if slug == 'home' else 'default',
              'sort_order': i + 1})
    data['pages'].append(r)

# journeys JSON data
jline = [l for l in read('home')['scripts'].split('\n') if 'WAM_JOURNEYS' in l][0]
jarr = json.loads(jline[len('<script>window.WAM_JOURNEYS='):-len(';</script>')])
for k, x in enumerate(SIG):
    it, j = itins[x], jarr[k]
    assert j['name'] == it['title'] and j['img'] == it['card_image'] and j['route'] == it['card_route'], (j, it['card_route'])
    assert j['summary'] == text(it['summary']), (j['summary'], it['summary'])
    assert j['href'] == "{{ route('itineraries.show', '" + x + "') }}", j['href']
    assert j['cta'] == 'See the day-by-day'
    it['highlights'] = j['includes']
for it in itins.values():
    it.setdefault('highlights', [])
    it['destinations'] = [d for d in dests if d in it['filter_dest'].split()]

data['services'] = list(servs.values())
data['destinations'] = list(dests.values())
data['itineraries'] = list(itins.values())

# ---------------------------------------------------------------- menus + settings from partials
hdr = open(os.path.join(V, 'partials', 'header.blade.php'), encoding='utf-8').read()
ftr = open(os.path.join(V, 'partials', 'footer.blade.php'), encoding='utf-8').read()
ACTIVE = {'itineraries.index': 'itineraries.*', 'destinations.index': 'destinations.*', 'experiences': 'experiences',
          'services.index': 'services.*,case-studies.*', 'about': 'about,why-us,sustainability', 'journal.index': 'journal.*',
          'faq': 'faq'}


def menu(name, html, active=False):
    items = re.findall(r"<a href=\"\{\{ route\('([a-z.-]+)'(?:, '([a-z0-9-]+)')?\) \}\}\"(?:@if[^>]*@endif)?>(.*?)</a>", html)
    data['menus'].append({'name': name, 'items': [{'label': text(l), 'route': r, 'route_param': p or None,
                                                   'active_routes': ACTIVE.get(r) if active else None, 'sort_order': i + 1}
                                                  for i, (r, p, l) in enumerate(items)]})


menu('main', grab(r'<nav class="main-nav" aria-label="Main">(.*?)</nav>', hdr).group(1), True)
menu('mobile', grab(r'<nav aria-label="Mobile">(.*?)</nav>', hdr).group(1), True)
cols = re.findall(r'<div class="f-col"><h4>(.*?)</h4><ul>(.*?)</ul></div>', ftr)
menu('footer-explore', cols[0][1]); menu('footer-services', cols[1][1])
menu('legal', grab(r'<nav aria-label="Legal">(.*?)</nav>', ftr).group(1))
atel = re.findall(r'<li>(.*?)</li>', cols[2][1])
data['settings'] = [
    {'group': 'contact', 'key': 'phone', 'value': grab(r'href="tel:([^"]+)">([^<]+)</a>', hdr).group(1)},
    {'group': 'contact', 'key': 'phone_display', 'value': grab(r'href="tel:([^"]+)">([^<]+)</a>', hdr).group(2)},
    {'group': 'contact', 'key': 'email', 'value': grab(r'mailto:([^"]+)"', ftr).group(1)},
    {'group': 'contact', 'key': 'address', 'value': text(atel[0])},
    {'group': 'social', 'key': 'socials', 'value': [{'label': l, 'url': u} for u, l in re.findall(r'<a href="([^"]*)">([^<]+)</a>', atel[3])]},
    {'group': 'footer', 'key': 'footer_blurb', 'value': text(grab(r'<p class="f-blurb">(.*?)</p>', ftr).group(1))},
    {'group': 'footer', 'key': 'copyright', 'value': text(grab(r'<div class="container legal"><span>(.*?)</span>', ftr).group(1))},
    {'group': 'footer', 'key': 'footer_tagline', 'value': text(grab(r'</nav><span>(.*?)</span></div>', ftr).group(1))},
    {'group': 'footer', 'key': 'footer_columns', 'value': [cols[0][0], cols[1][0], cols[2][0]]},
    {'group': 'ui', 'key': 'icons', 'value': {'arrow': ARROW, 'star': STAR, 'check': ICON_INC, 'doc': ICON_EXC}},
]

for k, v in data.items():
    with open(os.path.join(OUT, k + '.json'), 'w', encoding='utf-8', newline='\n') as fh:
        json.dump(v, fh, ensure_ascii=False, indent=1)
print({k: len(v) for k, v in data.items()})
