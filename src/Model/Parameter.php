<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class Parameter extends OModel {
	#[OPK(
		comment: 'Id del tipo de requisito'
	)]
	public ?int $id;

	#[OField(
		comment: 'Título del requisito',
		nullable: false,
		default: null,
		max: 100
	)]
	public ?string $title;

	#[OField(
		comment: 'Texto para describir el requisito',
		type: OField::LONGTEXT,
		nullable: false
	)]
	public ?string $body;

	#[OField(
		comment: 'Coste del requisito',
		nullable: false
	)]
	public ?int $cost;

	#[OField(
		comment: 'Tipo de parámetro: 0 texto, 1 numero, 2 fecha, 3 bool',
		nullable: false
	)]
	public ?int $type;

	#[OCreatedAt(
		comment: 'Fecha de creación del registro'
	)]
	public ?string $created_at;

	#[OUpdatedAt(
		comment: 'Fecha de ultima modificación del registro'
	)]
	public ?string $updated_at;

	private ?array $values = null;

	/**
	 * Set parameter values
	 *
	 * @param array $values List of values for the parameter
	 *
	 * @return void
	 */
	public function setValues(array $values): void {
		$this->values = $values;
	}

	/**
	 * Get parameter values
	 *
	 * @return array List of values for the parameter
	 */
	public function getValues(): array {
		if (is_null($this->values)) {
			$this->loadValues();
		}
		return $this->values;
	}

	/**
	 * Load parameter values
	 *
	 * @return void
	 */
	private function loadValues(): void {
		$parameter_values = ParameterValue::where(['id_parameter' => $this->id]);
		$values = [];
		if ($this->type === 0) {
			foreach ($parameter_values as $parameter_value) {
				$values[] = $parameter_value->value_string;
			}
		}
		if ($this->type === 1) {
			foreach ($parameter_values as $parameter_value) {
				$values[] = $parameter_value->value_num;
			}
		}
		$this->values = $values;
	}
}
