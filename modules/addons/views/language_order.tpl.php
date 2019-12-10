<ul id="langlist" class="list-group mb-3">
		<?php foreach ($langs as $lang): ?>
			<li class="list-group-item lang<?php echo $lang->__addon->id ?>" data-id="<?php echo $lang->__addon->id ?>" data-name="<?php echo $lang->info()->name ?>" data-update="<?php echo 'admin/addons/order/'.$lang->__addon->url() ?>">
				<ul class="list-inline m-0">
                    <li class="list-inline-item"><?php echo NeoFrag()->button_sort($lang->__addon->id, 'admin/addons/order/'.$lang->__addon->url()) ?></li>
                    <li class="list-inline-item"><?php echo $lang->info()->icon ?></li>
                    <li class="list-inline-item"><?php echo $lang->info()->title ?></li>
					<li class="list-inline-item"><?php echo $lang->settings()->order ?></li>
					<?php $langid[] = $lang->__addon->id; ?>
				</ul>
			</li>
		<?php endforeach ?>
		</ul>

		<span class="btn btn-primary" onClick="send_langs()"><?php echo $this->lang('Update'); ?></span>
		<span class="btn btn-light float-right" data-dismiss="modal"><?php echo $this->lang('Fermer'); ?></span>

<script type="text/javascript">
	$(function(){
		//OK
		$('.list-group').sortable({
			cursor: 'move',
			intersect: 'pointer',
			revert: true
		});
	});
	function send_langs(){
		var langid = [];
    			<?php foreach($langid as $key => $val){ ?>
					langid.push('<?php echo $val; ?>');
				<?php } ?>
				
		var nodes = Array.prototype.slice.call( document.getElementById('langlist').children );
				langid.forEach(function(tempid) {
					liRef = document.getElementsByClassName('lang' + tempid)[0];
					pos = nodes.indexOf( liRef ) + 1;
					var url = '/' + liRef.dataset.name + '/' + liRef.dataset.update;
					$.ajax({
						url: url,
						type: 'post',
						dataType: 'text',
						data : {position:pos},
						success: function(){
    						location.reload();
  						}
					});
			});
	}
</script>