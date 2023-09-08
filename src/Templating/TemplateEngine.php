<?php

namespace RepoScribe\Templating;

class TemplateEngine
{
    private string $path;
    private ?string $layout;

    public function __construct(string $path, ?string $layout = null)
    {
        $this->path = $path;
        $this->layout = $layout;
    }

    public function render(array $data = []): string
    {
        $content = $this->fetch($this->path, $data);

        if ($this->layout) {
            $data['content'] = $content;
            return $this->fetch($this->layout, $data);
        }

        return $content;
    }

    private function fetch(string $path, array $data): string
    {
        ob_start();
        extract($data, EXTR_OVERWRITE);
        include $path;
        return ob_get_clean();
    }
}