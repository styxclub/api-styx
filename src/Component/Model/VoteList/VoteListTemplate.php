<?php
use Osumi\OsumiFramework\App\Component\Model\Vote\VoteComponent;

foreach ($list as $i => $vote) {
  $component = new VoteComponent([ 'vote' => $vote ]);
	echo strval($component);
	if ($i < count($list) - 1) {
		echo ",\n";
	}
}
