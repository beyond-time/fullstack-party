<?php

namespace GitHub\Helpers;

class Template
{
    static function formatDate($date)
    {
        $units = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        $diff = (new \DateTime($date))->diff(new \DateTime);

        foreach ($units as $attribute => $unit) {
            $count = $diff->{$attribute};
            if ($count) {
                if ($count !== 1) {
                    $unit .= 's';
                }
                return $diff->invert ? "in $count $unit" : "$count $unit ago";
            }
        }

        return 'right now';
    }

    static function formatIssueUrl($url)
    {
        $parts = explode('/repos/', $url);
        return '/' . $parts[1];
    }

    static function generatePagination($total, $current)
    {
        $links = [];

        $current_page_class = 'pager__item--current';
        $html_link = function ($title, $index, $class = '') {
            return '<a href="/pages/' . $index . '" class="pager__item ' . $class . '">' . $title . '</a>';
        };

        if ($total >= 6) {
            if ($total == $current || ($total - $current == 1)) {
                $links[0] = $html_link(1, 1);
                $links[1] = $html_link(2, 2);
                $links[2] = '..';

                for ($i = $total - 2; $i <= $total; $i++) {
                    $links[] = $html_link($i, $i, $i == $current ? $current_page_class : '');
                }
            } elseif ($current >= 3) {
                $links[0] = $html_link(1, 1);
                $links[1] = '..';

                $page = $current - 1;
                $links[2] = $html_link($page, $page);

                $links[3] = $html_link($current, $current, $current_page_class);

                $page = $current + 1;
                $links[4] = $html_link($page, $page);

                $links[5] = '..';
                $links[6] = $html_link($total, $total);
            } else {
                for ($i = 1; $i < 3; $i++) {
                    $links[] = $html_link($i, $i, $i == $current ? $current_page_class : '');
                }
                $links[3] = '...';

                $page = $total - 1;
                $links[4] = $html_link($page, $page);

                $links[5] = $html_link($total, $total);
            }
        } else {
            for ($i = 1; $i <= $total; $i++) {
                $links[] = $html_link($i, $i, $i == $current ? $current_page_class : '');
            }
        }

        $previous = $current == 1 ? 'Previous' : '<a href="/pages/' . ($current - 1) . '" class="pager__prev">Previous</a>';
        $next = ($current == $total || $total == 1) ? 'Next' : '<a href="/pages/' . ($current + 1) . '" class="pager__next">Next</a>';


        array_unshift($links, $previous);
        $links[] = $next;

        return implode($links);
    }
}
