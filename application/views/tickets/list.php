<div class="span10">
	<div class="content">
		<legend>Список
			тикетов <?php echo anchor('tickets/add', '<i class="icon-file"></i> Добавить новый', 'class="btn btn-small"'); ?>
			| <?php echo anchor('tickets/display_closed', '<i class="icon-remove-circle"></i> Закрытые', 'class="btn btn-small"'); ?></legend>
		<table class="table table-striped table-bordered table-condensed">
			<thead>
			<tr>
				<th></th>
				<?php foreach ($fields as $field_name => $field_display) : ?>
					<th <?php if ($sort_by == $field_name) echo "class=\"sort-$sort_order\""; ?> >
						<?php echo anchor("tickets/display/$field_name/" . (($sort_order == 'asc' && $field_name == $sort_by) ? 'desc' : 'asc'), $field_display); ?>
					</th>
				<?php endforeach; ?>

			</tr>
			</thead>
			<tbody>
			<?php foreach ($tickets as $ticket): ?>
				<tr>
					<td>
						<?php echo anchor('tickets/edit/' . $ticket['id'], '<i class="icon-edit"></i>', array('title' => 'редактировать')); ?>
					</td>
					<?php foreach ($fields as $field_name => $field_display) : ?>
						<td><?php echo $ticket[$field_name] ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		echo $pagination;
		?>
	</div>
</div>
