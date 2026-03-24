<?php

namespace Tests\Feature;

use Tests\TestCase;

class AssetStylesTest extends TestCase
{
    public function test_multi_select_checked_options_keep_a_visible_selected_state(): void
    {
        $styles = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($styles);
        $this->assertStringContainsString('select[multiple="multiple"] option:checked,', $styles);
        $this->assertStringContainsString('select[multiple="multiple"] option[selected] {', $styles);
        $this->assertStringContainsString('background: #2b628f linear-gradient(0deg, #2b628f 0%, #2b628f 100%);', $styles);
        $this->assertStringContainsString('box-shadow: inset 0 0 0 9999px #2b628f;', $styles);
        $this->assertStringContainsString('-webkit-text-fill-color: #ffffff;', $styles);
    }

    public function test_layout_loads_css_and_javascript_via_vite(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertIsString($layout);
        $this->assertStringContainsString('href="/css/app.css"', $layout);
        $this->assertStringContainsString('src="/js/app.js"', $layout);
    }

    public function test_build_script_copies_resource_css_to_public_css(): void
    {
        $packageJson = file_get_contents(base_path('package.json'));

        $this->assertIsString($packageJson);
        $this->assertStringContainsString('"build": "cp resources/css/app.css public/css/app.css && vite build"', $packageJson);
    }
}
