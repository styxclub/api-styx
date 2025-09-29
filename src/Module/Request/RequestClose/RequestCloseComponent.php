<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Request\RequestClose;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\RequestService;

class RequestCloseComponent extends OComponent {
  private ?RequestService $request_service = null;
  public string           $status          = 'error';

  public function __construct() {
    parent::__construct();
    $this->request_service = inject(RequestService::class);
  }

  /**
   * Método para cerrar un evento y crear las votaciones
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

    $id_request   = $req->getParamInt('id');
    $completed_at = $req->getParamString('completedAt');

    if (is_null($id_request) || is_null($completed_at)) {
      $core->setHttpStatus(401);
      return;
    }

    try {
      $this->request_service->closeRequest($id_request, $completed_at);
      $this->status = 'ok';
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
      $this->status = 'error';
    }
  }
}
