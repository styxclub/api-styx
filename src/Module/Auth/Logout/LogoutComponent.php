<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Auth\Logout;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\App\DTO\LogoutDTO;
use Osumi\OsumiFramework\App\Service\AuthService;

class LogoutComponent extends OComponent {
  private ?AuthService $auth_service = null;

  public string $status = 'error';

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
  }

  /**
   * Endpoint para cerrar sesión: revoca el refresh token (o todos los del usuario).
   *
   * @param LogoutDTO $data DTO con refreshToken y opcional allDevices
   * @return void
   */
  public function run(LogoutDTO $data): void {
    global $core;

    if (!$data->isValid()) {
      $core->setHttpStatus(400);
      return;
    }

    try {
      $all_devices = (bool) ($data->allDevices ?? false);
      $this->auth_service->logout($data->refreshToken ?? '', $all_devices);

      $this->status = 'ok';
    }
    catch (\Throwable $e) {
      $code = $e->getMessage();
      $http = match ($code) {
        'INVALID_REFRESH_TOKEN' => 400,
        default => 500
      };
      $core->setHttpStatus($http);
    }
  }
}
