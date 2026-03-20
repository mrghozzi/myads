<?php

namespace App\Support;

class BannerEmbedCode
{
    public static function buildLegacy(string $scriptUrl, int $publisherId, string $size, string $extensionsCode = ''): string
    {
        $requestedSize = BannerSizeCatalog::normalize($size) ?? BannerSizeCatalog::default();
        $legacyAlias = BannerSizeCatalog::legacyAlias($requestedSize) ?? $requestedSize;
        $bootstrapUrl = rtrim($scriptUrl, '?');
        $snippet = '<script language="javascript" src="' . $bootstrapUrl . '?ID=' . $publisherId . '&px=' . $legacyAlias . '"></script>';

        return self::appendExtensionsCode($snippet, $extensionsCode);
    }

    public static function build(string $scriptUrl, int $publisherId, string $size, string $extensionsCode = ''): string
    {
        if ($size === 'responsive2') {
            return self::buildResponsive2($scriptUrl, $publisherId, $extensionsCode);
        }

        $requestedSize = $size === 'responsive'
            ? 'responsive'
            : (BannerSizeCatalog::normalize($size) ?? BannerSizeCatalog::default());

        $bootstrapUrl = rtrim($scriptUrl, '?');

        $loader = <<<JS
<script type="text/javascript">
(function(w,d){
  var key='myads_banner_vt';
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
  var px='{$requestedSize}';
  if (px === 'responsive') {
    var current = document.currentScript || d.currentScript;
    var parent = current && current.parentNode ? current.parentNode : null;
    var width = 0;
    if (parent && parent.getBoundingClientRect) {
      width = Math.round(parent.getBoundingClientRect().width || 0);
    }
    if (!width && parent) {
      width = parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
    }
    if (!width) {
      width = parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    if (width >= 728) {
      px = '728x90';
    } else if (width >= 468) {
      px = '468x60';
    } else if (width >= 300) {
      px = '300x250';
    } else if (width >= 160) {
      px = '160x600';
    } else {
      px = '300x250';
    }
  }
  var src='{$bootstrapUrl}?ID={$publisherId}&px=' + encodeURIComponent(px);
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  d.write('<scr' + 'ipt type="text/javascript" src="' + src + '"><\/scr' + 'ipt>');
})(window,document);
</script>
JS;

        return self::appendExtensionsCode($loader, $extensionsCode);
    }

    public static function buildResponsive2(string $scriptUrl, int $publisherId, string $extensionsCode = ''): string
    {
        $bootstrapUrl = rtrim($scriptUrl, '?');

        $loader = <<<JS
<script type="text/javascript">
(function(w,d){
  var key='myads_banner_vt';
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
  var current = document.currentScript || d.currentScript;
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
  var src='{$bootstrapUrl}?ID={$publisherId}&px=responsive2';
  if (width > 0) {
    src += '&cw=' + encodeURIComponent(width);
  }
  if (height > 0) {
    src += '&ch=' + encodeURIComponent(height);
  }
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  d.write('<scr' + 'ipt type="text/javascript" src="' + src + '"><\/scr' + 'ipt>');
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
