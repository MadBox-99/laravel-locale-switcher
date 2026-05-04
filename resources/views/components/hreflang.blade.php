@foreach (\MadBox\LocaleSwitcher\LocaleSwitcher::alternates() as $hreflang => $url)
<link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $url }}" />
@endforeach
