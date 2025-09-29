<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class Request extends OModel {
	#[OPK(
		comment: 'Id de la solicitud'
	)]
	public ?int $id;

	#[OField(
		comment: 'Id del creador de la solicitud',
		nullable: false,
		ref: 'user.id'
	)]
	public ?int $id_user;

	#[OField(
		comment: 'Título de la solicitud',
		nullable: false,
		max: 100
	)]
	public ?string $title;

	#[OField(
		comment: 'Texto para describir la solicitud',
		type: OField::LONGTEXT,
		nullable: false
	)]
	public ?string $body;

	#[OField(
		comment: 'Coste en créditos de la solicitud',
		nullable: false
	)]
	public ?int $cost;

	#[OField(
		comment: 'Recompensa en créditos por la solicitud cumplida',
		nullable: false
	)]
	public ?int $reward;

	#[OField(
		comment: 'Estado de la solicitud, 0 nueva 1 cerrada 2 celebrada 3 votada',
		nullable: false,
		default: 0
	)]
	public ?int $status;

	#[OField(
		comment: 'Fecha de celebración del evento',
		type: OField::DATE,
		nullable: true,
		default: null
	)]
	public ?string $completed_at;

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
	private function loadUser(): void {
		$this->user = User::findOne(['id' => $this->id_user]);
	}
}
