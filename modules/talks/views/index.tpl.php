<?php
$count = count($data['messages']);

if (!empty($data['user_id']) && !empty($data['position']))
{
	$user_id = $data['user_id'];
	$media   = $data['position'];
	
	if ($count)
	{
		echo '<hr style="margin: 15px 0;" />';
	}
}

foreach ($data['messages'] as $i => $message)
{
	if (!isset($user_id) || $user_id != $message['user_id'])
	{
		$media = isset($media) && $media == 'left' ? 'right' : 'left';
	}
	
	if (!isset($media))
	{
		$media = 'left';
	}
?>
<div class="media" data-message-id="<?php echo $message['message_id']; ?>" data-position="<?php echo $media; ?>">
<?php
	
	ob_start();
?>
	<div class="media-<?php echo $media; ?>">
		<a href="{base_url}members/<?php echo $message['user_id']; ?>/<?php echo url_title($message['username']); ?>.html">
			<img class="media-object" src="<?php echo $NeoFrag->user->avatar($message['avatar'], $message['sex']); ?>" style="max-width: 40px; max-height: 40px;" alt="" />
		</a>
	</div>
<?php
	$avatar = ob_get_clean();
	ob_start();
?>
	<div class="media-body<?php if ($media == 'right') echo ' text-right'; ?>">
		<?php
			if ($NeoFrag->user('user_id') == $message['user_id'] || is_authorized('talks', 'delete', $message['talk_id']))
			{
				echo '<div class="pull-'.($media == 'right' ? 'left' : 'right').'">'.button_delete($this->config->base_url.'ajax/talks/delete/'.$message['message_id'].'.html').'</div>';
			}
		?>
		<h4 class="media-heading">
		<?php
			$title = array($NeoFrag->user->link($message['user_id'], $message['username']), '<small><i class="fa fa-clock-o"></i> '.time_span($message['date']).'</small>');
			
			if ($media == 'right')
			{
				$title = array_reverse($title);
			}
			
			echo implode(' ', $title);
		?>
		</h4>
		<?php echo $message['message'] ? strtolink($message['message']) : '<i>Message supprimé</i>'; ?>
	</div>
<?php
	$output = array($avatar, ob_get_clean());
	
	if ($media == 'right')
	{
		$output = array_reverse($output);
	}
	
	echo implode($output);
?>
</div>
<?php
	if ($i < $count - 1)
	{
		echo '<hr style="margin: 15px 0;" />';
	}
	
	$user_id = $message['user_id']; 
}
?>
<?php if (!$count && empty($data['user_id']) && empty($data['position'])): ?>
	<div class="text-center">Aucun message dans la discussion</div>
<?php endif; ?>