<?php
if (!function_exists('render')) {
    /**
     * Render a view relative to BASE_VIEW_PATH with optional data extraction.
     *
     * @param string $view Relative path from the base view directory.
     * @param array  $data Variables to extract for the view.
     */
    function render(string $view, array $data = []): void
    {
        $viewPath = rtrim(BASE_VIEW_PATH, '/\\') . '/' . ltrim($view, '/\\');
        if (!file_exists($viewPath)) {
            throw new RuntimeException("View not found: {$viewPath}");
        }
        extract($data, EXTR_SKIP);
        require $viewPath;
    }
}
?>