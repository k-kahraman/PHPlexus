<?php

namespace PHPlexus\Templating;

class TemplateSandbox {
    public function __construct(string $__path, array $__data) {
        extract($__data, EXTR_OVERWRITE);
        include $__path;
    }
}

class TemplateEngine {
    private string $path;
    private ?string $layout;

    public function __construct(string $path, ?string $layout = null) {
        $this->path = $path;
        $this->layout = $layout;
    }

    public function render(array $data = []): string {
        $content = $this->fetch($this->path, $data);

        if ($this->layout) {
            $data['content'] = $content;
            return $this->fetch($this->layout, $data);
        }

        return $content;
    }

    private function fetch(string $path, array $data): string {
        ob_start();
        new TemplateSandbox($path, $data);
        return ob_get_clean();
    }
}