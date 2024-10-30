<?php
function blogtopdf_options() {
	global $cc_login_type,$current_user,$wp_roles;

	$blogtopdf_options[] = array(	"name" => "Cache",
			"desc" => "Once you're satisifed everything works as expected, turn on cache to improve performance.",
			"id" => BLOGTOPDF_SLUG."_cached",
			"type" => "checkbox");
	
	$blogtopdf_options=apply_filters('blogtopdf_options_filter',$blogtopdf_options);
	
	return $blogtopdf_options;
}

function blogtopdf_add_admin() {

	global $blogtopdf;

	$blogtopdf_options=blogtopdf_options();

	if (isset($_GET['page']) && ($_GET['page'] == "blogtopdf")) {
		delete_option('blogtopdf_pro_local_key');
		if ( isset($_REQUEST['action']) && 'install' == $_REQUEST['action'] ) {
			delete_option('blogtopdf_cache');
			foreach ($blogtopdf_options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
				} else { delete_option( $value['id'] );
				}
			}
			header("Location: admin.php?page=blogtopdf&installed=true");
			die;
		}
	}
	
	add_options_page(BLOGTOPDF_NAME, BLOGTOPDF_NAME, 'activate_plugins', 'blogtopdf','blogtopdf_admin');
}

function blogtopdf_admin() {
	$controlpanelOptions=blogtopdf_options();

	if ( isset($_REQUEST['install']) ) echo '<div id="message" class="updated fade"><p><strong>'.BLOGTOPDF_NAME.' settings updated.</strong></p></div>';
	if ( isset($_REQUEST['error']) ) echo '<div id="message" class="updated fade"><p>The following error occured: <strong>'.$_REQUEST['error'].'</strong></p></div>';

	?>
<div class="wrap">
<div id="cc-left" style="position: relative; float: left; width: 50%; min-height:500px;">
<h2><?php echo BLOGTOPDF_NAME; ?></h2>
	<?php
	$blogtopdf_version=get_option("blogtopdf_version");
	$submit='Update';
	?>
<form method="post">
<?php require(dirname(__FILE__).'/includes/cpedit.inc.php')?>
<p class="submit"><input name="install" type="submit" value="<?php echo $submit;?>" /> <input
	type="hidden" name="action" value="install"
/></p>
</form>
</div>
<div id="cc-right" style="position: relative; float: right; width: 50%">
<h3>Help</h3>
<p>To use Blog To PDF on your website, simply activate one of the Blog To PDF widgets. The widget adds a Download PDF button on your site allowing a user to download a PDF version of the page (and sub-pages) they are viewing.</p>
<p>In the widget you can specify the title of the PDF document to generate, you can include a front page image and you can also define on what pages (and sub-pages) the Download PDF button should appear.</p>
<h3>Discover Blog To PDF Pro</h3>
Blog To PDF Pro offers additional features:
<ul>
<li>- Page numbers</li>
<li>- Table of contents</li>
<li>- Add an image to the front page of the generated PDF</li>
<li>- Choose the Wordpress theme used to style the PDF</li>
</ul>
<p>Blog To PDF Pro can be downloaded <a href="http://www.zingiri.com/blog-to-pdf" target="_blank">here</a></p>
</div>
<div style="clear:both;"></div>
<hr />
<center><img src="<?php echo plugins_url('blogtopdf')?>/images/zingiri-logo-transparent.png" />
</center>
 <?php
//require(dirname(__FILE__).'/includes/support-us.inc.php');
//zing_support_us('the-slider','the-slider','blogtopdf',THESLIDER_VERSION);
}
add_action('admin_menu', 'blogtopdf_add_admin');