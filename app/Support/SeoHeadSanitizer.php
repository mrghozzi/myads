<?php

namespace App\Support;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMNode;

class SeoHeadSanitizer
{
    private const ALLOWED_META_ATTRIBUTES = [
        'name',
        'content',
        'property',
        'http-equiv',
        'charset',
        'id',
        'class',
        'media',
    ];

    private const ALLOWED_LINK_ATTRIBUTES = [
        'rel',
        'href',
        'as',
        'type',
        'sizes',
        'media',
        'crossorigin',
        'integrity',
        'referrerpolicy',
        'title',
        'imagesrcset',
        'imagesizes',
        'id',
        'class',
    ];

    private const ALLOWED_SCRIPT_ATTRIBUTES = [
        'src',
        'async',
        'defer',
        'type',
        'crossorigin',
        'integrity',
        'nomodule',
        'referrerpolicy',
        'nonce',
        'id',
        'class',
        'charset',
        'fetchpriority',
    ];

    public function sanitize(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $source = new DOMDocument('1.0', 'UTF-8');
        $output = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);
        $source->loadHTML(
            '<?xml encoding="utf-8" ?><div id="seo-head-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $source->getElementById('seo-head-root');
        if (!$root) {
            return '';
        }

        $fragments = [];

        foreach ($root->childNodes as $node) {
            $cleanNode = $this->sanitizeNode($output, $node);
            if ($cleanNode instanceof DOMNode) {
                $output->appendChild($cleanNode);
                $htmlFragment = trim($output->saveHTML($cleanNode));
                // Normalize boolean attributes for cleaner HTML5 markup (e.g. async="" to async)
                $htmlFragment = preg_replace('/\s(async|defer|nomodule)=""/i', ' $1', $htmlFragment);
                $fragments[] = $htmlFragment;
            }
        }

        return trim(implode(PHP_EOL, array_filter($fragments)));
    }

    private function sanitizeNode(DOMDocument $output, DOMNode $node): ?DOMNode
    {
        if ($node instanceof DOMComment) {
            return $output->createComment($node->nodeValue);
        }

        if (!$node instanceof DOMElement) {
            return null;
        }

        return match (strtolower($node->tagName)) {
            'meta' => $this->sanitizeMeta($output, $node),
            'link' => $this->sanitizeLink($output, $node),
            'script' => $this->sanitizeScript($output, $node),
            'style' => $this->sanitizeStyle($output, $node),
            'noscript' => $this->sanitizeNoscript($output, $node),
            default => null,
        };
    }

    private function sanitizeMeta(DOMDocument $output, DOMElement $node): DOMElement
    {
        $element = $output->createElement('meta');

        foreach (self::ALLOWED_META_ATTRIBUTES as $attribute) {
            if ($node->hasAttribute($attribute)) {
                $element->setAttribute($attribute, trim($node->getAttribute($attribute)));
            }
        }

        return $element;
    }

    private function sanitizeLink(DOMDocument $output, DOMElement $node): ?DOMElement
    {
        if (!$node->hasAttribute('rel') || !$node->hasAttribute('href')) {
            return null;
        }

        $href = trim($node->getAttribute('href'));
        if (preg_match('/^(?:javascript|vbscript):/i', $href)) {
            return null;
        }

        $element = $output->createElement('link');

        foreach (self::ALLOWED_LINK_ATTRIBUTES as $attribute) {
            if ($node->hasAttribute($attribute)) {
                $element->setAttribute($attribute, trim($node->getAttribute($attribute)));
            }
        }

        return $element;
    }

    private function sanitizeScript(DOMDocument $output, DOMElement $node): ?DOMElement
    {
        $type = strtolower(trim($node->getAttribute('type')));
        $content = trim($node->textContent);
        $hasSrc = $node->hasAttribute('src');
        $src = trim($node->getAttribute('src'));

        // Handle JSON-LD structured data
        if ($type === 'application/ld+json') {
            if ($content === '') {
                return null;
            }

            json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            $element = $output->createElement('script');
            $element->setAttribute('type', 'application/ld+json');
            $element->appendChild($output->createTextNode($content));

            return $element;
        }

        // Block javascript: or vbscript: pseudoprotocols in src
        if ($hasSrc && preg_match('/^(?:javascript|vbscript):/i', $src)) {
            return null;
        }

        // If not JSON-LD and has neither src nor content, skip empty script
        if (!$hasSrc && $content === '') {
            return null;
        }

        // Validate type attribute if present (allow standard js types or empty)
        $allowedTypes = [
            '',
            'text/javascript',
            'application/javascript',
            'module',
        ];
        if (!in_array($type, $allowedTypes, true)) {
            return null;
        }

        $element = $output->createElement('script');

        // Copy allowed attributes and any data-* attributes (such as data-ad-client)
        foreach ($node->attributes as $attr) {
            $name = strtolower($attr->name);
            if (in_array($name, self::ALLOWED_SCRIPT_ATTRIBUTES, true) || str_starts_with($name, 'data-')) {
                if ($name === 'src') {
                    $element->setAttribute('src', $src);
                } elseif (in_array($name, ['async', 'defer', 'nomodule'], true)) {
                    $element->setAttribute($name, '');
                } else {
                    $element->setAttribute($attr->name, trim($attr->value));
                }
            }
        }

        if ($content !== '') {
            $element->appendChild($output->createTextNode($node->textContent));
        }

        return $element;
    }

    private function sanitizeStyle(DOMDocument $output, DOMElement $node): ?DOMElement
    {
        $content = trim($node->textContent);
        if ($content === '') {
            return null;
        }

        $element = $output->createElement('style');
        foreach (['type', 'media', 'id', 'class', 'nonce'] as $attribute) {
            if ($node->hasAttribute($attribute)) {
                $element->setAttribute($attribute, trim($node->getAttribute($attribute)));
            }
        }
        $element->appendChild($output->createTextNode($node->textContent));

        return $element;
    }

    private function sanitizeNoscript(DOMDocument $output, DOMElement $node): ?DOMElement
    {
        $element = $output->createElement('noscript');
        foreach ($node->childNodes as $child) {
            $imported = $output->importNode($child, true);
            $element->appendChild($imported);
        }

        return $element;
    }
}
