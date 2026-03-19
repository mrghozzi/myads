<?php

namespace App\Support;

class LinkEmbedCode
{
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
  var src='{$bootstrapUrl}?ID={$publisherId}&px=responsive2';
  if (width > 0) {
    src += '&cw=' + encodeURIComponent(width);
  }
  d.write('<scr' + 'ipt type="text/javascript" src="' + src + '"><\/scr' + 'ipt>');
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

    private static function appendExtensionsCode(string $snippet, string $extensionsCode = ''): string
    {
        $extensionsCode = trim($extensionsCode);

        if ($extensionsCode === '') {
            return $snippet;
        }

        return $snippet . "\n" . $extensionsCode;
    }
}
