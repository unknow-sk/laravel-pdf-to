<?php

use Illuminate\Support\Facades\Artisan;

it('executes the pdf-to-image command successfully', function () {
    Artisan::call('pdf-to-image', [
        'pdf' => __DIR__.'/dummy.pdf',
        '--output-dir' => __DIR__.'/output',
        '--name' => 'output',
    ]);

    expect(Artisan::output())->toContain('Conversion complete!');

    Artisan::call('pdf-to-image', [
        'pdf' => __DIR__.'/dummy.pdf',
        '--output-dir' => __DIR__.'/output',
        '--name' => 'output',
        '--jpg' => true,
    ]);

    expect(Artisan::output())->toContain('Conversion complete!');
});
