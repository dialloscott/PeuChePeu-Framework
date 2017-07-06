<?php

return [
    \App\Shop\ProductUpload::class => \DI\object()->constructor(\DI\string('{upload_path}/produits')),
];
