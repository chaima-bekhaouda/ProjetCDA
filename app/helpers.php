<?php

if (! function_exists('view')) {
    function view(string $path, array $data = []): string
    {
        $file = __DIR__ . '/Views/' . $path . '.php';

        if (!file_exists($file)) {
            return 'View not found';
        }

        extract($data);

        ob_start();
        require $file;
        return ob_get_clean();
    }
}
