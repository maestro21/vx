<v-navigation-drawer
  fixed
  v-model="adminpanel"
  app
>
  <v-list dense>
    	<?php $modules = cache('modules'); 
			if($modules) {
				foreach($modules as $module) { 
					if($module['status'] > 1) {?>
            <a href="<?php echo BASE_URL . $module['name'];?>">
              <v-list-tile>
  					    <v-list-tile-content>
          					<v-list-tile-title><?php echo T($module['name']);?></v-list-tile-title>
        					</v-list-tile-content>
               </v-list-tile>
             </a>    
      		<?php }
      		}
      	} ?>		
  </v-list>
</v-navigation-drawer>