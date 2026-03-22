<?php

namespace App\Support;

class LinkEmbedCode
{
    public static function build(string $scriptUrl, int $publisherId, string $mode, string $extensionsCode = ''): string
    {
        $bootstrapUrl = rtrim($scriptUrl, '?');
        $normalizedMode = self::normalizeRecommendedMode($mode);
        $snippet = '<script type="text/javascript" src="' . $bootstrapUrl . '?ID=' . $publisherId . '&px=' . rawurlencode($normalizedMode) . '"></script>';

        return self::appendExtensionsCode($snippet, $extensionsCode);
    }

    public static function buildDirect(string $scriptUrl, int $publisherId, string $mode, string $extensionsCode = ''): string
    {
        $bootstrapUrl = rtrim($scriptUrl, '?');
        $normalizedMode = self::normalizeMode($mode);
        $snippet = '<script type="text/javascript" src="' . $bootstrapUrl . '?ID=' . $publisherId . '&px=' . $normalizedMode . '"></script>';

        return self::appendExtensionsCode($snippet, $extensionsCode);
    }

    public static function buildResponsive2Smart(string $scriptUrl, int $publisherId, string $extensionsCode = ''): string
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
    return width;
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
    var slot = createSlot(current);
    if (!slot) {
      return;
    }
    var width = measureBox(slot);
    var src='{$bootstrapUrl}?ID={$publisherId}&px=responsive2';
    if (width > 0) {
      src += '&cw=' + encodeURIComponent(width);
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

    private static function normalizeMode(string $mode): string
    {
        return match (trim($mode)) {
            '1' => '468x60',
            '2', 'responsive' => '510x320',
            default => trim($mode),
        };
    }

    private static function normalizeRecommendedMode(string $mode): string
    {
        return match (trim($mode)) {
            '1' => '468x60',
            '2', '510x320' => 'responsive',
            default => trim($mode),
        };
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
