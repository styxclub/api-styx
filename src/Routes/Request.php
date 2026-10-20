<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\Routes;

use Osumi\OsumiFramework\Routing\ORoute;
use Osumi\OsumiFramework\App\Middleware\LoginMiddleware;
use Osumi\OsumiFramework\App\Module\Request\RequestClose\RequestCloseComponent;
use Osumi\OsumiFramework\App\Module\Request\RequestVotes\RequestVotesComponent;
use Osumi\OsumiFramework\App\Module\Request\SaveRequestVotes\SaveRequestVotesComponent;

ORoute::prefix('/request', function() {
  ORoute::post('/request-close',      RequestCloseComponent::class,     ['before' => [LoginMiddleware::class]]);
  ORoute::post('/request-votes',      RequestVotesComponent::class,     ['before' => [LoginMiddleware::class]]);
  ORoute::post('/save-request-votes', SaveRequestVotesComponent::class, ['before' => [LoginMiddleware::class]]);
});
