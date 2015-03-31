<?php
/*
 * Plugin Name: Native-ads
 * Plugin URI: http://spoti.io
 * Description: Simple automated advertising opportunity for you, allowing advertisers to deliver relevant, videos and native ads placed in the heart of your editorial content. Once activated you'll find me under <strong>Settings &rarr; nativeads settings</strong>.
 * Version: 0.1
 * Author: Roberto Gomez
 * Author URI: http://spoti.io
 */
class nativeadsSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	//public countryList = [];

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Native-ads settings', 
            'manage_options', 
            'nativeads-settings', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
       $this->options = get_option( 'nativeadsOptions' );
		
		if ( isset ( $_GET['tab'] ) ) admin_tabs($_GET['tab']); else admin_tabs('settings');
		
        ?>
        <div class="wrap">
		<?php if (!isset ( $_GET['tab'] ) || $_GET['tab']=='settings') { ?>
            <h2>Native-ads settings</h2>           
            <form method="post">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'option_group' );   
                do_settings_sections( 'nativeads-settings' );
                submit_button(); 
            ?>
            </form>
			
			<form method="post">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'option_group' );   
                do_settings_sections( 'nativeads-contact' );
                submit_button("Send mail", "primary", "sendMail"); 
            ?>
            </form>
		<?php } else if ($_GET['tab']=='demo1') { ?> 
			<iframe width="100%" height="700px" src="http://kazoon.tv/video-intext-demo/"></iframe>
		<?php } else if ($_GET['tab']=='demo2') { ?> 
			<iframe width="100%" height="700px" src="http://kazoon.tv/video-intext-autoplay/"></iframe>
		<?php } else if ($_GET['tab']=='demo3') { ?> 
			<iframe width="100%" height="700px" src="http://kazoon.tv/native-intext-demo/"></iframe>
		<?php } ?>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'option_group', // Option group
            'nativeadsOptions', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

         add_settings_section(
             'setting_section_id', // ID
             'Native Ad will ad video or native advert into the existing content', // Title
             array( $this, 'print_section_info' ), // Callback
             'nativeads-settings' // Page
         );  

        add_settings_field(
            'script_field', // ID
            'Client ID', // Title 
            array( $this, 'script_field_callback' ), // Callback
            'nativeads-settings', // Page
            'setting_section_id' // Section           
        );

		add_settings_field(
            'countryFilter_field', // ID
            'In which countries do you want to display ads?', // Title 
            array( $this, 'countryFilter_field_callback' ), // Callback
            'nativeads-settings', // Page
            'setting_section_id' // Section           
        );

		register_setting(
            'option_group', // Option group
            'nativeadsOptions', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
		
		add_settings_section(
             'setting_section_id', // ID
             'Fill in the form and click Send mail to get your Client ID', // Title
             array( $this, 'print_section_info' ), // Callback
             'nativeads-contact' // Page
         );  
		
        add_settings_field(
            'name_field', 
            'Firstname', 
            array( $this, 'name_field_callback' ), 
            'nativeads-contact', 
            'setting_section_id'
        ); 
		
		add_settings_field(
            'lastname_field', 
            'Lastname', 
            array( $this, 'lastname_field_callback' ), 
            'nativeads-contact', 
            'setting_section_id'
        );

		add_settings_field(
            'email_field', 
            'Email to contact you', 
            array( $this, 'email_field_callback' ), 
            'nativeads-contact', 
            'setting_section_id'
        );
		
		add_settings_field(
            'country_field', 
            'Country', 
            array( $this, 'country_field_callback' ), 
            'nativeads-contact', 
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['script_field'] ) )
            $new_input['script_field'] = absint( $input['script_field'] );

		if( isset( $input['countryFilter_field'] ) )
            $new_input['countryFilter_field'] = sanitize_text_field( $input['countryFilter_field'] );
		
		/* ------------------------------ */
		
        if( isset( $input['name_field'] ) )
            $new_input['name_field'] = sanitize_text_field( $input['name_field'] );

		if( isset( $input['lastname_field'] ) )
            $new_input['lastname_field'] = sanitize_text_field( $input['lastname_field'] );
		
		if( isset( $input['email_field'] ) )
            $new_input['email_field'] = sanitize_text_field( $input['email_field'] );
		
		if( isset( $input['country_field'] ) )
            $new_input['country_field'] = sanitize_text_field( $input['country_field'] );		
		
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print ''; //Enter your settings below:
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function script_field_callback()
    {
        printf(
            '<input style="width:320px" type="text" id="script_field" name="nativeadsOptions[script_field]" value="%s" />',
            esc_attr( get_option( 'nativeadsOptions_script_field' ) ) 
        );
    }
	
	public function countryFilter_field_callback()
    {
		$countryList=get_option('nativeadsOptions_countryFilter_field');
		?>
		
			<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][GE]" value="1" <?php if (isset($countryList['GE'])) echo 'checked="checked"'; ?> />Germany<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][FR]" value="1" <?php if (isset($countryList['FR'])) echo 'checked="checked"'; ?> />France<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][ES]" value="1" <?php if (isset($countryList['ES']) || get_option( 'nativeadsOptions_countryFilter_field' ) == false) echo 'checked="checked"'; ?> />Spain<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][GB]" value="1" <?php if (isset($countryList['GB'])) echo 'checked="checked"'; ?> />UK<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][IT]" value="1" <?php if (isset($countryList['IT'])) echo 'checked="checked"'; ?> />Italy<br/>
			<input type="checkbox" name="nativeadsOptions[countryFilter_field][US]" value="1" <?php if (isset($countryList['US'])) echo 'checked="checked"'; ?> />USA<br/>
			<input type="hidden" name="nativeadsOptions[countryFilter_field][n]" value="1" />
		<?php        
		//echo(  $countryList["\[GE\]"] );
		//var_dump($countryList);
    }
	

    /** 
     * Get the settings option array and print one of its values
     */
    public function name_field_callback()
    {
        printf(
            '<input type="text" id="name_field" name="nativeadsOptions[name_field]" value="%s" />',
            esc_attr( get_option( 'nativeadsOptions_name_field' ) ) 
        );
    }
	
	public function lastname_field_callback()
    {
        printf(
            '<input type="text" id="lastname_field" name="nativeadsOptions[lastname_field]" value="%s" />',
            esc_attr( get_option( 'nativeadsOptions_lastname_field' ) ) 
        );
    }
	
	public function email_field_callback()
    {
        printf(
            '<input type="text" id="email_field" name="nativeadsOptions[email_field]" value="%s" />',
            esc_attr( get_option( 'nativeadsOptions_email_field' ) ) 
        );
    }
	
	public function country_field_callback()
    {
        printf(
            '<input type="text" id="country_field" name="nativeadsOptions[country_field]" value="%s" />',
            esc_attr( get_option( 'nativeadsOptions_country_field' ) ) 
        );
    }
}

if( is_admin() ) {	
	if (isset($_POST['nativeadsOptions']))
	foreach ($_POST['nativeadsOptions'] as $key=>$value) {	
		//var_dump($_POST['nativeadsOptions']);
		//die();
		update_option( 'nativeadsOptions_'.$key, $value );
	}
	if (isset($_POST['sendMail'])) {
		add_action( 'plugins_loaded', 'sendMail' );
		
		
		
	}	
	$my_settings_page = new nativeadsSettings();
}

function sendMail() {
	$mail=$_POST['nativeadsOptions']['email_field'];
	$name=$_POST['nativeadsOptions']['name_field'];		
	$lastname=$_POST['nativeadsOptions']['lastname_field'];
	$country=$_POST['nativeadsOptions']['country_field'];
	$theme = wp_get_theme();
	$theme = $theme->get( 'Name' );
	$url = get_site_url();
	$text = "Firstname: ".$name."<br/>";
	$text .= "Lastname: ".$lastname."<br/>";
	$text .= "E-Mail: ".$mail."<br/>";
	$text .= "Country: ".$country."<br/>";
	$text .= "Theme name: ".$theme."<br/>";
	$text .= "Site URL: <a href='".$url."'>".$url."</a><br/>";	
		
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	$to = 'io.spoti.publishers@gmail.com';
	$subject = 'Native ads web submission';
	wp_mail( $to, $subject, $text );
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}


function set_html_content_type() {
	return 'text/html';
}

add_filter ('wp_head', 'nativeadsOptionsAddContent');
function nativeadsOptionsAddContent() {
   //if(is_single()) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$country = json_decode(file_get_contents("http://target.spoti.io/country?ip=".$ip))->Country;		
		$countryFilter = get_option('nativeadsOptions_countryFilter_field');		
			
		if (isset($countryFilter[$country])) 
		echo "<script>
			window.spotiApiMaster='".get_option( 'nativeadsOptions_script_field')."';
			</script>
			<script src=\"http://content.spoti.io/js/spoti.io.ads.js\" async=\"\"></script>";
   //}
	//return $content;
}
	
function plugin_add_settings_link( $links ) {
	array_push( $links, '<a href="options-general.php?page=nativeads-settings">' . __( 'Settings' ) . '</a>' );
	array_push( $links, '<a target="_blank" href="http://kazoon.tv/video-intext-demo/">Demo video</a>' );
	array_push( $links, '<a target="_blank" href="http://kazoon.tv/video-intext-autoplay/">Demo video autoplay</a>' );
	array_push( $links, '<a target="_blank" href="http://kazoon.tv/native-intext-demo/">Demo image</a>' );
	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' ); 	


function admin_tabs( $current = 'settings' ) {
    $tabs = array( 'settings' => 'Settings', 'demo1' => 'Demo video', 'demo2' => 'Demo video autoplay', 'demo3' => 'Demo image' );
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=nativeads-settings&tab=$tab'>$name</a>";

    }
    echo '</h2>';
}




