<?php

Breadcrumbs::for('admin.obfuscator.logs', function ($trail) {
    $trail->parent('admin.dashboard');
    $trail->push(__('menus.backend.obfuscator.logs'), url('admin/obfuscator/logs'));
});
