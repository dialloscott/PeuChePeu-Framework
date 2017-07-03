<?php

return [
    \App\Blog\PostUpload::class          => \DI\object()->constructor(\DI\string('{upload_path}/posts')),
];
