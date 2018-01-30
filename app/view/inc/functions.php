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