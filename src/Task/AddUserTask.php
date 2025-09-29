<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Task;

use Osumi\OsumiFramework\Core\OTask;
use Osumi\OsumiFramework\App\Model\User;

class AddUserTask extends OTask {
	public function __toString() {
		return "addUser: Nueva tarea para añadir usuarios";
	}

	public function run(array $options = []): void {
		if (count($options) < 2) {
			echo "\nERROR: Debes indicar el nombre de usuario y contraseña a crear\n\n";
			echo "  php of addUser --username nombre --pass pass\n\n";
			exit();
		}

		$username = $options['username'];
		$pass = $options['pass'];

		$user = new User();
		$user->username = $username;
		$user->pass = password_hash($pass, PASSWORD_BCRYPT);
		$user->bio = null;
		$user->credits = 5;
		$user->reputation = 0;
		$user->votes = 0;
		$user->is_admin = true;
		$user->save();

		echo "\n\n  Usuario ".$username." creado con id ".$user->id."\n\n";
	}
}
