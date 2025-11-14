<?php

// config for Hubio/LaravelPdfTo
return [
    /**
     * Set the pdftotext binary path manually
     */
    'pdftotext_bin' => env('PDF_TO_TEXT_PATH'),

    /**
     * Set the pdftohtml binary path manually
     */
    'pdftohtml_bin' => env('PDF_TO_HTML_PATH'),

    /**
     * Set the pdftoppm binary path manually
     */
    'pdftoppm_bin' => env('PDF_TO_PPM_PATH'),

    /**
     * Set the pdftocairo binary path manually
     */
    'pdftocairo_bin' => env('PDF_TO_CAIRO_PATH'),

    /**
     * Set the default output directory
     */
    'output_dir' => env('PDF_TO_OUTPUT_DIR', storage_path('app/pdf-to')),
];
