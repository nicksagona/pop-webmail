<?php

function getSort($sort) {
    if (isset($_GET['sort']) && ($_GET['sort'] == $sort)) {
        $sort = (substr($sort, 0, 1) == '-') ? substr($sort, 1) : '-' . $sort;
    }
    return $sort;
}

function getQuery($omit = null, $amp = true) {
    $queryString = null;

    if (!empty($_GET)) {
        $query = $_GET;
        if (null !== $omit) {
            if (!is_array($omit)) {
                $omit = [$omit];
            }
            foreach ($omit as $field) {
                if (isset($query[$field])) {
                    unset($query[$field]);
                }
            }
        }

        if (!empty($query)) {
            $queryString = http_build_query($query);
            if ($amp) {
                $queryString = '&' . $queryString;
            }
        }
    }

    return $queryString;
}

function getFilesize($size) {
    $result = '';
    if ($size >= 1000000) {
        $result = round(($size / 1000000), 2) . ' MB';
    } else if (($size < 1000000) && ($size >= 1000)) {
        $result = round(($size / 1000), 2) . ' KB';
    } else if ($size < 1000) {
        $result = $size . ' B';
    }
    return $result;
}

function convertLinks($string, $target = null) {
    $target = (null !== $target) ? 'target="' . $target . '" ' : '';
    $string = preg_replace('/[ftp|http|https]+:\/\/[^\s]*/', '<a href="$0">$0</a>', $string);
    $string = preg_replace('/\s[\w]+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,4})/', ' <a href="http://$0">$0</a>', $string);
    $string = preg_replace('/[a-zA-Z0-9\.\-\_+%]+@[a-zA-Z0-9\-\_\.]+\.[a-zA-Z]{2,4}/', '<a href="mailto:$0">$0</a>', $string);
    $string = str_replace(
        [
            'href="http:// ',
            'href="https:// ',
            '"> ',
            '<a '
        ],
        [
            'href="http://',
            'href="https://',
            '">',
            '<a ' . $target
        ],
        $string
    );
    return $string;
}