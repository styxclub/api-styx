<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Home\GetMessages;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\AuthService;
use Osumi\OsumiFramework\App\Service\HomeService;

class GetMessagesComponent extends OComponent {
  private ?AuthService $auth_service = null;
  private ?HomeService $home_service = null;

  public string             $status    = 'ok';
  public string             $messages  = 'null';
  public string | int       $page      = 'null';
  public string | int       $limit     = 'null';
  public string | int       $next_page = 'null';

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
    $this->home_service = inject(HomeService::class);
  }

  /**
   * Método para obtener los datos de la home de un usuario
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

    $id_user     = $filter['sub'];
    $this->limit = $req->getParamInt('limit') ?? 20;
    $this->page  = $req->getParamInt('page') ?? 0;

    try {
      $messages = $this->home_service->getMessages($this->limit, $this->page);
      $this->messages  = json_encode($messages);
      $this->next_page = count($messages) === $this->limit ? $this->page + 1 : 'null';
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
      $this->status      = 'error';
      $this->messages    = 'null';
      $this->page        = 'null';
      $this->limit       = 'null';
      $this->next_page   = 'null';
    }
  }
}
