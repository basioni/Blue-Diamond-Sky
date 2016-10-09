<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$es_errors = array();
$es_success = '';
$es_error_found = FALSE;
$csv = array();

// Preset the form fields
$form = array(
	'es_email_name' => '',
	'es_email_status' => '',
	'es_email_group' => '',
	'es_email_mail' => ''
);

// Form submitted, check the data
if (isset($_POST['es_form_submit']) && $_POST['es_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('es_form_add');
	
	$extension = pathinfo($_FILES['es_csv_name']['name'], PATHINFO_EXTENSION);
	//$extension = strtolower(end(explode('.', $_FILES['es_csv_name']['name'])));
	//$extension = end( $extension); 
	//$extension = end($extension);
	//echo $extension . "<br>"; 
	//$path_parts = pathinfo($fullPath);
	//$extension = strtolower($path_parts["extension"]);  

	$tmpname = $_FILES['es_csv_name']['tmp_name'];
	
	if($extension === 'csv')
	{
		$csv = es_cls_common::es_readcsv($tmpname);
	}
	
	if(count($csv) > 0)
	{
		$inserted = 0;
		$duplicate = 0;
		$invalid = 0;
		for ($i = 1; $i < count($csv) - 1; $i++)
		{
			$form["es_email_mail"] = trim($csv[$i][0]);
			$form["es_email_name"] = trim($csv[$i][1]);
			$form["es_email_group"] = "Public";
			$form["es_email_status"] = "Confirmed";
			$action = es_cls_dbquery::es_view_subscriber_ins($form, "insert");
			if( $action == "sus" )
			{
				$inserted = $inserted + 1;
			}
			elseif( $action == "ext" )
			{
				$duplicate = $duplicate + 1;
			}
			elseif( $action == "invalid" )
			{
				$invalid = $invalid + 1;
			}

			// Reset the form fields
			$form = array(
				'es_email_name' => '',
				'es_email_status' => '',
				'es_email_group' => '',
				'es_email_mail' => ''
			);
		}
		?>
		<div class="updated fade">
			<p><strong><?php echo $inserted; ?> <?php _e('Email(s) was successfully imported.', ES_TDOMAIN); ?></strong></p>
			<p><strong><?php echo $duplicate; ?> <?php _e('Email(s) are already in our database.', ES_TDOMAIN); ?></strong></p>
			<p><strong><?php echo $invalid; ?> <?php _e('Email(s) are invalid.', ES_TDOMAIN); ?></strong></p>
			<p><strong><a href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers">
			<?php _e('Click here', ES_TDOMAIN); ?></a> <?php _e(' to view the details', ES_TDOMAIN); ?></strong></p>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="error fade">
			<p><strong><?php _e('File upload failed or no data available in the csv file.', ES_TDOMAIN); ?></strong></p>
		</div>
		<?php
	}
}

if ($es_error_found == TRUE && isset($es_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $es_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($es_error_found == FALSE && isset($es_success[0]) == TRUE)
{
	?>
	  <div class="updated fade">
		<p>
		<strong>
		<?php echo $es_success; ?>
		<a href="<?php echo ES_ADMINURL; ?>?page=es-view-subscribers">
			<?php _e('Click here', ES_TDOMAIN); ?></a> <?php _e(' to view the details', ES_TDOMAIN); ?>
		</strong>
		</p>
	  </div>
	  <?php
	}
?>
<script language="javaScript" src="<?php echo ES_URL; ?>subscribers/view-subscriber.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ES_PLUGIN_DISPLAY, ES_TDOMAIN); ?></h2>
	<form name="form_addemail" id="form_addemail" method="post" action="#" onsubmit="return _es_importemail()" enctype="multipart/form-data">
      <h3><?php _e('Upload email', ES_TDOMAIN); ?></h3>
	  <label for="tag-image"><?php _e('Select csv file', ES_TDOMAIN); ?></label>
	  <input type="file" name="es_csv_name" id="es_csv_name" />
      <p><?php _e('Please select the input csv file. Please check official website for csv structure.', ES_TDOMAIN); ?></p>
	    
      <input type="hidden" name="es_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Upload CSV', ES_TDOMAIN); ?>" type="submit" />
		<input name="publish" lang="publish" class="button add-new-h2" onclick="_es_redirect()" value="<?php _e('Back', ES_TDOMAIN); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_es_help()" value="<?php _e('Help', ES_TDOMAIN); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('es_form_add'); ?>
    </form>
</div>
<p class="description"><?php echo ES_OFFICIAL; ?></p>
</div>