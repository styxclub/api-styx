<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\App\Model\Chat;

class ChatService extends OService {
  /**
   * Método para obtener los 5 últimos mensajes de un usuario para mostrar en la home
   *
   * @param int $id_user_to Id del usuario al que han mandado los mensajes
   * @return array Lista de mensajes
   */
  public function getLatest(int $id_user_to): array {
    return Chat::where(
      ['id_user_to' => $id_user_to],
      [
        'order_by' => 'created_at#desc',
        'limit' => 0,
        'offset' => 5
      ]
    );
  }
}
