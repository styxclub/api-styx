<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Auth\Refresh;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\App\DTO\RefreshDTO;
use Osumi\OsumiFramework\App\Service\AuthService;

class RefreshComponent extends OComponent {
  private ?AuthService $auth_service = null;

  public string       $status         = 'error';
  public ?string      $access_token   = null;
	public string | int $expires_in     = 'null';
	public ?string      $refresh_token  = null;

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
  }

  /**
   * Endpoint para refrescar el access token usando un refresh token válido.
   *
   * @param RefreshDTO $data DTO con el campo refreshToken
   * @return void
   */
  public function run(RefreshDTO $data): void {
    global $core;

    if (!$data->isValid()) {
      $core->setHttpStatus(400);
      return;
    }

    try {
      $tokens = $this->auth_service->refreshTokens($data->refreshToken ?? '');

      $this->status        = 'ok';
      $this->access_token  = $tokens['access_token'];
      $this->expires_in    = $tokens['expires_in'];
      $this->refresh_token = $tokens['refresh_token'];
    }
    catch (\Throwable $e) {
      $code = $e->getMessage();
      $http = match($code) {
        'INVALID_REFRESH_TOKEN', 'REFRESH_TOKEN_REVOKED', 'REFRESH_TOKEN_EXPIRED' => 401,
        'TOKEN_SERVICE_NOT_CONFIGURED' => 500,
        default => 500
      };
      $core->setHttpStatus($http);
      $this->status        = 'error';
      $this->access_token  = null;
      $this->expires_in    = 'null';
      $this->refresh_token = null;
    }
  }
}
