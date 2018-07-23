<table>
<?php foreach ($fields as $k => $v){ ?>
	<tr>
		<td><?php echo T($k);?></td>
		<td><?php echo fType(@$fields[$k], $data[$k])?></td>
	</tr>	
<?php	
} ?>
</table>