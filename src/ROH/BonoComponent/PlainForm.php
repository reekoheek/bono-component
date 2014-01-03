<?php

namespace ROH\BonoComponent;

class PlainForm {
    protected $app;
    protected $clazz;
    protected $schema;
    protected $config;
    protected $globalConfig;

    public function __construct($clazz, $config = NULL) {
        $this->app = \Bono\App::getInstance();
        $this->clazz = $clazz;

        $collection = \Norm\Norm::factory($clazz);
        $this->schema = $collection->schema();

        $this->globalConfig = $this->app->config('component.form');

        $this->config = (isset($this->globalConfig['mapping'][$this->clazz])) ? $this->globalConfig['mapping'][$this->clazz] : NULL;
    }

    public function renderReadonlyFields($entry) {
        foreach ($this->schema as $field) {
            $field['readonly'] = true;
        }

        return $this->renderFields($entry);
    }

    public function renderFields($entry) {
        $html = '';
        $iterator = $this->config ?: $this->schema;

        foreach ($iterator as $key => $v) {
            $field = $this->schema[$key];
            $html .= '<div class="row">';
            $html .= '<div class="span-12">';
            $html .= $field->label();
            $html .= $field->input(@$entry[$field['name']], @$entry);
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }
}