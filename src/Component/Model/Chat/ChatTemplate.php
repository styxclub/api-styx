<?php if (is_null($chat)): ?>
null
<?php else: ?>
{
	"id": {{ chat.id }},
	"idRequest": {{ chat.id_request | number }},
	"idUserFrom": {{ chat.id_user_from }},
	"username": "<?php echo urlencode($chat->getUserFrom()->username) ?>",
	"body": {{ chat.body | string }},
	"isRead": {{ chat.is_read | bool }},
	"createdAt": {{ chat.created_at | date }}
}
<?php endif ?>
