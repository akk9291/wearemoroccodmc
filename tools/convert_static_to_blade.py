"""Convert the static We Are Morocco site into Blade views.
usage: python -I convert.py <static-site-dir> <laravel-backend-dir>
"""
import re, sys, os, json

SRC, DST = sys.argv[1], sys.argv[2]
VIEWS = os.path.join(DST, 'resources', 'views')

# file -> (view name, route name, route param or None)
PAGES = {
    'index.html': ('home', 'home', None),
    'about.html': ('about', 'about', None),
    'why-us.html': ('why-us', 'why-us', None),
    'services.html': ('services.index', 'services.index', None),
    'services-tailor-made.html': ('services.pages.tailor-made', 'services.show', 'tailor-made'),
    'services-incentives.html': ('services.pages.incentives', 'services.show', 'incentives'),
    'services-events.html': ('services.pages.events', 'services.show', 'events'),
    'services-ground-handling.html': ('services.pages.ground-handling', 'services.show', 'ground-handling'),
    'destinations.html': ('destinations.index', 'destinations.index', None),
    'destination-marrakech.html': ('destinations.pages.marrakech', 'destinations.show', 'marrakech'),
    'itineraries.html': ('itineraries.index', 'itineraries.index', None),
    'itinerary-imperial-cities.html': ('itineraries.pages.imperial-cities', 'itineraries.show', 'imperial-cities'),
    'itinerary-magical-morocco.html': ('itineraries.pages.magical-morocco', 'itineraries.show', 'magical-morocco'),
    'itinerary-grand-tour.html': ('itineraries.pages.grand-tour', 'itineraries.show', 'grand-tour'),
    'itinerary-desert-kasbahs.html': ('itineraries.pages.desert-kasbahs', 'itineraries.show', 'desert-kasbahs'),
    'itinerary-marrakech-incentive.html': ('itineraries.pages.marrakech-incentive', 'itineraries.show', 'marrakech-incentive'),
    'experiences.html': ('experiences', 'experiences', None),
    'case-studies.html': ('case-studies.index', 'case-studies.index', None),
    'case-study.html': ('case-studies.pages.agafay-gala', 'case-studies.show', 'agafay-gala'),
    'journal.html': ('journal.index', 'journal.index', None),
    'journal-post.html': ('journal.pages.best-time-to-visit-marrakech', 'journal.show', 'best-time-to-visit-marrakech'),
    'faq.html': ('faq', 'faq', None),
    'quote.html': ('quote', 'quote', None),
    'contact.html': ('contact', 'contact', None),
    'thank-you.html': ('thank-you', 'thank-you', None),
    'sustainability.html': ('sustainability', 'sustainability', None),
    'terms.html': ('legal.terms', 'terms', None),
    'privacy.html': ('legal.privacy', 'privacy', None),
    'cookies.html': ('legal.cookies', 'cookies', None),
}
# child views of these parents extend the group template instead of the layout
GROUPS = {'services.pages.': 'services.show', 'destinations.pages.': 'destinations.show',
          'itineraries.pages.': 'itineraries.show', 'case-studies.pages.': 'case-studies.show',
          'journal.pages.': 'journal.show'}


def route_expr(fname):
    _, name, param = PAGES[fname]
    return f"route('{name}', '{param}')" if param else f"route('{name}')"


LINK_RE = re.compile(r'(["\'])([a-z0-9-]+\.html)((?:[?#][^"\']*)?)\1')
ASSET_RE = re.compile(r'(["\'])assets/([^"\']+)\1')


def rewrite_links(s):
    def rep(m):
        q, f, rest = m.groups()
        if f not in PAGES:
            raise SystemExit(f'unknown link target {f}')
        return f"{q}{{{{ {route_expr(f)} }}}}{rest}{q}"
    s = LINK_RE.sub(rep, s)
    s = ASSET_RE.sub(lambda m: f"{m.group(1)}{{{{ asset('assets/{m.group(2)}') }}}}{m.group(1)}", s)
    return s


def between(s, start, end, incl=False):
    i = s.index(start)
    j = s.index(end, i + len(start))
    return s[i:j + len(end)] if incl else s[i + len(start):j]


def parse(fname):
    html = open(os.path.join(SRC, fname), encoding='utf-8').read().replace('\r\n', '\n')
    p = {}
    p['title'] = between(html, '<title>', '</title>')
    p['meta'] = between(html, '</title>\n', '<link rel="icon"').rstrip('\n')
    p['head_extra'] = between(html, 'assets/arena.css">\n', '</head>').strip('\n')
    p['pre_header'] = between(html, '<body>\n', '<header class="site-header">')
    p['header'] = between(html, '<header class="site-header">', '</header>', True)
    p['between'] = between(html, '</header>\n', '<main id="main">')
    p['main'] = between(html, '<main id="main">\n', '\n</main>')
    p['footer'] = between(html, '<footer class="site-footer">', '</footer>', True)
    tail = between(html, '</footer>\n', '<script src="assets/arena.js"></script>')
    mi = tail.index('<div class="modal" id="inquiry"')
    me = tail.index('\n</div>\n', mi) + len('\n</div>\n')
    p['modal'] = tail[mi:me]
    rest = tail[me:]
    fab_m = re.match(r'(<button type="button" class="btn btn-gold fab".*?</button>\n)', rest, re.S)
    p['fab'] = fab_m.group(1)
    p['scripts'] = rest[len(p['fab']):].strip('\n')
    p['pre_modal'] = tail[:mi]
    p['after'] = html[html.index('<script src="assets/arena.js"></script>'):]
    cur = re.search(r'<nav class="main-nav"[^>]*>(.*?)</nav>', p['header']).group(1)
    m = re.search(r'href="([a-z0-9-]+)\.html" aria-current="page"', cur)
    p['nav'] = m.group(1) if m else ''
    mob = re.search(r'<nav aria-label="Mobile">(.*?)</nav>', p['header']).group(1)
    m2 = re.search(r'href="([a-z0-9-]+)\.html" aria-current="page"', mob)
    p['nav_mobile'] = m2.group(1) if m2 else ''
    return p


pages = {f: parse(f) for f in PAGES}
base = pages['index.html']
for f, p in pages.items():
    for k in ('pre_header', 'between', 'footer', 'modal', 'fab', 'pre_modal', 'after'):
        if p[k] != base[k]:
            raise SystemExit(f'{f}: shared part {k} differs from index.html')
    if p['head_extra']:
        raise SystemExit(f'{f}: unexpected extra head content')
    # header must equal the index header once aria-current is removed
    if p['header'].replace(' aria-current="page"', '') != base['header']:
        raise SystemExit(f'{f}: header differs')
    if p['nav'] != p['nav_mobile']:
        raise SystemExit(f'{f}: desktop/mobile nav mismatch')


def write(view, content):
    path = os.path.join(VIEWS, *view.split('.')) + '.blade.php'
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8', newline='\n') as fh:
        fh.write(content)


# header partial: nav links get aria-current based on $nav
header = rewrite_links(base['header'])
for f in PAGES:
    key = f[:-5]
    expr = re.escape(f'href="{{{{ {route_expr(f)} }}}}"')
    header = re.sub(expr, lambda m, key=key: m.group(0) + f"@if($nav === '{key}') aria-current=\"page\"@endif", header)
write('partials.header', header + '\n')
news = base['footer'][base['footer'].index('      <form class="news"'):base['footer'].index('</form>') + 7]
write('partials.newsletter', rewrite_links(news) + '\n')
footer = base['footer'].replace(news + '\n', "      @include('partials.newsletter')\n")
write('partials.footer', rewrite_links(footer) + '\n')
write('partials.inquiry', rewrite_links(base['modal']))
write('partials.fab', base['fab'])

layout = f"""<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{!! trim($__env->yieldContent('title')) !!}}</title>
@yield('meta')
<link rel="icon" href="{{{{ asset('assets/favicon.svg') }}}}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.pexels.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="{{{{ asset('assets/arena.css') }}}}">
@stack('head')
</head>
<body>
{base['pre_header']}@php($nav = trim($__env->yieldContent('nav')))
@include('partials.header')
{base['between']}<main id="main">
@yield('content')
</main>
@include('partials.footer')
@include('partials.inquiry')
@include('partials.fab')
@stack('scripts')
<script src="{{{{ asset('assets/arena.js') }}}}"></script>
</body>
</html>
"""
# sanity: the fixed head lines in the layout must match index.html exactly
idx = open(os.path.join(SRC, 'index.html'), encoding='utf-8').read()
for line in ['<link rel="preconnect" href="https://fonts.googleapis.com">',
             '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap">']:
    assert line in idx
assert base['after'].strip() == '<script src="assets/arena.js"></script>\n</body>\n</html>'.strip(), repr(base['after'])
write('layouts.app', layout)

group_nav = {}
for f, (view, _, _) in PAGES.items():
    for prefix, parent in GROUPS.items():
        if view.startswith(prefix):
            group_nav.setdefault(parent, set()).add(pages[f]['nav'])
for parent, navs in group_nav.items():
    assert len(navs) == 1, (parent, navs)
    write(parent, "@extends('layouts.app')\n\n"
          "{{-- Shared template for every page in this group. Child views in ./pages fill the sections. --}}\n"
          f"@section('nav', '{navs.pop()}')\n")

for f, (view, _, _) in PAGES.items():
    p = pages[f]
    parent = next((par for pre, par in GROUPS.items() if view.startswith(pre)), None)
    out = [f"@extends('{parent or 'layouts.app'}')", '']
    if not parent:
        out.append(f"@section('nav', '{p['nav']}')")
    out.append("@section('title')\n" + p['title'] + "\n@endsection")
    out.append('')
    out.append('@section(\'meta\')\n' + rewrite_links(p['meta']) + '\n@endsection')
    out.append('')
    out.append('@section(\'content\')\n' + rewrite_links(p['main']) + '\n@endsection')
    if p['scripts']:
        out.append('')
        out.append("@push('scripts')\n" + rewrite_links(p['scripts']) + '\n@endpush')
    write(view, '\n'.join(out) + '\n')

print('ok', len(PAGES), 'pages')
