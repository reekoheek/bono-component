<?php

namespace ROH\BonoComponent;

use Bono\Helper\URL;
use ROH\Util\Inflector;

class PlainTable {
    protected $app;
    protected $clazz;
    protected $config;
    protected $schema;

    public function __construct($clazz, $config = NULL) {
        $this->app = \Bono\App::getInstance();
        $this->clazz = $clazz;

        $collection = \Norm\Norm::factory($clazz);
        $this->schema = $collection->schema();

        $globalConfig = $this->app->config('component.table');

        if (!isset($config['actions'])) {
            if (isset($globalConfig['mapping'][$clazz]['actions'])) {
                $config['actions'] = $globalConfig['mapping'][$clazz]['actions'];
            } elseif (isset($globalConfig['default']['actions'])) {
                $config['actions'] = $globalConfig['default']['actions'];
            } else {
                $config['actions'] = array('read' => NULL, 'update' => NULL, 'delete' => NULL);
            }
        }


        if (!isset($config['columns'])) {
            if (isset($globalConfig['mapping'][$clazz]['columns'])) {
                $config['columns'] = $globalConfig['mapping'][$clazz]['columns'];
            } elseif (isset($globalConfig['default']['columns'])) {
                $config['columns'] = $globalConfig['default']['columns'];
            }
        }

        if (empty($this->schema) && empty($config['columns'])) {
            throw new \Exception('Plain table needs collection schema or "component.table" configuration!');
        }

        $this->config = $config;

        $this->view = new \Slim\View();
        $this->view->setTemplatesDirectory(realpath(dirname(__FILE__).'/../../../templates'));
        $this->view->set('self', $this);
    }

    public function renderColumns($entry = NULL) {
        $html = '';

        $iterator = isset($this->config['columns']) ? $this->config['columns'] : $this->schema;

        if (is_null($entry)) {
            foreach ($iterator as $key => $valueGetter) {
                if ($key[0] !== '$') {
                    $html .= '<th>'.(isset($this->schema[$key]) ? $this->schema[$key]['label'] : Inflector::humanize($key)).'</th>';
                }
            }
        } else {
            $first = true;
            foreach ($iterator as $key => $valueGetter) {
                if ($key[0] !== '$') {
                    $html .= '<td>';
                    if ($first) {
                        $url = URL::site($this->app->controller->getBaseUri().'/'.$entry['$id']);
                        $html .= '<a href="'.$url.'">';
                    }
                    if (isset($valueGetter) && $iterator !== $this->schema) {
                        if ($valueGetter) {
                            $html .= $valueGetter(@$entry[$key], $entry);
                        }
                    } else {
                        $value = @$entry[$key];
                        if (isset($this->schema[$key])) {
                            $value = $this->schema[$key]->cell($value, $entry);
                        }
                        $html .= $value;
                    }
                    if ($first) {
                        $html .= '</a>';
                        $first = false;
                    }
                    $html .= '</td>';
                }
            }
        }
        return $html;
    }

    public function renderAction($entry = NULL) {
        if (!empty($this->config['actions'])) {

            if (is_null($entry)) {
                return '<th style="width:1px">&nbsp;</th>';
            } else {
                $html = '<td>';
                foreach ($this->config['actions'] as $key => $value) {
                    $html .= $this->renderActionButton($key, $value, $entry);
                }
                $html .= '</td>';
                return $html;
            }
        }
    }

    public function renderActionButton($name, $value, $context) {
        if (empty($value)) {
            $url = URL::site($this->app->controller->getBaseUri().'/'.$context['$id'].'/'.$name);
            return '<a href="'.$url.'">'.$this->humanize($name)."</a>\n";
        } else {
            return $value($name, $value, $context);
        }
    }

    public function show($entries) {
        $this->view->set('entries', $entries);

        return $this->view->fetch('table.php');
    }

    public function isEmpty($entries) {
        if (empty($entries)) {
            return true;
        }

        if ($entries instanceof \Norm\Cursor) {
            return ($entries->count() <= 0);
        }
    }

    protected function humanize($name) {
        return Inflector::classify($name);
    }
}