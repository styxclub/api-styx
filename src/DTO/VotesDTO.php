<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\DTO;

use Osumi\OsumiFramework\DTO\ODTO;
use Osumi\OsumiFramework\DTO\ODTOField;

class VotesDTO extends ODTO {
  #[ODTOField(required: true)]
  public ?int $idRequest = null;

  #[ODTOField(required: true)]
  public ?array $votes = null;

  #[ODTOField(required: true, middleware:'Login', middlewareProperty: 'sub')]
	public ?int $idUser = null;
}
