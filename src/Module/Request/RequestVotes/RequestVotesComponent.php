<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Request\RequestVotes;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\RequestService;
use Osumi\OsumiFramework\App\Component\Model\VoteList\VoteListComponent;

class RequestVotesComponent extends OComponent {
  private ?RequestService   $request_service = null;
  public string             $status          = 'error';
  public ?VoteListComponent $votes           = null;

  public function __construct() {
    parent::__construct();
    $this->request_service = inject(RequestService::class);
    $this->votes = new VoteListComponent();
  }

  /**
   * Método para obtener las votaciones de un evento
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

    $id_request = $req->getParamInt('id');
    $id_user    = $filter['sub'];

    if (is_null($id_request)) {
      $core->setHttpStatus(401);
      return;
    }

    try {
      $this->votes->list = $this->request_service->getRequestVotes($id_request, $id_user);
      $this->status = 'ok';
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
      $this->status = 'error';
    }
  }
}
