<?php
class featured_update_class
{
    public $featured_slider_current_version;
    public $featured_slider_update_path;
    public $featured_slider_plugin_slug;
    public $featured_slider_slug;
    function __construct($featured_slider_current_version, $featured_slider_update_path, $featured_slider_plugin_slug)
    { 
        // Set the class public variables
        $this->current_version = $featured_slider_current_version;
        $this->update_path = $featured_slider_update_path;
        $this->plugin_slug = $featured_slider_plugin_slug;
        list ($t1, $t2) = explode('/', $featured_slider_plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'featured_check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'featured_check_info'), 10, 3);
    }

    public function featured_check_update($transient)
    { 
        if (empty($transient->checked)) {
            return $transient;
        }
		$featured_license_key=get_option('featured_license_key');
        $remote_version = $this->featured_getRemote_version();

        if (version_compare($this->current_version, $remote_version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version;
            $obj->url = $this->update_path;
			if(isset($featured_license_key) && !empty($featured_license_key))
				$obj->package = 'http://slidervilla.com/store/receipt/?duid='.$featured_license_key;
            $transient->response[$this->plugin_slug] = $obj;
        }
        return $transient;
    }

    public function featured_check_info($false, $action, $arg)
    { 
        if ($arg->slug === $this->slug or $arg->slug === $this->plugin_slug) {
            $featured_license_key=get_option('featured_license_key');
			$information = $this->featured_getRemote_information();
			if(isset($featured_license_key) && !empty($featured_license_key)){
				$information = (array)$information;
				$information['download_link']='http://slidervilla.com/store/receipt/?duid='.$featured_license_key;
				$information=(object)$information;
			}
			return $information;
        }
        return $false;
    }

    public function featured_getRemote_version()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'version')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
    }

    public function featured_getRemote_information()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'info')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize($request['body']);
        }
        return false;
    }

    public function featured_getRemote_license()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'license')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
    }
}