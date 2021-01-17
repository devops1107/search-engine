<?php

namespace spark\drivers\Nav;

/**
* BreadCrumbs Generator
*
* @package spark
*/
class BreadCrumbs
{
    protected $links = [];

    public function add($id, $label, $url)
    {
        $this->links[$id] = [
            'label' => $label,
            'url'   => $url
        ];
        return $this;
    }

    public function remove($id)
    {
        unset($this->links[$id]);
        return $this;
    }

    public function getAll()
    {
        return $links;
    }

    public function renderJson($scriptTags = true)
    {
        $return = '';

        if ($scriptTags) {
            $return .= '<script type="application/ld+json">';
        }

        $data = [
            '@context' => 'http://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $i = 0;

        foreach ($this->links as $link) {
            $i++;
            $data['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $i,
                'item' => [
                    '@id' => html_escape($link['url']),
                    'name' => html_escape($link['label']),
                ],
            ];
        }

        $return .= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($scriptTags) {
            $return .= '</script>';
        }

        return $return;
    }


    public function renderHtml($before = '', $after = '', array $attrs = [])
    {
        $links = $this->links;

        if (!is_array($links) || empty($links)) {
            return '';
        }

        $total = count($links);

        if ($total < 2) {
            return '';
        }

        $defaultAttrs = [
            'class' => 'breadcrumb',
            'itemscope' => '',
            'itemtype' => 'https://schema.org/BreadcrumbList'
        ];
        $attrs = array_merge($defaultAttrs, $attrs);

        $attrHTML = null;

        foreach ($attrs as $key => $value) {
            $key = e_attr($key);
            if (empty($value)) {
                $attrHTML .= " {$key}";
            } else {
                $attrHTML .= ' ' . $key . '="' . e_attr($value) . '"';
            }
        }


        $breadcrumbs = '';
        $breadcrumbs .= $before;
        $breadcrumbs .= '<ol ' . $attrHTML . '>';

        $i = 1;

        $markup = ' itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem" ';

        foreach ($links as $link) {
            $label = html_escape($link['label'], false);
            $url = html_escape($link['url'], false);

            $labelMarkup = '<span itemprop="name">' . $label . '</span>';

            $meta = '<meta itemprop="position" content="' . $i . '" />';

            if ($i === $total) {
                $breadcrumbs .= '<li' . $markup . ' class="breadcrumb-item active">' . $labelMarkup . ' ' . $meta . '</li>';
            } else {
                $breadcrumbs .= '
                <li ' . $markup . ' class="breadcrumb-item">
                <a class="sp-link" href="' . $url . '" itemscope itemtype="https://schema.org/WebPage"
                itemprop="item" itemid="'.$url.'">' . $labelMarkup . ' </a> ' . $meta . '
                </li>';
            }

            $i++;
        }
        $breadcrumbs .= '</ol>';

        $breadcrumbs .= $after;
        return $breadcrumbs;
    }
}
