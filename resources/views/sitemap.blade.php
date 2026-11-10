{{-- XML sitemap. Rendered by the /sitemap.xml route in routes/web.php --}}
{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <lastmod>{{ $modified }}</lastmod>
        <priority>{{ $url['priority'] }}</priority>
    </url>
@endforeach
</urlset>
