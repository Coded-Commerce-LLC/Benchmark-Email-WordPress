<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_benchmarkemaillite" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="dashboard" />
</form>

<h2><?php _e( 'COM_BENCHMARKEMAILLITE_HEADING_LISTS' ); ?></h2>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS1' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS2' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS3' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS4' ); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach( $contact_lists as $val ) { ?>
		<tr>
			<td><?php echo $val['id']; ?></td>
			<td><?php echo $val['listname']; ?></td>
			<td><?php echo $val['contactcount']; ?></td>
			<td><?php echo $val['createdDate']; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>

<h2><?php _e( 'COM_BENCHMARKEMAILLITE_HEADING_FORMS' ); ?></h2>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS1' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS2' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS3' ); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach( $signup_forms as $val ) { ?>
		<tr>
			<td><?php echo $val['id']; ?></td>
			<td><?php echo $val['listName']; ?></td>
			<td><?php echo $val['toListName']; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>
