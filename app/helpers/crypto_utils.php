<?php
/**
 * Encode binary data in Base64 URL-safe format without padding.
 */
function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Decode Base64 URL-safe data.
 *
 * @return string|false Returns the decoded string or false on failure.
 */
function base64url_decode(string $data)
{
    $padding = strlen($data) % 4;
    if ($padding !== 0) {
        $data .= str_repeat('=', 4 - $padding);
    }

    return base64_decode(strtr($data, '-_', '+/'), true);
}