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
        $requestedSize = $size === 'responsive'
            ? 'responsive'
            : ($size === 'responsive2'
                ? 'responsive2'
                : (BannerSizeCatalog::normalize($size) ?? BannerSizeCatalog::default()));

        $bootstrapUrl = rtrim($scriptUrl, '?');
        $snippet = '<script type="text/javascript" src="' . $bootstrapUrl . '?ID=' . $publisherId . '&px=' . rawurlencode($requestedSize) . '"></script>';

        return self::appendExtensionsCode($snippet, $extensionsCode);
    }

    public static function buildInlineLoader(string $scriptUrl, int $publisherId, string $size, string $extensionsCode = ''): string
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
  var current = document.currentScript || d.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-slot','1');
    if (script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head') {
      script.parentNode.insertBefore(slot, script);
      return slot;
    }
    if (d.body) {
      d.body.insertBefore(slot, d.body.firstChild || null);
      return slot;
    }
    return null;
  }
  function measureBox(node){
    var width=0;
    if (node && node.getBoundingClientRect) {
      width=Math.round(node.getBoundingClientRect().width || 0);
    }
    if (!width && node) {
      width=parseInt(node.clientWidth || node.offsetWidth || 0, 10) || 0;
    }
    var parent=node && node.parentNode ? node.parentNode : null;
    if (!width && parent && parent.getBoundingClientRect) {
      width=Math.round(parent.getBoundingClientRect().width || 0) || 0;
    }
    if (!width && parent) {
      width=parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
    }
    if (!width) {
      width=parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    return {width: width};
  }
  function appendLoader(slot, src){
    var loader=d.createElement('script');
    loader.type='text/javascript';
    loader.src=src;
    if (slot && slot.parentNode) {
      slot.parentNode.insertBefore(loader, slot.nextSibling || null);
      return;
    }
    (d.body || d.head || d.documentElement).appendChild(loader);
  }
  function boot(){
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
    var slot = createSlot(current);
    if (!slot) {
      return;
    }
    var width = measureBox(slot).width;
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
  } else {
    var slot = createSlot(current);
    if (!slot) {
      return;
    }
  }
  var src='{$bootstrapUrl}?ID={$publisherId}&px=' + encodeURIComponent(px);
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  src += '&slot=' + encodeURIComponent(slot.id);
  appendLoader(slot, src);
  }
  if (d.body) {
    boot();
    return;
  }
  d.addEventListener('DOMContentLoaded', boot, false);
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
  var current = document.currentScript || d.currentScript || (function(){var scripts=d.getElementsByTagName('script');return scripts[scripts.length-1]||null;})();
  function createSlot(script){
    var slot=d.createElement('div');
    slot.id='myads-slot-' + Math.random().toString(36).slice(2) + Date.now().toString(36);
    slot.setAttribute('data-myads-slot','1');
    if (script && script.parentNode && String(script.parentNode.tagName || '').toLowerCase() !== 'head') {
      script.parentNode.insertBefore(slot, script);
      return slot;
    }
    if (d.body) {
      d.body.insertBefore(slot, d.body.firstChild || null);
      return slot;
    }
    return null;
  }
  function measureBox(node){
    var width=0;
    var height=0;
    if (node && node.getBoundingClientRect) {
      var rect=node.getBoundingClientRect();
      width=Math.round(rect.width || 0);
      height=Math.round(rect.height || 0);
    }
    if ((!width || !height) && node) {
      width=width || parseInt(node.clientWidth || node.offsetWidth || 0, 10) || 0;
      height=height || parseInt(node.clientHeight || node.offsetHeight || 0, 10) || 0;
    }
    var parent=node && node.parentNode ? node.parentNode : null;
    if ((!width || !height) && parent && parent.getBoundingClientRect) {
      var parentRect=parent.getBoundingClientRect();
      width=width || Math.round(parentRect.width || 0);
      height=height || Math.round(parentRect.height || 0);
    }
    if ((!width || !height) && parent) {
      width=width || parseInt(parent.clientWidth || parent.offsetWidth || 0, 10) || 0;
      height=height || parseInt(parent.clientHeight || parent.offsetHeight || 0, 10) || 0;
    }
    if (!width) {
      width=parseInt(w.innerWidth || d.documentElement.clientWidth || 0, 10) || 0;
    }
    return {width: width, height: height};
  }
  function appendLoader(slot, src){
    var loader=d.createElement('script');
    loader.type='text/javascript';
    loader.src=src;
    if (slot && slot.parentNode) {
      slot.parentNode.insertBefore(loader, slot.nextSibling || null);
      return;
    }
    (d.body || d.head || d.documentElement).appendChild(loader);
  }
  function boot(){
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
  var slot = createSlot(current);
  if (!slot) {
    return;
  }
  var metrics = measureBox(slot);
  var src='{$bootstrapUrl}?ID={$publisherId}&px=responsive2';
  if (metrics.width > 0) {
    src += '&cw=' + encodeURIComponent(metrics.width);
  }
  if (metrics.height > 0) {
    src += '&ch=' + encodeURIComponent(metrics.height);
  }
  if (vt) {
    src += '&vt=' + encodeURIComponent(vt);
  }
  src += '&slot=' + encodeURIComponent(slot.id);
  appendLoader(slot, src);
  }
  if (d.body) {
    boot();
    return;
  }
  d.addEventListener('DOMContentLoaded', boot, false);
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
