<?php

use Illuminate\Support\Facades\Artisan;

it('executes the pdf-to-text command successfully', function () {
    Artisan::call('pdf-to-text', [
        'pdf' => __DIR__.'/dummy.pdf',
        '--output-dir' => __DIR__.'/output',
        '--name' => 'output',
    ]);

    expect(Artisan::output())->toContain('Conversion complete!');
});
