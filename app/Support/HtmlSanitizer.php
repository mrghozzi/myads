<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    private const STRIP_WITH_CONTENT_TAGS = [
        'script',
        'style',
        'noscript',
        'template',
        'object',
        'embed',
        'applet',
        'svg',
        'math',
        'form',
        'input',
        'button',
        'textarea',
        'select',
        'option',
        'meta',
        'link',
        'base',
        'frame',
        'frameset',
        'title',
    ];

    private const ALLOWED_TAGS = [
        'p', 'br', 'b', 'strong', 'i', 'em', 'u', 's', 'strike', 'del', 'ins',
        'sub', 'sup', 'blockquote', 'pre', 'code', 'hr',
        'ul', 'ol', 'li',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'a', 'img', 'font', 'span', 'div', 'figure', 'figcaption',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'iframe',
    ];

    private const GLOBAL_ALLOWED_ATTRIBUTES = [
        'class', 'dir', 'title', 'alt', 'align', 'colspan', 'rowspan', 'style',
    ];

    private const TAG_ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'font' => ['color', 'size', 'face'],
        'iframe' => ['src', 'title', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen'],
    ];

    /**
     * Sanitize rich HTML content (e.g., Quill/Markdown forum topics and descriptions)
     * preserving safe formatting markup while stripping scripts, event handlers,
     * unsafe URI schemes, and untrusted embeds.
     */
    public static function sanitize(?string $html): string
    {
        $raw = (string) $html;
        if (trim($raw) === '') {
            return '';
        }

        if (!str_contains($raw, '<')) {
            return $raw;
        }

        $rootId = '__myads_html_sanitizer_root__';
        $wrapped = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><div id="' . $rootId . '">' . $raw . '</div></body></html>';

        $previousErrorState = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $loaded = @$dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorState);

        if (!$loaded) {
            return e($raw);
        }

        $root = $dom->getElementById($rootId);
        if (!$root instanceof DOMElement) {
            return e($raw);
        }

        self::sanitizeChildren($root);

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }

    private static function sanitizeChildren(DOMNode $parent): void
    {
        $children = [];
        foreach ($parent->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $node) {
            if ($node->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($node);
                continue;
            }

            if (!$node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, self::STRIP_WITH_CONTENT_TAGS, true)) {
                $parent->removeChild($node);
                continue;
            }

            if ($tag === 'iframe') {
                $src = trim($node->getAttribute('src'));
                if (!self::isAllowedEmbedUrl($src)) {
                    $parent->removeChild($node);
                    continue;
                }
            }

            // First sanitize descendants before potential unwrapping
            self::sanitizeChildren($node);

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($node->firstChild !== null) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);
                continue;
            }

            self::sanitizeAttributes($node, $tag);
        }
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowedForTag = self::TAG_ALLOWED_ATTRIBUTES[$tag] ?? [];
        $allowed = array_merge(self::GLOBAL_ALLOWED_ATTRIBUTES, $allowedForTag);

        $attrNames = [];
        foreach ($element->attributes as $attr) {
            $attrNames[] = $attr->name;
        }

        foreach ($attrNames as $name) {
            $lower = strtolower($name);

            if (str_starts_with($lower, 'on') || !in_array($lower, $allowed, true)) {
                $element->removeAttribute($name);
                continue;
            }

            $value = $element->getAttribute($name);

            if ($lower === 'href' || $lower === 'src') {
                $cleanUrl = self::sanitizeUrl($value, $tag === 'img');
                if ($cleanUrl === null) {
                    $element->removeAttribute($name);
                } else {
                    $element->setAttribute($name, $cleanUrl);
                }
                continue;
            }

            if ($lower === 'style') {
                $cleanStyle = self::sanitizeStyle($value);
                if ($cleanStyle === null) {
                    $element->removeAttribute($name);
                } else {
                    $element->setAttribute($name, $cleanStyle);
                }
                continue;
            }

            if ($lower === 'target') {
                $target = strtolower(trim($value));
                if (!in_array($target, ['_blank', '_self'], true)) {
                    $element->removeAttribute($name);
                } elseif ($target === '_blank' && $tag === 'a') {
                    $element->setAttribute('rel', 'noopener noreferrer');
                }
            }
        }

        if ($tag === 'a' && strtolower($element->getAttribute('target')) === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function sanitizeUrl(string $url, bool $isImage): ?string
    {
        $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleaned = preg_replace('/[\x00-\x20\x7F]+/', '', $decoded) ?? '';
        $trim = trim($url);

        if ($trim === '' || $cleaned === '') {
            return null;
        }

        if (preg_match('/^(?:javascript|vbscript|file|jar):/i', $cleaned) === 1) {
            return null;
        }

        if (str_starts_with(strtolower($cleaned), 'data:')) {
            if ($isImage && preg_match('/^data:image\/(?:png|jpeg|jpg|gif|webp);base64,[a-zA-Z0-9+\/=]+$/i', $cleaned) === 1) {
                return $cleaned;
            }
            return null;
        }

        if (preg_match('/^(?:https?:\/\/|mailto:|\/(?!\/)|#)/i', $cleaned) === 1) {
            return $trim;
        }

        // Allow clean relative path segments like upload/img.png without colon schemes
        if (!str_contains(explode('/', explode('?', explode('#', $cleaned)[0])[0])[0], ':')) {
            return $trim;
        }

        return null;
    }

    private static function sanitizeStyle(string $style): ?string
    {
        $strippedComments = preg_replace('/\/\*.*?\*\//s', '', $style) ?? '';
        $normalized = preg_replace('/\\\\[0-9a-fA-F]{1,6}\s?/', '', $strippedComments) ?? $strippedComments;

        if (
            preg_match('/(?:expression\s*\(|javascript:|vbscript:|data:|behavior\s*:|-moz-binding|@import|url\s*\()/i', $normalized) === 1
        ) {
            return null;
        }

        $trimmed = trim($style);
        return $trimmed === '' ? null : $trimmed;
    }

    private static function isAllowedEmbedUrl(string $src): bool
    {
        return preg_match(
            '#^https://(?:www\.)?(?:youtube\.com|youtube-nocookie\.com)/embed/[A-Za-z0-9_-]{11}(?:\?[^"\'\s]*)?$#i',
            $src
        ) === 1;
    }
}
