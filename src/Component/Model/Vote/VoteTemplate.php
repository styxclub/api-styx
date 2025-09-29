<?php
use Osumi\OsumiFramework\App\Component\Model\User\UserComponent;
?>
<?php if (is_null($vote)): ?>
null
<?php else: ?>
{
	"id": {{ vote.id }},
	"idUserFrom": {{ vote.id_user_from }},
	"userFrom": <?php echo strval(new UserComponent([ 'user' => $vote->getUserFrom(), 'full' => false ])) ?>,
	"idUserTo": {{ vote.id_user_to }},
	"userTo": <?php echo strval(new UserComponent([ 'user' => $vote->getUserTo(), 'full' => false ])) ?>,
	"idRequest": {{ vote.id_request }},
	"vote": {{ vote.vote }}
}
<?php endif ?>
