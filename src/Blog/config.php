<?php

return [
    \App\Blog\BlogModule::class => \DI\object()
        ->constructorParameter('middleware', \DI\get('admin.middleware')),
    \App\Blog\PostUpload::class => \DI\object()->constructor(\DI\string('{upload_path}/posts')),
];
