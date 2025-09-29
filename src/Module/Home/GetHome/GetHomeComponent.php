<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Home\GetHome;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\App\Service\AuthService;
use Osumi\OsumiFramework\App\Service\ChatService;
use Osumi\OsumiFramework\App\Service\HomeService;
use Osumi\OsumiFramework\App\Component\Model\ChatList\ChatListComponent;

class GetHomeComponent extends OComponent {
  private ?AuthService $auth_service = null;
  private ?ChatService $chat_service = null;
  private ?HomeService $home_service = null;

  public string             $status    = 'ok';
  public ?ChatListComponent $chats     = null;
  public string             $board     = 'null';
  public string | int       $page      = 'null';
  public string | int       $limit     = 'null';
  public string | int       $next_page = 'null';

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
    $this->chat_service = inject(ChatService::class);
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
    $filter = $req->getFilter('Login');

    if (is_null($filter) || !array_key_exists('sub', $filter)) {
      $core->setHttpStatus(401);
      return;
    }

    $id_user     = $filter['sub'];
    $this->limit = $req->getParamInt('limit') ?? 20;
    $this->page  = $req->getParamInt('page') ?? 0;
    $this->chats = new ChatListComponent();

    try {
      $this->chats->list = $this->chat_service->getLatest($id_user);
      $items = $this->home_service->getBoard($id_user, $this->limit, $this->page);
      $this->board = json_encode($items);
      $this->next_page = count($items) === $this->limit ? $this->page + 1 : 'null';
    }
    catch (\Throwable $e) {
      $core->setHttpStatus(500);
      $this->status      = 'error';
      $this->chats->list = [];
      $this->board       = 'null';
      $this->page        = 'null';
      $this->limit       = 'null';
      $this->next_page   = 'null';
    }
  }
}
