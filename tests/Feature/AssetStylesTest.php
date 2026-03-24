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
        $this->assertStringNotContainsString('src="/js/app.js"', $layout);
    }

    public function test_build_script_copies_resource_css_to_public_css(): void
    {
        $packageJson = file_get_contents(base_path('package.json'));

        $this->assertIsString($packageJson);
        $this->assertStringContainsString('"build": "cp resources/css/app.css public/css/app.css && vite build"', $packageJson);
    }

    public function test_customer_credential_modals_use_checkbox_server_picker_markup(): void
    {
        $view = file_get_contents(resource_path('views/customers/view.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('class="itsdb-server-picker"', $view);
        $this->assertStringContainsString('type="checkbox" name="server_ids[]"', $view);
        $this->assertStringContainsString('@checked(in_array((string) $serverOption->id, $selectedServerIds, true))', $view);
    }

    public function test_server_credential_modals_use_checkbox_server_picker_markup(): void
    {
        $view = file_get_contents(resource_path('views/servers/view.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('class="itsdb-server-picker"', $view);
        $this->assertStringContainsString('type="checkbox" name="server_ids[]"', $view);
        $this->assertStringContainsString('@checked((string) $serverOption->id === (string) $server->id)', $view);
    }

    public function test_server_picker_styles_highlight_checked_items_without_focus(): void
    {
        $styles = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($styles);
        $this->assertStringContainsString('.itsdb-server-picker__option input:checked + span {', $styles);
        $this->assertStringContainsString('background-color: #2b628f;', $styles);
        $this->assertStringContainsString('font-weight: bold;', $styles);
    }

    public function test_server_view_contains_compose_workspace_markup(): void
    {
        $view = file_get_contents(resource_path('views/servers/_partials/config.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('data-compose-workspace', $view);
        $this->assertStringContainsString('data-compose-workspace-json', $view);
        $this->assertStringContainsString('data-product-toggle="', $view);
        $this->assertStringContainsString('data-container-toggle="', $view);
        $this->assertStringContainsString('data-compose-diff-output', $view);
        $this->assertStringContainsString('docker-compose Analyse', $view);
        $this->assertStringContainsString('1. Optional Compose analysieren oder gespeicherte Compose laden.', $view);
    }

    public function test_server_view_contains_compose_workspace_script(): void
    {
        $view = file_get_contents(resource_path('views/servers/view.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('parseComposeServices', $view);
        $this->assertStringContainsString('JSON.parse(dataElement.textContent || \'{}\')', $view);
        $this->assertStringContainsString('data-compose-workspace-initialized', $view);
        $this->assertStringContainsString("window.addEventListener('load', initServerComposeWorkspace);", $view);
        $this->assertStringContainsString('window.setTimeout(initServerComposeWorkspace, 50);', $view);
        $this->assertStringContainsString("root.addEventListener('input', function(event) {", $view);
        $this->assertStringNotContainsString("root.addEventListener('change', function(event) {", $view);
        $this->assertStringContainsString("var productItem = event.target.closest('[data-product-toggle]');", $view);
        $this->assertStringContainsString("var containerItem = event.target.closest('[data-container-toggle]');", $view);
        $this->assertStringContainsString('event.preventDefault();', $view);
        $this->assertStringContainsString('data-compose-analyze', $view);
        $this->assertStringContainsString('buildDiffText', $view);
        $this->assertStringContainsString('getCoveredProducts(baselineContainerIds, true)', $view);
    }

    public function test_server_compose_workspace_styles_exist(): void
    {
        $styles = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($styles);
        $this->assertStringContainsString('.server-compose-workspace {', $styles);
        $this->assertStringContainsString('.server-compose-hint {', $styles);
        $this->assertStringContainsString('.server-compose-picker__item.is-added .server-compose-picker__body {', $styles);
        $this->assertStringContainsString('.server-compose-chip--success {', $styles);
        $this->assertStringContainsString('.server-compose-workspace__summary-grid {', $styles);
    }

    public function test_server_controller_sorts_available_compose_entries_alphabetically(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ServerController.php'));

        $this->assertIsString($controller);
        $this->assertStringContainsString("->whereNotIn('id', \$added_compose)", $controller);
        $this->assertStringContainsString("->orderBy('title')", $controller);
    }
}
