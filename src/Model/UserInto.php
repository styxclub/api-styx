<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class UserInto extends OModel {
	#[OPK(
		comment: 'Id del usuario',
		ref: 'user.id'
	)]
	public ?int $id_user;

	#[OPK(
		comment: 'Id del gusto',
		ref: 'into.id'
	)]
	public ?int $id_into;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;
}
