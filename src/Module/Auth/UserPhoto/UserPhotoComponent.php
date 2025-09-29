<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Auth\UserPhoto;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\AuthService;

class UserPhotoComponent extends OComponent {
  private ?AuthService $auth_service = null;

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
  }

  /**
   * Método para obtener la foto de un usuario
   *
   * @param ORequest $req Datos pasados al método
   * @return void
   */
  public function run(ORequest $req): void {
    global $core;
    $filter = $req->getFilter('Login');

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

      $path = $this->getConfig()->getExtra('photos').$id.'.webp';
      if (!file_exists($path)) {
        $path = $this->getConfig()->getExtra('photos').'default.webp';
      }
      header('Content-Type: image/webp');
      readfile($path);
      die();
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
    }
  }
}
