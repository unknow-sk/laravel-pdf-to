<?php

use Illuminate\Support\Facades\Artisan;

it('executes the pdf-to-html command successfully', function () {
    Artisan::call('pdf-to-html', [
        'pdf' => __DIR__.'/dummy.pdf',
        '--output-dir' => __DIR__.'/output',
        '--name' => 'output',
    ]);

    expect(Artisan::output())->toContain('Conversion complete!');
});
