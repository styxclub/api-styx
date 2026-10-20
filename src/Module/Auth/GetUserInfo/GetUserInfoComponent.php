<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Auth\GetUserInfo;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\AuthService;
use Osumi\OsumiFramework\App\Component\Model\User\UserComponent;

class GetUserInfoComponent extends OComponent {
  private ?AuthService $auth_service = null;

  public string         $status = 'error';
  public ?UserComponent $user   = null;

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
    $this->user         = new UserComponent();
  }

  /**
   * Método para obtener la información de un usuario
   *
   * @param ORequest $req Datos pasados al método
   * @return void
   */
  public function run(ORequest $req): void {
    global $core;
    $filter = $req->getMiddleware('Login');

    if (is_null($filter) || !array_key_exists('sub', $filter)) {
      $core->setHttpStatus(401);
      return;
    }

    $id = $req->getParamInt('id');
    if (is_null($id)) {
      $core->setHttpStatus(404);
      return;
		}

    try {
      $user = $this->auth_service->getUser($id);

      if (is_null($user)) {
        $core->setHttpStatus(404);
        return;
  		}

      $this->status     = 'ok';
      $this->user->user = $user;
    }
    catch (\Throwable $e) {
      $this->status     = 'error';
      $this->user->user = null;
      $core->setHttpStatus(500);
    }
  }
}
