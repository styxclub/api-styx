<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\Plugins\OToken;

/**
 * Servicio para crear y (opcionalmente) verificar access tokens JWT usando OToken.
 */
class TokenService extends OService {
  private string $secret;
  private int $access_ttl;

  /**
   * Carga configuración desde config extra.
   */
  public function __construct() {
    $this->secret = $this->getConfig()->getExtra('secret');
    $this->access_ttl = $this->getConfig()->getExtra('access_ttl');
  }

  /**
   * Devuelve el TTL (segundos) del access token.
   *
   * @return int Segundos de validez del access token
   */
  public function getAccessTokenTtl(): int {
    return $this->access_ttl;
  }

  /**
   * Crea un access token para un usuario usando OToken.
   * Añade 'sub' (id del usuario)
   *
   * @param int $id_user Id del usuario
   * @param array<string,int|string|float|bool> $extra_claims (opcional) claims adicionales
   *
   * @return string JWT generado
   */
  public function createAccessToken(int $id_user, array $extra_claims = []): string {
    $tk = new OToken($this->secret);
    $tk->addParam('sub', $id_user);

    // Claims extra si los pasas
    foreach ($extra_claims as $k => $v) {
      $tk->addParam($k, $v);
    }

    // TTL / exp + iat
    $tk->setEXP(time() + $this->access_ttl);

    return $tk->getToken();
  }

  /**
   * Verifica un JWT (string) y devuelve sus claims + iat/exp.
   * Útil si quieres validar dentro de servicios/endpoints (tu LoginFilter ya valida).
   *
   * @param string $jwt Token JWT (sin 'Bearer ')
   * @return array{
   *   valid: bool,
   *   claims?: array<string, mixed>,
   *   iat?: int|null,
   *   exp?: int|null
   * }
   */
  public function verifyAccessToken(string $jwt): array {
    $tk = new OToken($this->secret);
    $ok = $tk->checkToken($jwt);
    if (!$ok) {
      return ['valid' => false];
    }
    $claims = $tk->getParams();
    return [
      'valid'  => true,
      'claims' => $claims,
      'iat'    => $tk->getIAT(),
      'exp'    => $tk->getEXP()
    ];
  }
}
