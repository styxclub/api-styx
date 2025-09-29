<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\DTO;

use Osumi\OsumiFramework\DTO\ODTO;
use Osumi\OsumiFramework\DTO\ODTOField;

class LogoutDTO extends ODTO {
  #[ODTOField(required: true)]
  public ?string $refreshToken = null;

  #[ODTOField(required: false)]
  public ?bool $allDevices = null;
}
