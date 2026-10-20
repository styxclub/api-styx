<?php

declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Middleware;

use Osumi\OsumiFramework\Core\OMiddleware;
use Osumi\OsumiFramework\Plugins\OToken;

final class LoginMiddleware {
	/**
	 * Security middleware for players.
	 *
	 * Expects:
	 * Authorization: Bearer <jwt>
	 *
	 * @param string $phase Current middleware phase.
	 * @param array<string, mixed> $data Current middleware pipeline data.
	 *
	 * @return array<string, mixed> Middleware result.
	 */
	public static function handle(
		string $phase,
		array $data
	): array {
		if ($phase !== OMiddleware::PHASE_BEFORE) {
			return [];
		}

		global $core;

		$headers = $data['headers'] ?? [];

		if (!is_array($headers)) {
			return [
				'stop' => true,
				'status_code' => 403,
				'message' => ''
			];
		}

		$auth_header = self::extractAuthHeader(
			$headers
		);

		if ($auth_header === null) {
			return [
				'stop' => true,
				'status_code' => 403,
				'message' => ''
			];
		}

		$jwt = self::extractBearerToken(
			$auth_header
		);

		if ($jwt === null) {
			return [
				'stop' => true,
				'status_code' => 403,
				'message' => ''
			];
		}

		$token = new OToken(
			$core->config->getExtra('secret')
		);

		if (!$token->checkToken($jwt)) {
			return [
				'stop' => true,
				'status_code' => 403,
				'message' => ''
			];
		}

		return [
			'context' => [
				'status' => 'ok',
				'id' => null,
				'sub' => (int) $token->getParam('sub')
			]
		];
	}

	/**
	 * Get the Authorization header value case-insensitively.
	 *
	 * @param array<array-key, mixed> $headers HTTP headers.
	 *
	 * @return string|null Header value or null if missing.
	 */
	private static function extractAuthHeader(
		array $headers
	): ?string {
		foreach ($headers as $key => $value) {
			if (
				strcasecmp(
					(string) $key,
					'authorization'
				) !== 0 &&
				strcasecmp(
					(string) $key,
					'http_authorization'
				) !== 0
			) {
				continue;
			}

			if (is_array($value)) {
				if (!isset($value[0])) {
					return null;
				}

				return (string) $value[0];
			}

			return (string) $value;
		}

		return null;
	}

	/**
	 * Extract the JWT from a Bearer Authorization header.
	 *
	 * A raw JWT is also accepted for backward compatibility.
	 *
	 * @param string $auth_header Authorization header value.
	 *
	 * @return string|null JWT or null if the header is invalid.
	 */
	private static function extractBearerToken(
		string $auth_header
	): ?string {
		$auth_header = trim(
			$auth_header
		);

		$matches = [];

		if (
			preg_match(
				'/^Bearer\s+(\S+)$/i',
				$auth_header,
				$matches
			) === 1
		) {
			return $matches[1];
		}

		if (
			preg_match(
				'/^[A-Za-z0-9\-\_=]+\.[A-Za-z0-9\-\_=]+\.[A-Za-z0-9\-\_=]+$/',
				$auth_header
			) === 1
		) {
			return $auth_header;
		}

		return null;
	}
}
