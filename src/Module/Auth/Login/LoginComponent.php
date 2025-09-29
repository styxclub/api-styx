<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Auth\Login;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\App\DTO\LoginDTO;
use Osumi\OsumiFramework\App\Service\AuthService;
use Osumi\OsumiFramework\App\Service\ParameterService;
use Osumi\OsumiFramework\App\Component\Model\ParameterList\ParameterListComponent;

class LoginComponent extends OComponent {
  private ?AuthService $auth_service = null;
  private ?ParameterService $parameter_service = null;

  public string         $status              = 'error';
	public string | int   $user_id             = 'null';
	public ?string        $user_username       = null;
  public string | float $user_reputation     = 'null';
  public string | int   $user_votes          = 'null';
  public string | int   $user_credits        = 'null';
  public ?string        $user_bio            = null;
  public ?string        $user_is_active      = 'false';
	public ?string        $token_access_token  = null;
	public string | int   $token_expires_in    = 'null';
	public ?string        $token_refresh_token = null;
  public ?ParameterListComponent $parameters = null;

  public function __construct() {
    parent::__construct();
    $this->auth_service = inject(AuthService::class);
    $this->parameter_service = inject(ParameterService::class);
  }

  /**
   * Endpoint de login: valida credenciales y emite tokens.
   *
   * @param LoginDTO $data DTO con usuario y password
   * @return void
   */
  public function run(LoginDTO $data): void {
    global $core;
    $this->parameters = new ParameterListComponent();

    if (!$data->isValid()) {
      $core->setHttpStatus(400);
			return;
    }

    try {
      $result = $this->auth_service->login(
        username: trim((string) $data->username),
        password: (string) $data->password
      );

      $this->status              = 'ok';
			$this->user_id             = $result['user']['id'];
			$this->user_username       = $result['user']['username'];
      $this->user_reputation     = $result['user']['reputation'];
      $this->user_votes          = $result['user']['votes'];
      $this->user_credits        = $result['user']['credits'];
      $this->user_bio            = $result['user']['bio'];
      $this->user_is_active      = $result['user']['is_active'] ? 'true' : 'false';
			$this->token_access_token  = $result['tokens']['access_token'];
			$this->token_expires_in    = $result['tokens']['expires_in'];
			$this->token_refresh_token = $result['tokens']['refresh_token'];
      $this->parameters->list    = $this->parameter_service->getAllParameters();
    }
    catch (\Throwable $e) {
      $code = $e->getMessage();
      $http = match ($code) {
        'INVALID_CREDENTIALS' => 401,
        'TOKEN_SERVICE_NOT_CONFIGURED' => 500,
        default => 500
      };
      $core->setHttpStatus($http);
      $this->status               = 'error';
			$this->user_id              = 'null';
			$this->user_username        = null;
      $this->user_reputation      = 'null';
      $this->user_votes           = 'null';
      $this->user_credits         = 'null';
      $this->user_bio             = null;
      $this->user_is_active       = 'false';
			$this->token_access_token   = null;
			$this->token_expires_in     = 'null';
			$this->token_refresh_token  = null;
      $this->parameters->list     = [];
    }
  }
}
