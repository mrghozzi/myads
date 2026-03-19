<?php

namespace App\Support;

class SmartAdEmbedCode
{
    public static function build(string $scriptUrl, int $publisherId, string $extensionsCode = ''): string
    {
        $bootstrapUrl = rtrim($scriptUrl, '?');

        $loader = <<<JS
<script type="text/javascript">
(function(w,d){
  var key='myads_smart_vt';
  var vt='';
  try {
    vt = w.localStorage ? (w.localStorage.getItem(key) || '') : '';
  } catch (e) {}
  if (!vt) {
    var cookieMatch = d.cookie.match(new RegExp('(?:^|; )' + key + '=([^;]*)'));
    if (cookieMatch) {
      vt = decodeURIComponent(cookieMatch[1]);
    }
  }
  if (!vt) {
    vt = 'vt-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    try {
      if (w.localStorage) {
        w.localStorage.setItem(key, vt);
      }
    } catch (e) {}
    try {
      d.cookie = key + '=' + encodeURIComponent(vt) + '; path=/; max-age=31536000; SameSite=Lax';
    } catch (e) {}
  }
  var current = d.currentScript || document.currentScript;
  var parent = current && current.parentNode ? current.parentNode : null;
  var width = 0;
  var height = 0;
  if (parent && parent.getBoundingClientRect) {
    var rect = parent.getBoundingClientRect();
    width = Math.round(rect.width || 0);
    height = Math.round(rect.height || 0);
  }
  if ((!width || !height) && parent) {
    width = width || parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
    height = height || parseInt(parent.clientHeight || parent.offsetHeight || 0, 10) || 0;
  }
  if (!width) {
    width = parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
  }
  var ua = (navigator.userAgent || '').toLowerCase();
  var isTablet = /(ipad|tablet|playbook|silk)|(android(?!.*mobile))/i.test(ua);
  var isMobile = !isTablet && /(iphone|ipod|android|mobile)/i.test(ua);
  var deviceType = isTablet ? 'tablet' : (isMobile ? 'mobile' : 'desktop');
  function readMeta(name, attr) {
    var node = d.querySelector('meta[' + attr + '=\"' + name + '\"]');
    return node ? (node.getAttribute('content') || '') : '';
  }
  function normalizeText(value) {
    return (value || '').replace(/\\s+/g, ' ').trim();
  }
  var contextParts = [];
  contextParts.push(normalizeText(d.title || ''));
  contextParts.push(normalizeText(readMeta('keywords', 'name')));
  contextParts.push(normalizeText(readMeta('description', 'name')));
  contextParts.push(normalizeText(readMeta('og:title', 'property')));
  contextParts.push(normalizeText(readMeta('og:description', 'property')));
  var heading = d.querySelector('h1, h2, h3');
  if (heading) {
    contextParts.push(normalizeText(heading.textContent || ''));
  }
  var article = d.querySelector('article, main, [role=\"main\"], .content, .post, .entry, body');
  if (article) {
    contextParts.push(normalizeText((article.textContent || '').slice(0, 320)));
  }
  var context = normalizeText(contextParts.filter(Boolean).join(' | ')).slice(0, 500);
  var src='{$bootstrapUrl}?ID={$publisherId}';
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  if (width > 0) {
    src += '&cw=' + encodeURIComponent(width);
  }
  if (height > 0) {
    src += '&ch=' + encodeURIComponent(height);
  }
  if (deviceType) {
    src += '&dv=' + encodeURIComponent(deviceType);
  }
  if (context) {
    src += '&ctx=' + encodeURIComponent(context);
  }
  d.write('<scr' + 'ipt type=\"text/javascript\" src=\"' + src + '\"><\\/scr' + 'ipt>');
})(window,document);
</script>
JS;

        return self::appendExtensionsCode($loader, $extensionsCode);
    }

    private static function appendExtensionsCode(string $snippet, string $extensionsCode = ''): string
    {
        $extensionsCode = trim($extensionsCode);

        if ($extensionsCode === '') {
            return $snippet;
        }

        return $snippet . "\n" . $extensionsCode;
    }
}
