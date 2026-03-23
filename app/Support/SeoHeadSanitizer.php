<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SeoHeadSanitizer
{
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
                $fragments[] = trim($output->saveHTML($cleanNode));
            }
        }

        return trim(implode(PHP_EOL, array_filter($fragments)));
    }

    private function sanitizeNode(DOMDocument $output, DOMNode $node): ?DOMNode
    {
        if (!$node instanceof DOMElement) {
            return null;
        }

        return match (strtolower($node->tagName)) {
            'meta' => $this->sanitizeMeta($output, $node),
            'link' => $this->sanitizeLink($output, $node),
            'script' => $this->sanitizeJsonLd($output, $node),
            default => null,
        };
    }

    private function sanitizeMeta(DOMDocument $output, DOMElement $node): DOMElement
    {
        $element = $output->createElement('meta');

        foreach (['name', 'content', 'property', 'http-equiv', 'charset'] as $attribute) {
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

        $element = $output->createElement('link');

        foreach (['rel', 'href', 'as', 'type', 'sizes', 'media', 'crossorigin'] as $attribute) {
            if ($node->hasAttribute($attribute)) {
                $element->setAttribute($attribute, trim($node->getAttribute($attribute)));
            }
        }

        return $element;
    }

    private function sanitizeJsonLd(DOMDocument $output, DOMElement $node): ?DOMElement
    {
        $type = trim(strtolower($node->getAttribute('type')));
        $content = trim($node->textContent);

        if ($type !== 'application/ld+json' || $content === '') {
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
}
