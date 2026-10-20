<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class AuthRefreshToken extends OModel {
	#[OPK(
		comment: 'PK'
	)]
	public ?int $id;

	#[OField(
		comment: 'Id del usuario',
		nullable: false,
		default: 0,
		ref: 'user.id'
	)]
	public ?int $id_user;

	#[OField(
		comment: 'Token de refresco',
		nullable: false,
		max: 64
	)]
	public ?string $token;

	#[OField(
		comment: 'Expiración',
		nullable: false,
		type: OField::DATETIME
	)]
	public ?string $expires_at;

	#[OField(
		comment: 'Revocado',
		nullable: false,
		default: false
	)]
	public ?bool $revoked;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;

  private ?User $user = null;

	/**
	 * Save User
	 *
	 * @param User $user User
	 *
	 * @return void
	 */
	public function setUser(User $user): void {
		$this->user = $user;
	}

	/**
	 * Get User
	 *
	 * @return User user
	 */
	public function getUser(): array {
		if (is_null($this->user)) {
			$this->loadUser();
		}
		return $this->user;
	}

	/**
	 * Load User
	 *
	 * @return void
	 */
	private function loadUsers(): void {
		$this->user = User::findOne(['id' => $this->id_user]);
	}
}
