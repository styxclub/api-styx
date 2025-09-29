<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class ParameterValue extends OModel {
	#[OPK(
		comment: 'Id del valor'
	)]
	public ?int $id;

	#[OField(
		comment: 'Id del parámetro al que va el valor',
		nullable: false,
		ref: 'parameter.id'
	)]
	public ?int $id_parameter;

	#[OField(
		comment: 'Valor de texto',
		nullable: true,
		default: null,
		max: 200
	)]
	public ?string $value_string;

	#[OField(
		comment: 'Valor numérico',
		nullable: true,
		default: null
	)]
	public ?int $value_num;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;

	private ?Parameter $parameter = null;

	/**
	 * Save Parameter
	 *
	 * @param Parameter $parameter Parameter
	 *
	 * @return void
	 */
	public function setParameter(Parameter $parameter): void {
		$this->parameter = $parameter;
	}

	/**
	 * Get Parameter
	 *
	 * @return Parameter Parameter
	 */
	public function getParameter(): array {
		if (is_null($this->parameter)) {
			$this->loadParameter();
		}
		return $this->parameter;
	}

	/**
	 * Load Parameter
	 *
	 * @return void
	 */
	private function loadParameter(): void {
		$this->parameter = Parameter::findOne(['id' => $this->id_parameter]);
	}

}
