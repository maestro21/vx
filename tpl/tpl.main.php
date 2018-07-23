<!DOCTYPE HTML>
<html>
	<head>
		<?php echo tpl('head', array('class' => $class)); ?>
	</head>
	<body>
		<div id="vue">
			<v-app id="maestro">
				<?php if(superAdmin()) echo tpl('adminpanel'); ?>
				<v-content>
					<?php echo tpl('header', array('class' => $class));?>
						<v-tabs-items v-model="tab" v-scroll>
							<v-tab-item>
							<v-card flat class="page">
									<v-card-text><?php echo $content;?></v-card-text>
								  </v-card>
								</v-tab-item>
						</v-tabs-items>
				</v-content>
				<v-footer app inset>
					<v-container fill-height>
						<?php echo tpl('footer'); ?>
					</v-container>
			    </v-footer>
			</v-app>
		</div>
		<?php echo tpl('vue');?>
	</body>
</html>
