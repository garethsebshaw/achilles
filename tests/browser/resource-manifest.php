<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = [];

foreach (glob(app_path('Nova/*.php')) as $file) {
    $class = 'App\\Nova\\' . basename($file, '.php');

    if (! class_exists($class) || $class === App\Nova\Resource::class || ! is_subclass_of($class, Laravel\Nova\Resource::class)) {
        continue;
    }

    try {
        $model = $class::newModel();
    } catch (Throwable) {
        continue;
    }

    $entries[] = [
        'label' => $class::label(),
        'slug' => $class::uriKey(),
        'sampleId' => $model->newQuery()->value($model->getKeyName()),
    ];
}

usort($entries, static fn (array $left, array $right): int => strcmp($left['slug'], $right['slug']));

echo json_encode($entries, JSON_THROW_ON_ERROR);
