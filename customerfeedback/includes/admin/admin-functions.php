<?php
class My_Admin_Functions {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_scripts'));
        add_action('admin_menu', array($this, 'add_feedback_form_menu'));
        add_action('admin_menu', array($this, 'add_feedback_analysis_menu'));
        add_action('wp_ajax_save_selected_data', array($this, 'save_selected_data_callback'));
    }

    /**
     * Enqueues scripts and styles for the admin side.
     * 
     * Enqueues Bootstrap CSS and JS, the feedback form handler JS, 
     * the feedback analysis handler JS, jQuery, DataTables and 
     * related libraries, and custom admin styles.
     * 
     * Localizes the form and analysis handler scripts with the AJAX url.
    */
    public function enqueue_custom_scripts() {
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3', true);
        
        wp_enqueue_script('form-plugin-script', plugin_dir_url(__FILE__) . '../../assets/js/feedback-form-handler.js', array('jquery'), null, true);
        wp_localize_script('form-plugin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_script('form-analysis-script', plugin_dir_url(__FILE__) . '../../assets/js/feedback-analysis-handler.js', array('jquery'), null, true);
        wp_localize_script('form-analysis-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        
        wp_enqueue_script('jquery');         
        wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), null, true);         
        wp_enqueue_script('datatables-buttons', 'https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js', array('datatables'), '2.3.2', true);        
        wp_enqueue_script('datatables-buttons-html5', 'https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js', array('datatables-buttons'), '2.3.2', true);        
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');       
        wp_enqueue_style('datatables-buttons-css', 'https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css');        
        wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), '3.10.1', true);
        
        wp_enqueue_style( 'custom-style', plugin_dir_url( __FILE__ ) . '../../assets/css/feedback-analysis.css');
    }
        
    /**    
     * 
     * This uses add_menu_page() to add a new top-level menu page
     * under the name "Feedback Form" and with the slug "feedback-form".      
    */
    public function add_feedback_form_menu() {
        add_menu_page(
            'Feedback Form',          // Page title
            'Feedback Form',          // Menu title
            'manage_options',         // Capability
            'feedback-form',          // Menu slug
            array($this, 'render_feedback_form'),  // Callback function
            'dashicons-feedback',     // Icon
            55                        // Position
        );
    }

    /**    
     * 
     * This uses add_menu_page() to add a new top-level menu page
     * under the name "Feedback Analysis" and with the slug "feedback-analysis".      
    */
    public function add_feedback_analysis_menu() {
        add_menu_page(
            'Feedback Form Analysis',    // Page title
            'Feedback Analysis',         // Menu title
            'manage_options',            // Capability
            'feedback-analysis',         // Menu slug
            array($this, 'render_feedback_analysis'),  // Callback function
            'dashicons-chart-pie',       // Icon
            56                           // Position
        );
    }    

    public function render_feedback_form() {
        include_once 'feedback-form.php';
        render_feedback_form();
    }

    public function render_feedback_analysis() {
        include_once 'feedback-analysis.php';
        render_feedback_analysis();
    }

    /**
        * Saves the selected category and options from the feedback form.
    */
    public function save_selected_data_callback() {
        global $wpdb;
        $inputCategory = isset($_POST['inputCategory']) ? $_POST['inputCategory'] : '';
        $selectedOptions = isset($_POST['selectedOptions']) ? $_POST['selectedOptions'] : '';
        $data = array(
            'input_category' => $inputCategory,
            'selected_options' => $selectedOptions
        );
        update_option('selected_data', json_encode($data)); // Encode the array to JSON
        echo "Input category and selected options saved successfully";
        wp_die();
    }
    
}


new My_Admin_Functions();