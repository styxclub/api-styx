<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Filter;

use Osumi\OsumiFramework\Plugins\OToken;

class LoginFilter {
	/**
	 * Security filter for players (expects: Authorization: Bearer <jwt>)
	 *
	 * @param array $params   Parameter array received on the call
	 * @param array $headers  HTTP header array received on the call
	 *
	 * @return array{status:string,id:int|null} Filter status and player id (from JWT "sub")
	 */
	public static function handle(array $params, array $headers): array {
		global $core;

		$ret = ['status' => 'error', 'id' => null];

		$auth_header = self::extractAuthHeader($headers);
		if ($auth_header === null) {
			return $ret;
		}

		$jwt = self::extractBearerToken($auth_header);
		if ($jwt === null) {
			return $ret;
		}

		$tk = new OToken($core->config->getExtra('secret'));
		if ($tk->checkToken($jwt)) {
			$ret['status'] = 'ok';
			$ret['sub'] = intval($tk->getParam('sub'));
		}

		return $ret;
	}

	/**
	 * Gets the Authorization header value (case-insensitive).
	 *
	 * @param array $headers HTTP headers array
	 * @return string|null   The header value or null if missing
	 */
	private static function extractAuthHeader(array $headers): ?string {
		// Normaliza claves y recupera primero que coincida con "authorization"
		foreach ($headers as $key => $value) {
			if (strcasecmp((string)$key, 'authorization') === 0 || strcasecmp((string)$key, 'http_authorization') === 0) {
				// Puede venir como string o array (coge el primero)
				if (is_array($value)) {
					return isset($value[0]) ? (string)$value[0] : null;
				}
				return (string) $value;
			}
		}
		return null;
	}

	/**
	 * Extracts the JWT from a "Bearer <token>" header. Accepts raw JWT as fallback.
	 *
	 * @param string $auth_header Full Authorization header value
	 * @return string|null        JWT string or null if invalid
	 */
	private static function extractBearerToken(string $auth_header): ?string {
		$auth_header = trim($auth_header);

		// Caso estándar: "Bearer <token>"
		if (preg_match('/^Bearer\s+(\S+)$/i', $auth_header, $m)) {
			return $m[1];
		}

		// Fallback: si ya viene solo el token (sin "Bearer")
		if (preg_match('/^[A-Za-z0-9\-\_=]+\.[A-Za-z0-9\-\_=]+\.[A-Za-z0-9\-\_=]+$/', $auth_header)) {
			return $auth_header;
		}

		return null;
	}
}
