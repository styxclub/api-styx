<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class Invite extends OModel {
	#[OPK(
		comment: 'Id de la invitación'
	)]
	public ?int $id;

	#[OField(
		comment: 'Indica si se ha abierto la invitación',
		default: false
	)]
	public ?bool $is_opened;

	#[OField(
		comment: 'Indica si se ha invitado un hombre 0 o una mujer 1',
		nullable: true,
		default: null
	)]
	public ?int $gender;

	#[OField(
		comment: 'Notas u observaciones',
		type: OField::LONGTEXT,
		nullable: true,
		default: null
	)]
	public ?string $notes;

	#[OField(
		comment: 'Id del usuario, si acepta la invitación',
		nullable: true,
		default: null,
		ref: 'user.id'
	)]
	public ?int $id_user;

	#[OField(
		comment: 'Indica si la invitación esta activa',
		default: false
	)]
	public ?bool $active;

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
	 * @return User User
	 */
	public function getUser(): array {
		if (!is_null($this->id_user) && is_null($this->user)) {
			$this->loadUser();
		}
		return $this->user;
	}

	/**
	 * Load User
	 *
	 * @return void
	 */
	private function loadUser(): void {
		$this->user = User::findOne(['id' => $this->id_user]);
	}

}
