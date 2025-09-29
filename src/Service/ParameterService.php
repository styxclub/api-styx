<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Service;

use Osumi\OsumiFramework\Core\OService;
use Osumi\OsumiFramework\App\Model\Parameter;

class ParameterService extends OService {
  /**
   * Función para obtener el listado de parámetros
   *
   * @return array Listado de parámetros
   */
  public function getAllParameters(): array {
    return Parameter::all(['order_by' => 'title']);
  }
}
