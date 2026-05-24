<?php

namespace PHPlexus\Tests\Core\Templating;

use PHPlexus\Templating\TemplateEngine;
use PHPUnit\Framework\TestCase;

class TemplateEngineTest extends TestCase {
    private string $tempTemplate;

    protected function setUp(): void {
        $this->tempTemplate = tempnam(sys_get_temp_dir(), 'tmpl');
    }

    protected function tearDown(): void {
        @unlink($this->tempTemplate);
    }

    public function testSandboxIsolation() {
        require_once __DIR__ . '/../../../src/Templating/helpers.php';

        file_put_contents($this->tempTemplate, 'Hello <?= e($name) ?>. This is <?= get_class($this) ?>');

        $engine = new TemplateEngine($this->tempTemplate);
        $output = $engine->render(['name' => '<script>Alice</script>']);

        $this->assertStringContainsString('Hello &lt;script&gt;Alice&lt;/script&gt;', $output);
        $this->assertStringContainsString('This is PHPlexus\Templating\TemplateSandbox', $output);
    }
}
