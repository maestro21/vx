<v-toolbar height="50px" v-bind:class="{ adminpanel : adminpanel }">
	<v-icon @click.stop="adminpanel = !adminpanel">settings</v-icon>
	<?php
	$tabs = $class->tabs;
	if($tabs) { ?>
		<v-tabs dark fixed  v-model="tab">
			<?php  foreach($tabs as $k=> $tab) {	?>
				<v-tab><?php echo t($tab);?></v-tab>
			<?php } ?>
		 </v-tabs>
	<?php } ?>
</v-toolbar>
