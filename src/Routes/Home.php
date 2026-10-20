<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\Routes;

use Osumi\OsumiFramework\Routing\ORoute;
use Osumi\OsumiFramework\App\Middleware\LoginMiddleware;
use Osumi\OsumiFramework\App\Module\Home\GetHome\GetHomeComponent;
use Osumi\OsumiFramework\App\Module\Home\GetMessages\GetMessagesComponent;
use Osumi\OsumiFramework\App\Module\Home\GetRequests\GetRequestsComponent;

ORoute::prefix('/home', function() {
  ORoute::post('/get-home',     GetHomeComponent::class,     ['before' => [LoginMiddleware::class]]);
  ORoute::post('/get-messages', GetMessagesComponent::class, ['before' => [LoginMiddleware::class]]);
  ORoute::post('/get-requests', GetRequestsComponent::class, ['before' => [LoginMiddleware::class]]);
});
