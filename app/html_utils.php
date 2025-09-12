<?php
/**
 * Escape HTML output.
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Safe XML parser with entity loader disabled.
 */
function parseXml(string $xml): \DOMDocument {
    $dom = new \DOMDocument();
    $dom->loadXML($xml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    return $dom;
}