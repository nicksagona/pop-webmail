<?php
function decodeText($text) {
    $decodedValues = imap_mime_header_decode($text);
    $decoded       = '';

    foreach ($decodedValues as $string) {
        $decoded .= $string->text;
    }

    return $decoded;
}

function getAttachments($parts, $id) {
    $attachments = [];

    foreach ($parts as $i => $part) {
        if ($part->attachment) {
            $attachments[] = '<a href="/mail/attachments/' . $id . '/' . ($i + 1) . '">' .
                (!empty($part->basename) ? $part->basename : 'file_' . ($i + 1)) . '</a>';
        }
    }

    return $attachments;
}

function getContent($parts)
{
    $text         = null;
    $html         = null;
    $foundContent = null;

    foreach ($parts as $i => $part) {
        if (!$part->attachment) {
            $content = (base64_decode($part->content, true) !== false) ? base64_decode($part->content, true) : $part->content;
            if ($content == strip_tags($content)) {
                $content = nl2br(convertLinks($content, true));
            }
            if (stripos($content, '<body')) {
                $content = substr($content, stripos($content, '<body'));
                $content = substr($content, (stripos($content, '>') + 1));
                $content = trim(substr($content, 0, stripos($content, '</body>')));
            }
            if ($part->type == 'text/html') {
                $html = $content;
            } else if ($part->type == 'text/plain') {
                $text = $content;
            }
        }
    }

    if (null !== $html) {
        $foundContent = $html;
    } else if (null !== $text) {
        $foundContent = $text;
    }

    return $foundContent;
}