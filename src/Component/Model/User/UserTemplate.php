<?php
use Osumi\OsumiFramework\App\Component\Model\IntoList\IntoListComponent;
?>
<?php if (is_null($user)): ?>
null
<?php else: ?>
{
	"id": {{ user.id }},
	"username": {{ user.username | string }},
	"bio": {{ user.bio | string }},
	"reputation": {{ user.reputation }},
	"votes": {{ user.votes }},
	"intos": [<?php echo $full ? strval(new IntoListComponent([ 'list' => $user->getIntos() ])) : '' ?>]
}
<?php endif ?>
