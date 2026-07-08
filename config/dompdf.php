<?php

return [
    'show_warnings' => false,
    'dpi' => 96,
    'default_font' => 'DejaVu Sans',
    'font_dir' => storage_path('fonts'),
    'font_cache' => storage_path('fonts'),
    'chroot' => realpath(base_path()),
    'log_output_file' => null,
    'enable_font_subsetting' => false,
    'pdf_backend' => 'CPDF',
    'default_paper_size' => 'a4',
    'default_paper_orientation' => 'portrait',
    'enable_remote' => true,
    'enable_html5_parser' => true,
    'enable_javascript' => false,
    'enable_php' => false,
    'font_height_ratio' => 1.1,
    'enable_css_float' => true,
];
