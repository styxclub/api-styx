<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Model;

use Osumi\OsumiFramework\ORM\OModel;
use Osumi\OsumiFramework\ORM\OPK;
use Osumi\OsumiFramework\ORM\OField;
use Osumi\OsumiFramework\ORM\OCreatedAt;
use Osumi\OsumiFramework\ORM\OUpdatedAt;

class RequestParameter extends OModel {
	#[OPK(
		comment: 'Id de la solicitud',
		ref: 'request.id'
	)]
	public ?int $id_request;

	#[OPK(
		comment: 'Id del tipo de parámetro',
		ref: 'parameter.id'
	)]
	public ?int $id_parameter;

	#[OField(
		comment: 'Numero de veces que se usa el parámetro',
		nullable: false,
		default: 1
	)]
	public ?int $num;

	#[OField(
		comment: 'Coste en créditos del parámetro',
		nullable: false,
		default: 0
	)]
	public ?int $cost;

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
		if (is_null($this->request)) {
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
