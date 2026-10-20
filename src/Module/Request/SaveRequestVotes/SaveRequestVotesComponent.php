<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Request\SaveRequestVotes;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\App\DTO\VotesDTO;
use Osumi\OsumiFramework\App\Service\RequestService;
use Osumi\OsumiFramework\App\Component\Model\User\UserComponent;

class SaveRequestVotesComponent extends OComponent {
  private ?RequestService   $request_service = null;
  public string             $status          = 'error';
  public string             $all_voted       = 'false';
  public ?UserComponent     $user            = null;

  public function __construct() {
    parent::__construct();
    $this->request_service = inject(RequestService::class);
    $this->user            = new UserComponent();
  }

  /**
   * Método para guardar las votaciones de un evento
   *
   * @param VotesDTO $data Datos pasados al método
   * @return void
   */
  public function run(VotesDTO $data): void {
    global $core;

    if (!$data->isValid()) {
      $core->setHttpStatus(401);
      return;
    }

    $id_request = $data->idRequest;
    $id_user    = $data->idUser;
    $votes      = $data->votes;

    try {
      $result = $this->request_service->updateVotes($id_request, $id_user, $votes);
      $this->user->user = $result['user'];
      $this->user->full = false;
      $this->status     = 'ok';
      $this->all_voted  = $result['all_voted'] ? 'true' : 'false';
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
      $this->status     = 'error';
      $this->all_voted  = 'false';
      $this->user->user = null;
    }
  }
}
