<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\ORM\ODB;

class HomeService extends OService {
  /**
   * Función para construir el resultado a devolver
   *
   * @param ODB Objeto con los datos obtenidos
   * @return array Array con los datos formateados
   */
  private function buildItems(ODB $db): array {
    $items = [];
    while ($row = $db->next()) {
      $items[] = [
        'id'        => (int)$row['id'],
        'createdAt' => $row['createdAt'],
        'kind'      => $row['kind'],  // 'message' | 'request'
        'title'     => $row['title'],
        'preview'   => $row['preview'],
        'payload'   => json_decode($row['payload'], true) // ya tipado (casts en SQL)
      ];
    }

    return $items;
  }

  /**
   * Función para obtener la lista conjunta de noticias y eventos
   *
   * @param int $current_user_id Usuario actual (para is_enrolled)
   * @param int $limit   Nº de ítems por página
   * @param int $page    Página (0-based)
   * @return array       Lista de ítems del feed
   */
  public function getBoard(int $current_user_id, int $limit = 20, int $page = 0): array {
    $limit  = max(1, min($limit, 100));
    $offset = max(0, $page) * $limit;

    $sql = "SELECT *
            FROM (
              /* --- Mensajes --- */
              SELECT
                bm.id,
                bm.created_at      AS `createdAt`,
                'message'          AS kind,
                bm.title           AS title,
                LEFT(bm.body, 200) AS preview,
                JSON_OBJECT(
                  'id',        CAST(bm.id      AS UNSIGNED),
                  'idUser',    CAST(bm.id_user AS UNSIGNED),
                  'username',  u.username,
                  'title',     bm.title,
                  'body',      bm.body,
                  'createdAt', bm.created_at
                ) AS payload
              FROM board_message bm
              JOIN `user` u ON u.id = bm.id_user

              UNION ALL

              /* --- Requests / Eventos --- */
              SELECT
                r.id,
                r.created_at       AS `createdAt`,
                'request'          AS kind,
                r.title            AS title,
                LEFT(r.body, 200)  AS preview,
                JSON_OBJECT(
                  'id',          CAST(r.id        AS UNSIGNED),
                  'idUser',      CAST(r.id_user   AS UNSIGNED),
                  'username',    u.username,
                  'reputation',  CAST(u.reputation AS DECIMAL(10,4)),
                  'votes',       CAST(u.votes      AS UNSIGNED),

                  'title',       r.title,
                  'body',        r.body,
                  'reward',      CAST(r.reward    AS SIGNED),
                  'status',      CAST(r.status    AS UNSIGNED),
                  'completedAt', r.completed_at,
                  'createdAt',   r.created_at,

                  /* métricas de participantes */
                  'participantsTotal',    CAST((SELECT COUNT(*) FROM user_request ur WHERE ur.id_request = r.id) AS UNSIGNED),
                  'participantsAccepted', CAST((SELECT COUNT(*) FROM user_request ur WHERE ur.id_request = r.id AND ur.status = 1) AS UNSIGNED),

                  /* inscripción del usuario actual */
                  'isEnrolled', CAST(EXISTS(
                                    SELECT 1
                                    FROM user_request ur2
                                    WHERE ur2.id_request = r.id
                                      AND ur2.id_user    = ?
                                  ) AS UNSIGNED),
                  'enrollmentStatus', (
                    SELECT CAST(ur3.status AS UNSIGNED)
                    FROM user_request ur3
                    WHERE ur3.id_request = r.id
                      AND ur3.id_user    = ?
                    LIMIT 1
                  ),

                  /* parámetros del request */
                  'parameters', (
                    SELECT JSON_ARRAYAGG(
                      JSON_OBJECT(
                        'idParameter', CAST(rp.id_parameter AS UNSIGNED),
                        'title',       p.title,
                        'body',        p.body,
                        'type',        CAST(p.type AS UNSIGNED),
                        'num',         CAST(rp.num AS SIGNED),
                        'valueString', rp.value_string,
                        'valueNum',    CAST(rp.value_num AS SIGNED)
                      )
                    )
                    FROM request_parameter rp
                    JOIN parameter p ON p.id = rp.id_parameter
                    WHERE rp.id_request = r.id
                  ),
                  /* inscritos (ordenados por created_at asc) */
                  'enrolled', (
                    SELECT JSON_ARRAYAGG(
                             JSON_OBJECT(
                               'idUser',     CAST(ur.id_user   AS UNSIGNED),
                               'username',   u2.username,
                               'reputation', CAST(u2.reputation AS DECIMAL(10,4)),
                               'votes',      CAST(u2.votes      AS UNSIGNED),
                               'status',     CAST(ur.status     AS UNSIGNED)
                             )
                             ORDER BY ur.created_at ASC
                           )
                    FROM user_request ur
                    JOIN `user` u2 ON u2.id = ur.id_user
                    WHERE ur.id_request = r.id
                  )
                ) AS payload
              FROM request r
              JOIN `user` u ON u.id = r.id_user
            ) feed
            ORDER BY feed.createdAt DESC, feed.id DESC
            LIMIT ?, ?";

    $db = new ODB();
    $db->query($sql, [$current_user_id, $current_user_id, $offset, $limit]);

    return $this->buildItems($db);
  }

  /**
   * @param int $limit   Nº de ítems por página
   * @param int $page    Página (0-based)
   * @return array       Lista de ítems del feed
   */
  public function getMessages(int $limit = 20, int $page = 0): array {
    $limit  = max(1, min($limit, 100));
    $offset = max(0, $page) * $limit;

    $sql = "SELECT
                bm.id,
                bm.created_at      AS `createdAt`,
                'message'          AS kind,
                bm.title           AS title,
                LEFT(bm.body, 200) AS preview,
                JSON_OBJECT(
                  'id',        CAST(bm.id      AS UNSIGNED),
                  'idUser',    CAST(bm.id_user AS UNSIGNED),
                  'username',  u.username,
                  'title',     bm.title,
                  'body',      bm.body,
                  'createdAt', bm.created_at
                ) AS payload
              FROM board_message bm
              JOIN `user` u ON u.id = bm.id_user
              LIMIT ?, ?";

    $db = new ODB();
    $db->query($sql, [$offset, $limit]);

    return $this->buildItems($db);
  }

  /**
   * Función para obtener solo la lista de eventos
   *
   * @param int $current_user_id Usuario actual (para is_enrolled)
   * @param int $limit   Nº de ítems por página
   * @param int $page    Página (0-based)
   * @return array       Lista de ítems del feed
   */
  public function getRequests(int $current_user_id, int $limit = 20, int $page = 0): array {
    $limit  = max(1, min($limit, 100));
    $offset = max(0, $page) * $limit;

    $sql = "SELECT
            r.id,
            r.created_at       AS `createdAt`,
            'request'          AS kind,
            r.title            AS title,
            LEFT(r.body, 200)  AS preview,
            JSON_OBJECT(
              'id',          CAST(r.id        AS UNSIGNED),
              'idUser',      CAST(r.id_user   AS UNSIGNED),
              'username',    u.username,
              'reputation',  CAST(u.reputation AS DECIMAL(10,4)),
              'votes',       CAST(u.votes      AS UNSIGNED),

              'title',       r.title,
              'body',        r.body,
              'reward',      CAST(r.reward    AS SIGNED),
              'status',      CAST(r.status    AS UNSIGNED),
              'completedAt', r.completed_at,
              'createdAt',   r.created_at,

              /* métricas de participantes */
              'participantsTotal',    CAST((SELECT COUNT(*) FROM user_request ur WHERE ur.id_request = r.id) AS UNSIGNED),
              'participantsAccepted', CAST((SELECT COUNT(*) FROM user_request ur WHERE ur.id_request = r.id AND ur.status = 1) AS UNSIGNED),

              /* inscripción del usuario actual */
              'isEnrolled', CAST(EXISTS(
                                SELECT 1
                                FROM user_request ur2
                                WHERE ur2.id_request = r.id
                                  AND ur2.id_user    = ?
                              ) AS UNSIGNED),
              'enrollmentStatus', (
                SELECT CAST(ur3.status AS UNSIGNED)
                FROM user_request ur3
                WHERE ur3.id_request = r.id
                  AND ur3.id_user    = ?
                LIMIT 1
              ),

              /* parámetros del request */
              'parameters', (
                SELECT JSON_ARRAYAGG(
                  JSON_OBJECT(
                    'idParameter', CAST(rp.id_parameter AS UNSIGNED),
                    'title',       p.title,
                    'body',        p.body,
                    'type',        CAST(p.type AS UNSIGNED),
                    'num',         CAST(rp.num AS SIGNED),
                    'valueString', rp.value_string,
                    'valueNum',    CAST(rp.value_num AS SIGNED)
                  )
                )
                FROM request_parameter rp
                JOIN parameter p ON p.id = rp.id_parameter
                WHERE rp.id_request = r.id
              ),
              /* inscritos (ordenados por created_at asc) */
              'enrolled', (
                SELECT JSON_ARRAYAGG(
                         JSON_OBJECT(
                           'idUser',     CAST(ur.id_user   AS UNSIGNED),
                           'username',   u2.username,
                           'reputation', CAST(u2.reputation AS DECIMAL(10,4)),
                           'votes',      CAST(u2.votes      AS UNSIGNED),
                           'status',     CAST(ur.status     AS UNSIGNED)
                         )
                         ORDER BY ur.created_at ASC
                       )
                FROM user_request ur
                JOIN `user` u2 ON u2.id = ur.id_user
                WHERE ur.id_request = r.id
              )
            ) AS payload
          FROM request r
          JOIN `user` u ON u.id = r.id_user
            ORDER BY r.created_at DESC, r.id DESC
            LIMIT ?, ?";

    $db = new ODB();
    $db->query($sql, [$current_user_id, $current_user_id, $offset, $limit]);

    return $this->buildItems($db);
  }
}
