<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\ORM\ODB;
use Osumi\OsumiFramework\App\Model\Request;
use Osumi\OsumiFramework\App\Model\UserRequest;
use Osumi\OsumiFramework\App\Model\Vote;
use Osumi\OsumiFramework\App\Model\User;

class RequestService extends OService {
  /**
   * Método para cerrar un evento y crear las votaciones
   *
   * @param int $id_request Id del evento a cerrar
   * @param string $completed_at Fecha de celebración del evento
   * @return void
   */
  public function closeRequest(int $id_request, string $completed_at): void {
    $request = Request::findOne(['id' => $id_request]);
    if (is_null($request)) {
      throw new \RuntimeException('REQUEST_NOT_FOUND');
    }

    $request->status = 2;
    $request->completed_at = $completed_at;
    $request->save();

    $user_list = [$request->id_user];

    $user_request_list = UserRequest::where(['id_request' => $request->id]);
    foreach ($user_request_list as $user_request) {
      $user_list[] = $user_request->id_user;
    }

    $this->createVotes($user_list, $request->id);
  }

  /**
   * Crea registros en vote para que todos los usuarios se voten entre sí (A→B y B→A, sin auto-voto).
   *
   * @param int[] $user_list Lista de IDs de usuario
   * @param int   $request_id Id de la solicitud
   * @param int   $batch_size Nº máximo de filas por INSERT (para no pasar el límite del servidor)
   * @return int  Nº de filas insertadas
   */
  private function createVotes(array $user_list, int $request_id, int $batch_size = 5000): int {
    // 1) Normaliza y quita duplicados en la lista de usuarios
    $ids = array_values(array_unique(array_map('intval', $user_list)));
    $ids = array_filter($ids, fn($v) => $v > 0);
    $n = count($ids);
    if ($n < 2) { return 0; }

    $now = date('Y-m-d H:i:s');
    $rows = 0;
    $db = new ODB();

    // 2) Prepara lotes de VALUES
    $values = [];
    $params = [];

    // Insertaremos columnas: id_user_from, id_user_to, id_request, created_at, updated_at
    foreach ($ids as $from) {
      foreach ($ids as $to) {
        if ($from === $to) { continue; } // sin auto-voto
        $values[] = "(?, ?, ?, ?, ?)";
        array_push($params, $from, $to, $request_id, $now, $now);

        // lanzamos el lote si alcanzamos el tamaño
        if (count($values) >= $batch_size) {
          $rows += $this->createVoteInsert($db, $values, $params);
          $values = [];
          $params = [];
        }
      }
    }

    // 3) Último lote
    if (!empty($values)) {
      $rows += $this->createVoteInsert($db, $values, $params);
    }

    return $rows;
  }

  /**
   * Ejecuta un INSERT multi-VALUES sobre la tabla vote.
   *
   * @param ODB $db Objeto con conexión a la BD
   * @param array $values Lista de valores a insertar
   * @param array $params Lista de parametros de los valores
   * @return int Número de resultados
   */
  private function createVoteInsert(ODB $db, array $values, array $params): int {
    $sql = "INSERT INTO vote (id_user_from, id_user_to, id_request, created_at, updated_at)
            VALUES " . implode(", ", $values);
    $db->query($sql, $params);
    return (int) ($db->count() ?? 0);
  }

  /**
   * Método para obtener las votaciones que puede hacer un usuario
   *
   * @param int $id_request Id del evento
   * @param int $id_user Id del usuario
   * @return array Listado de votos
   */
  public function getRequestVotes(int $id_request, int $id_user): array {
    return Vote::where(['id_request' => $id_request, 'id_user_from' => $id_user]);
  }

  /**
   * Método para actualizar votaciones de un evento
   *
   * @param int $id_request Id del evento
   * @param int $id_user Id del usuario que vota
   * @param array $votes Lista de votaciones hechas por el usuario
   * @return array Datos actualizados del usuario y si se ha finalizado el evento
   */
  public function updateVotes(int $id_request, int $id_user, array $votes): array {
    // Actualizo votos emitidos por el usuario
    foreach ($votes as $vote) {
      $v = Vote::findOne(['id' => $vote['id']]);
      $v->vote = $vote['vote'];
      $v->save();
    }

    // Busco todos los votos del evento
    $all_votes = Vote::where(['id_request' => $id_request]);
    $all_voted = true;
    $positive_votes = true;
    $all_users = [];
    foreach ($all_votes as $vote) {
      // Si hay alguno que todavía esté a 0 es que no ha votado
      if ($vote->vote === 0) {
        $all_voted = false;
      }
      // Si todos los votos tienen 3 estrellas o más es que el evento ha sido positivo
      if ($vote->vote < 3) {
        $positive_votes = false;
      }
      $all_users[] = $vote->getUserFrom();
    }

    // Si todos los votos han sido emitidos cerramos el evento
    if ($all_voted) {
      $request = Request::findOne(['id' => $id_request]);
      $request->status = 3;
      $request->save();

      // Actualizamos votos y reputación de los usuarios
      foreach ($all_users as $vote_user) {
        $user_votes = Vote::where(['id_user_to' => $vote_user->id]);
        $num_votes = 0;
        $reputation = 0;
        foreach ($user_votes as $user_vote) {
          if ($user_vote->vote > 0) {
            $num_votes++;
            $reputation += $user_vote->vote;
          }
        }
        $reputation = $reputation / $num_votes;

        // Si todos los votos han sido positivos añadimos recompensa
        if ($positive_votes) {
          $vote_user->credits  += $request->reward;
        }
        $vote_user->votes      = $num_votes;
        $vote_user->reputation = $reputation;
        $vote_user->save();
      }
    }

    // Devolvemos usuario actualizado y si se ha finalizado el evento
    return ['user' => User::findOne(['id' => $id_user]), 'all_voted' => $all_voted];
  }
}
