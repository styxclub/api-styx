<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\ORM\ODB;
use Osumi\OsumiFramework\App\Service\TokenService;
use Osumi\OsumiFramework\App\Model\AuthRefreshToken;
use Osumi\OsumiFramework\App\Model\User;

/**
 * Servicio de autenticación: refresco y cierre de sesión.
 */
class AuthService extends OService {
  private ?TokenService $token_service = null;

	function __construct() {
		$this->token_service = inject(TokenService::class);
	}

  /**
   * Método para obtener un usuario a partir de su id
   *
   * @param int $id_user Id del usuario
   * @return User | null Devuelve el usuario solicitado o null si no se encuentra
   */
  public function getUser(int $id_user): User | null {
    return User::findOne(['id' => $id_user]);
  }

  /**
   * Realiza el login de un usuario: valida credenciales, emite tokens
   * y devuelve datos del usuario.
   *
   * @param string $username Nombre de usuario
   * @param string $password Contraseña en claro
   * @return array{
   *   user: array{id:int,username:string},
   *   tokens: array{access_token:string,expires_in:int,refresh_token:string}
   * }
   *
   * @throws \RuntimeException INVALID_CREDENTIALS | TOKEN_SERVICE_NOT_CONFIGURED
   */
  public function login(string $username, string $password): array {
    // 1) Buscar usuario por nombre de usuario
    $user = User::findOne(['username' => $username]);
    if (is_null($user)) {
      throw new \RuntimeException('INVALID_CREDENTIALS');
    }
    $id_user       = $user->id;
    $password_hash = $user->pass;
    $reputation    = $user->reputation;
    $votes         = $user->votes;
    $credits       = $user->credits;
    $bio           = $user->bio;

    // 2) Verificar contraseña
    if (!password_verify($password, $password_hash)) {
      throw new \RuntimeException('INVALID_CREDENTIALS');
    }

    // 3) Tokens
    $access_token  = $this->token_service->createAccessToken($id_user);
    $expires_in    = $this->token_service->getAccessTokenTtl();

    $refresh_token = bin2hex(random_bytes(32));
    $art = AuthRefreshToken::create();
    $art->id_user    = $id_user;
    $art->token      = $refresh_token;
    $art->expires_at = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // 30 días
    $art->revoked    = false;
    $art->save();

    return [
      'user' => [
        'id'         => $id_user,
        'username'   => $username,
        'reputation' => $reputation,
        'votes'      => $votes,
        'credits'    => $credits,
        'bio'        => $bio
      ],
      'tokens' => [
        'access_token'  => $access_token,
        'expires_in'    => $expires_in,
        'refresh_token' => $refresh_token
      ]
    ];
  }

  /**
   * Refresca el access token usando un refresh token válido.
   * Aplica rotación de refresh token: revoca el actual y emite uno nuevo.
   *
   * @param string $refresh_token Refresh token recibido del cliente
   * @return array{access_token:string,expires_in:int,refresh_token:string} Tokens nuevos
   *
   * @throws \RuntimeException INVALID_REFRESH_TOKEN | REFRESH_TOKEN_REVOKED | REFRESH_TOKEN_EXPIRED | TOKEN_SERVICE_NOT_CONFIGURED
   */
  public function refreshTokens(string $refresh_token): array {
    // 1) Buscar token
    $art = AuthRefreshToken::findOne(['token' => $refresh_token]);
    if (is_null($art)) {
      throw new \RuntimeException('INVALID_REFRESH_TOKEN');
    }

    // 2) Validaciones
    if ($art->revoked) {
      throw new \RuntimeException('REFRESH_TOKEN_REVOKED');
    }
    $expires_at_ts = strtotime($art->expires_at);
    if ($expires_at_ts !== false && $expires_at_ts < time()) {
      throw new \RuntimeException('REFRESH_TOKEN_EXPIRED');
    }
    $id_user = $art->id_user;

    // 3) Rotar refresh token: revocar el actual y crear uno nuevo
    $art->revoked = true;
    $art->save();

    $new_refresh_token = bin2hex(random_bytes(32));
    $art = AuthRefreshToken::create();
    $art->id_user    = $id_user;
    $art->token      = $new_refresh_token;
    $art->expires_at = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // 30 días
    $art->revoked    = false;
    $art->save();

    // 4) Emitir nuevo access token
    $access_token = $this->token_service->createAccessToken($id_user);
    $expires_in   = $this->token_service->getAccessTokenTtl();

    return [
      'access_token'  => $access_token,
      'expires_in'    => $expires_in,
      'refresh_token' => $new_refresh_token
    ];
  }

  /**
   * Cierra sesión revocando el refresh token. Si $all_devices es true,
   * revoca todos los tokens del jugador propietario.
   *
   * @param string $refresh_token Refresh token a revocar
   * @param bool $all_devices Si true, revoca todos los tokens del jugador
   * @return void
   *
   * @throws \RuntimeException INVALID_REFRESH_TOKEN
   */
  public function logout(string $refresh_token, bool $all_devices = false): void {
    // 1) Obtener el token para identificar al usuario
    $art = AuthRefreshToken::findOne(['token' => $refresh_token]);
    if (is_null($art)) {
      throw new \RuntimeException('INVALID_REFRESH_TOKEN');
    }
    $id_user = $art->id_user;

    // 2) Revocación
    if ($all_devices) {
      $db = new ODB();
      $db->query(
        'UPDATE `auth_refresh_token`
            SET `revoked` = 1, `updated_at` = NOW()
          WHERE `id_user` = :p AND `revoked` = 0',
        ['p' => $id_user]
      );
    } else {
      $art->revoked = true;
      $art->save();
    }
  }
}
