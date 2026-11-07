<?php

namespace Tests\Feature;

use Tests\TestCase;

class CodeBlockRenderingTest extends TestCase
{
    public function test_docs_php_code_block_renders_prism_panel(): void
    {
        $this->get('/docs/ui/form-layouts')
            ->assertOk()
            ->assertSee('class="st-code"', false)
            ->assertSee('st-code__chrome', false)
            ->assertSee('language-php', false)
            ->assertSee('example.php', false);
    }

    public function test_ui_playground_guide_wraps_json_and_shell_samples(): void
    {
        $html = $this->get('/docs/ui-playground')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('language-json', $html);
        $this->assertStringContainsString('language-bash', $html);
        $this->assertStringContainsString('st-code__chrome', $html);
        $this->assertGreaterThan(1, substr_count($html, 'class="st-code"'));
    }

    public function test_playground_editor_exposes_json_prism_markup(): void
    {
        $this->get('/playground/text-input')
            ->assertOk()
            ->assertSee('data-playground-config-highlight', false)
            ->assertSee('language-json', false)
            ->assertSee('st-code', false);
    }
}
