<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class CreditLog extends OModel {
	#[OPK(
		comment: 'Id de una transacción'
	)]
	public ?int $id;

	#[OField(
		comment: 'Id del usuario',
		nullable: false,
		ref: 'user.id'
	)]
	public ?int $id_user;

	#[OField(
		comment: 'Id de la solicitud en la que se gano o gasto los puntos',
		nullable: true,
		default: null,
		ref: 'request.id'
	)]
	public ?int $id_request;

	#[OField(
		comment: 'Tipo de transacción, 0 créditos ganados 1 créditos gastados',
		nullable: false,
		default: null,
		max: 0
	)]
	public ?int $type;

	#[OField(
		comment: 'Cantidad de créditos ganados o gastados',
		nullable: false,
		default: null,
		max: 0
	)]
	public ?int $amount;

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

	private ?Request $request = null;

	/**
	 * Save Request
	 *
	 * @param Request $request Request
	 *
	 * @return void
	 */
	public function setRequest(Request $request): void {
		$this->request = $request;
	}

	/**
	 * Get Request
	 *
	 * @return Request Request
	 */
	public function getRequest(): array {
		if (!is_null($this->id_request) && is_null($this->request)) {
			$this->loadRequest();
		}
		return $this->request;
	}

	/**
	 * Load Request
	 *
	 * @return void
	 */
	private function loadRequest(): void {
		$this->request = Request::findOne(['id' => $this->id_request]);
	}

}
