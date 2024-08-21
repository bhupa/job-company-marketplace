<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', "{{ env('VITE_GOOGLE_TAG_MANAGER_ID') }}");
  </script>
  <!-- End Google Tag Manager -->

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JobMatchy</title>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- SEO Meta Tags -->
  <meta name="description" content="JobMatchy is the innovative platform connecting companies with job seekers. Whether you're a foreigner living in or outside Japan, find your perfect job match or hire top talent with ease. Experience a new way of job hunting and hiring in Japan today!">

  <!-- Facebook Meta Tags-->
  <meta property="og:type" content="website" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:url" content="{{ env('VITE_BASE_URL') }}" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Revolutionize Your Job Search and Hiring in Japan | JobMatchy: The Ultimate Job Matching App for Foreigners" />
  <meta property="og:description" content="JobMatchy is the innovative platform connecting companies with job seekers. Whether you're a foreigner living in or outside Japan, find your perfect job match or hire top talent with ease. Experience a new way of job hunting and hiring in Japan today!" />
  <meta property="og:image" content="{{ asset('seo/og-image.jpg') }}" />

  <!-- Twitter Meta Tags -->
  <meta name="twitter:card" content="{{ asset('seo/og-image.jpg') }}" />
  <meta property="twitter:domain" content="{{ env('VITE_BASE_URL') }}" />
  <meta property="twitter:url" content="{{ env('VITE_BASE_URL') }}" />
  <meta name="twitter:title" content="Revolutionize Your Job Search and Hiring in Japan | JobMatchy: The Ultimate Job Matching App for Foreigners" />
  <meta name="twitter:description" content="JobMatchy is the innovative platform connecting companies with job seekers. Whether you're a foreigner living in or outside Japan, find your perfect job match or hire top talent with ease. Experience a new way of job hunting and hiring in Japan today!" />
  <meta name="twitter:image" content="{{ asset('seo/og-image.jpg') }}" />

  <meta property="og:site_name" content="JobMatchy" />

  @vite('resources/css/app.css')
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id={{ env('VITE_GOOGLE_TAG_MANAGER_ID') }}" height="0" width="0" style="display:none;visibility:hidden">
    </iframe>
  </noscript>
  <!-- End Google Tag Manager (noscript) -->
  <div id="app"></div>
  @viteReactRefresh
  @vite(['resources/js/app.jsx'])
</body>

</html>