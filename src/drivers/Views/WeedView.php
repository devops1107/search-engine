<?php

namespace spark\drivers\Views;

use spark\drivers\Views\Weed;

/**
 * Renders templates using the Weed template system
 *
 */
class WeedView extends \Slim\View
{
    /**
     * @var Weed The weed instance for rendering templates.
     */
    private $parserInstance = null;

    protected $options = [
        'useCurrentNameSpace' => true,
        'autoEscape' => false
    ];

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function addFolder($namespace, $templatesPath)
    {
        return $this->getInstance()->addFolder($namespace, $templatesPath);
    }

    /**
     * Render Template
     *
     * This method will output the rendered template content
     *
     * @param string $template The path to the template, relative to the templates directory.
     * @param array $data
     * @return string
     */
    public function render($template, $data = null)
    {
        $t = $this->getInstance();
        $data = array_merge($this->all(), (array) $data);

        $route = get_current_route_name();

        $t->replace($data);

        return $t->render($template);
    }

    public function renderAjax($template, $data = [], $status = null)
    {
        $t = $this->getInstance();
        $data = array_merge($this->all(), $data);
        $t->replace($data);

        $app = app();

        $header = $content = $footer = '';


        if (empty($data['hide_header'])) {
            $header = $this->render($data['header_template'], $data);
        }

        $content = $this->render($template, $data);

        if (empty($data['hide_footer'])) {
            $footer = $this->render($data['footer_template'], $data);
        }

        $response = [
            'title'      => '',
            'route'      => [],
            'attr'       => [],
            'body_class' => '',
            'sections'   => [],
            'prepend'    => [],
            'append'     => [],
        ];

        if (is_ajax()) {
            $title = $t['title'];

            if ($t['title_append_site_name']) {
                $title .= " {$t['title_separator']} {$t['meta.name']}";
            }

            $class = '';

            if ($t['hide_header']) {
                $class .= ' no-header';
            } else {
                $class .= ' has-header';
            }

            if ($t['hide_footer']) {
                $class .= ' no-footer';
            } else {
                $class .= ' has-footer';
            }

            $response['title'] = $title;
            $response['url'] = $t->get('url', get_current_route_uri(true, ['ajax']));
            $response['form_change_url'] = $t->get('ajax_form_change_url', false);
            $response['body_class'] = e_attr($t['body_class']) . $class;
            $response['html_class'] = e_attr($t['html_class']);
            $response['route']['name'] = get_current_route_name();
            $response['route']['url'] = get_current_route_uri();
            $response['sections']['header']  = $header;
            $response['sections']['content'] = $content;
            $response['sections']['footer']  = $footer;

            if (is_array($t['ajax_view_prepend'])) {
                $response['prepend'] = $t['ajax_view_prepend'];
            }

            if (is_array($t['ajax_view_append'])) {
                $response['append'] = $t['ajax_view_append'];
            }

            if (is_array($t['ajax_view_sections'])) {
                foreach ($t['ajax_view_sections'] as $key => $value) {
                    $response['sections'][$key] = $value;
                }
            }

            if (is_array($t['ajax_view_attrs'])) {
                foreach ($t['ajax_view_attrs'] as $selector => $attrs) {
                    if (!is_array($attrs)) {
                        continue;
                    }

                    foreach ($attrs as $key => $value) {
                        $response['attr'][$selector][$key] = e_attr($value);
                    }
                }
            }

            return json($response);
        }

        $class = $t['body_class'];

        if ($t['hide_header']) {
            $class .= ' no-header';
        } else {
            $class .= ' has-header';
        }

        if ($t['hide_footer']) {
            $class .= ' no-footer';
        } else {
            $class .= ' has-footer';
        }

        $data['body_class'] = $class;
        $data['site_header'] = $header;
        $data['site_content'] = $content;
        $data['site_footer'] = $footer;

        return view('container.php', $data);
    }

    /**
     * Creates new instance if it doesn't already exist, and returns it.
     *
     * @return Weed
     */
    public function getInstance()
    {
        if (!$this->parserInstance) {
            $options = $this->options;
            $this->parserInstance = new Weed($this->getTemplatesDirectory(), $options);
        }

        return $this->parserInstance;
    }
}
