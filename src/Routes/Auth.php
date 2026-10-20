<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\Routes;

use Osumi\OsumiFramework\Routing\ORoute;
use Osumi\OsumiFramework\App\Middleware\LoginMiddleware;
use Osumi\OsumiFramework\App\Module\Auth\GetUserInfo\GetUserInfoComponent;
use Osumi\OsumiFramework\App\Module\Auth\Login\LoginComponent;
use Osumi\OsumiFramework\App\Module\Auth\Logout\LogoutComponent;
use Osumi\OsumiFramework\App\Module\Auth\Refresh\RefreshComponent;
use Osumi\OsumiFramework\App\Module\Auth\UserPhoto\UserPhotoComponent;

ORoute::prefix('/auth', function() {
  ORoute::post('/get-user-info',  GetUserInfoComponent::class, ['before' => [LoginMiddleware::class]]);
  ORoute::post('/login',          LoginComponent::class);
  ORoute::post('/logout',         LogoutComponent::class);
  ORoute::post('/refresh',        RefreshComponent::class);
  ORoute::post('/user-photo/:id', UserPhotoComponent::class, ['before' => [LoginMiddleware::class]]);
});
