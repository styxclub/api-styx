<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;
use Osumi\OsumiFramework\ORM\ODB;

class User extends OModel {
	#[OPK(
		comment: 'Id del usuario'
	)]
	public ?int $id;

	#[OField(
		comment: 'Nombre de usuario',
		nullable: false,
		max: 50
	)]
	public ?string $username;

	#[OField(
		comment: 'Contraseña cifrada del usuario',
		nullable: false,
		max: 100
	)]
	public ?string $pass;

	#[OField(
		comment: 'Auto descripción de un usuario',
		type: OField::LONGTEXT,
		nullable: true,
		default: null
	)]
	public ?string $bio;

	#[OField(
		comment: 'Numero de créditos disponibles para el usuario',
		nullable: false,
		default: 0
	)]
	public ?int $credits;

	#[OField(
		comment: 'Valoración media de un usuario',
		nullable: false,
		default: 0
	)]
	public ?float $reputation;

	#[OField(
		comment: 'Numero de valoraciones que ha recibido el usuario',
		nullable: false,
		default: 0
	)]
	public ?int $votes;

	#[OField(
		comment: 'Indica si el usuario está activo o no 0',
		default: true
	)]
	public ?bool $is_active;

	#[OField(
		comment: 'Indica si es admin 1 o no 0',
		default: false
	)]
	public ?bool $is_admin;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;

	private ?array $into = null;

	/**
	 * Save intos
	 *
	 * @param array $intos List of user intos
	 *
	 * @return void
	 */
	public function setIntos(array $intos): void {
		$this->intos = $intos;
	}

	/**
	 * Get intos
	 *
	 * @return array List of user intos
	 */
	public function getIntos(): array {
		if (is_null($this->intos)) {
			$this->loadIntos();
		}
		return $this->intos;
	}

	/**
	 * Load intos
	 *
	 * @return void
	 */
	private function loadIntos(): void {
		$sql = "SELECT * FROM `into` WHERE `id` IN (SELECT `id_into` FROM `user_into` WHERE `id_user` = ?)";
		$db = new ODB();
		$db->query($sql, [$this->id]);
		$list = [];
		while ($res = $db->next()) {
			$list[] = Into::from($res);
		}
		$this->intos = $list;
	}
}
