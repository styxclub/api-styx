<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class Chat extends OModel {
	#[OPK(
		comment: 'Id único de cada línea del chat'
	)]
	public ?int $id;

	#[OField(
		comment: 'Id de la solicitud en la que se ha hablado',
		nullable: true,
		default: null,
		ref: 'request.id'
	)]
	public ?int $id_request;

	#[OField(
		comment: 'Id del usuario que envía el mensaje',
		nullable: false,
		ref: 'user.id'
	)]
	public ?int $id_user_from;

	#[OField(
		comment: 'Id del usuario al que va enviado el mensaje',
		nullable: false,
		ref: 'user.id'
	)]
	public ?int $id_user_to;

	#[OField(
		comment: 'Texto de la línea del chat',
		type: OField::LONGTEXT
	)]
	public ?string $body;

	#[OField(
		comment: 'Indica si el mensaje se ha leido 1 o no 0',
		default: false
	)]
	public ?bool $is_read;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;

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
	 * @return Request request list
	 */
	public function getRequest(): array {
		if (!is_null($this->request) && is_null($this->request)) {
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

	private ?User $user_from = null;

	/**
	 * Save User from
	 *
	 * @param User $user User from
	 *
	 * @return void
	 */
	public function setUserFrom(User $user_from): void {
		$this->user_from = $user_from;
	}

	/**
	 * Get User from
	 *
	 * @return User User from
	 */
	public function getUserFrom(): User {
		if (is_null($this->user_from)) {
			$this->loadUserFrom();
		}
		return $this->user_from;
	}

	/**
	 * Load User from
	 *
	 * @return void
	 */
	private function loadUserFrom(): void {
		$this->user_from = User::findOne(['id' => $this->id_user_from]);
	}

	private ?User $user_to = null;

	/**
	 * Save User to
	 *
	 * @param User $user User to
	 *
	 * @return void
	 */
	public function setUserTo(User $user_to): void {
		$this->user_to = $user_to;
	}

	/**
	 * Get User to
	 *
	 * @return User User to
	 */
	public function getUserTo(): User {
		if (is_null($this->user_to)) {
			$this->loadUserTo();
		}
		return $this->user_to;
	}

	/**
	 * Load User to
	 *
	 * @return void
	 */
	private function loadUserTo(): void {
		$this->user_to = User::findOne(['id' => $this->id_user_to]);
	}
}
