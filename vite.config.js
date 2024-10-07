import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { globSync } from "glob";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/blog.js',
                'resources/css/filament/dashboard/theme.css',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/company/theme.css',
                'resources/css/filament/user/theme.css',
                ...globSync("resources/css/invoice/themes/*.css")
            ],
            refresh: true,
        }),
    ],
});
