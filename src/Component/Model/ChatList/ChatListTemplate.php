<?php
use Osumi\OsumiFramework\App\Component\Model\Chat\ChatComponent;

foreach ($list as $i => $chat) {
  $component = new ChatComponent([ 'chat' => $chat ]);
	echo strval($component);
	if ($i < count($list) - 1) {
		echo ",\n";
	}
}
