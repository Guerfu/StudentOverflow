<?php
// lib/view.php

function render(string $template, array $data = [], string $layout = 'layouts/app.html.php'): void
{
    $templatesRoot = __DIR__ . '/../templates/';

    $tplPath = $templatesRoot . ltrim($template, '/');
    $layPath = $templatesRoot . ltrim($layout, '/');

    if (!is_file($tplPath)) {
        http_response_code(500);
        echo "Template not found: " . htmlspecialchars($template);
        return;
    }
    if (!is_file($layPath)) {
        http_response_code(500);
        echo "Layout not found: " . htmlspecialchars($layout);
        return;
    }

    // Expose $data as local vars to template
    extract($data, EXTR_SKIP);

    // Render the template first
    ob_start();
    require $tplPath; // The template should echo page content
    $content_for_layout = ob_get_clean();

    // Now render the layout, which will output the full page
    require $layPath;
}

/**
 * Simple redirect helper with exit.
 */
function redirect_to(string $url): void
{
    header('Location: ' . $url);
    exit;
}
