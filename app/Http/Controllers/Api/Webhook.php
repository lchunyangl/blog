<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Webhook extends Controller
{
    public function deploy(Request $request)
    {
        $path = config('services.webhooks.path');
        $branch = config('services.webhooks.branch');
        $key = config('services.webhooks.key');
        if (hash_equals('sha1=' . hash_hmac('sha1', file_get_contents("php://input"), $key),
            $request->server('HTTP_X_HUB_SIGNATURE', ''))) {
            $env = config('app.env');
            $no_dev = '';
            if ($env == 'production')
                $no_dev = '--no-dev';
            echo shell_exec("cd {$path} && git checkout {$branch} && php artisan migrate && php /usr/local/bin/composer install {$no_dev} 2>&1");
        }
        exit("done " . date('Y-m-d H:i:s', time()));
    }
}
