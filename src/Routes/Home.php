<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\Routes;

use Osumi\OsumiFramework\Routing\ORoute;
use Osumi\OsumiFramework\App\Module\Home\GetHome\GetHomeComponent;
use Osumi\OsumiFramework\App\Module\Home\GetMessages\GetMessagesComponent;
use Osumi\OsumiFramework\App\Module\Home\GetRequests\GetRequestsComponent;
use Osumi\OsumiFramework\App\Filter\LoginFilter;

ORoute::prefix('/home', function() {
  ORoute::post('/get-home',     GetHomeComponent::class,     [LoginFilter::class]);
  ORoute::post('/get-messages', GetMessagesComponent::class, [LoginFilter::class]);
  ORoute::post('/get-requests', GetRequestsComponent::class, [LoginFilter::class]);
});
