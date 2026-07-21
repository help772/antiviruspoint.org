<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Backups

*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$of_options[] = array( 	
			"name" 		=> esc_html__('Backup Options' , 'hexwp'),
			"type" 		=> "heading",
			"id" 		=> "head-backup",
			"icon"		=> ADMIN_IMAGES . "backup.png"
		);
	$of_options[] = array(
					"position"		=> "start",
						"id" 			=> "backup_start",	
						"type"		=> "content"
				);
										
	$of_options[] = array( 
			"name" 		=> esc_html__('Backup and Restore Options' , 'hexwp'),
			"id" 		=> "of_backup",
			"std" 		=> "",
			"type" 		=> "backup",
			"desc" 		=> esc_html__('You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back.' , 'hexwp'),
		);
						
		$of_options[] = array( 
			"name" 		=> esc_html__('Transfer Theme Options Data' , 'hexwp'),
			"id" 		=> "of_transfer",
			"std" 		=> "",
			"type" 		=> "transfer",
			"desc" 		=> esc_html__('You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options' , 'hexwp')
		);
		$of_options[] = array( 	"position"	 	=> "end",
 						"type"			=> "content"
			);	