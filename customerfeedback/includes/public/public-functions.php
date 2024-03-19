<?php
class Public_Functions {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts_and_styles'));
        add_action('woocommerce_thankyou', array($this, 'custom_checkout_popup'));
        add_action('wp_ajax_get_selected_data', array($this, 'get_selected_data_callback'));
        add_action('wp_ajax_nopriv_get_selected_data', array($this, 'get_selected_data_callback'));
        add_action('wp_ajax_submit_feedback', array($this, 'submit_feedback_callback'));
        add_action('wp_ajax_nopriv_submit_feedback', array($this, 'submit_feedback_callback'));
    }

    /**
     * Enqueues scripts and styles for the public side of the site.
     * 
     * Enqueues Bootstrap CSS and JS, the custom JS script, Toastify CSS and JS, 
     * and the custom CSS stylesheet. 
     * 
     * The custom JS script is dependent on jQuery and passes an object with the AJAX url.
     * Toastify JS depends on its own CSS.
     * The custom CSS depends on nothing.
     */
    public function enqueue_public_scripts_and_styles() {
        
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3', true);    
        
        wp_enqueue_script('my-script', plugin_dir_url(__FILE__) . '../../assets/js/public-scripts.js', array('jquery'), '1.0', true);
        wp_localize_script('my-script', 'ajax_object', array('ajax_url' => site_url('wp-admin/admin-ajax.php')));    
        
        wp_enqueue_style('toastify-css', 'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css');       
        wp_enqueue_script('toastify-js', 'https://cdn.jsdelivr.net/npm/toastify-js', array(), null, true);    
        
        wp_enqueue_style('my-style', plugin_dir_url(__FILE__) . '../../assets/css/public-styles.css');
    }

    /**
     * Outputs HTML for a custom popup displayed after checkout.
     * 
     * The popup contains a title, area to display dynamic content,
     * a submit button, and a close button. The content is rendered
     * client-side via JS by retrieving data from an AJAX request.
     */
    public function custom_checkout_popup() {
        ?>        
        <div id="custom-popup" class="custom-popup">
            <div class="popup-content">
                <span class="close-btn">&times;</span>
                <h2 class="popup-title">Give us Feedback</h2>
                <div class="label-container mt-3"></div>
                <button type="button" id="submit-btn" class="btn btn-dark mt-3">Submit</button>
            </div>
        </div>   
        <?php   
    }

    /**
     * Retrieves selected data via AJAX and returns JSON encoded response.
     * 
     * Gets selected data from 'selected_data' option, decodes JSON string, 
     * and returns object containing category and options.
     * Handles errors decoding JSON or if no data found.
     */
    public function get_selected_data_callback() {        
        $selectedData = get_option('selected_data');
        if ($selectedData) {
            $data = json_decode($selectedData, true);
            if ($data) {
                $inputCategory = isset($data['input_category']) ? $data['input_category'] : '';
                $selectedOptions = isset($data['selected_options']) ? $data['selected_options'] : array();
                $response = array(
                    'input_category' => $inputCategory,
                    'selected_options' => $selectedOptions
                );
                echo json_encode($response);
            } else {
                echo "Error: Failed to decode JSON string";
            }
        } else {
            echo "Error: No data found in selected_data option";
        }
        wp_die();
    }

    /**
     * Submits feedback data received from the frontend form. 
     *
     * Inserts the feedback data into the wp_customer_feedback table.
     * 
     * @since 1.0.0
    */
    public function submit_feedback_callback() {
        $form_data = isset($_POST['formData']) ? $_POST['formData'] : array();
        global $wpdb;
        $table_name = $wpdb->prefix . 'customer_feedback';
        foreach ($form_data as $data) {
            $label = $data['label'];
            $category = $data['category'];
            $type = $data['type'];
            $ratingResult = isset($data['ratingResult']) ? $data['ratingResult'] : 0;
            $reviews = isset($data['review']) ? $data['review'] : 0;    
            $wpdb->insert(
                $table_name,
                array(
                    'label' => $label,
                    'category' => $category,
                    'type' => $type,
                    'ratings' => $ratingResult,
                    'reviews' => $reviews
                )
            );
        }
        echo 'Feedback submitted successfully';
        wp_die();
    }
}

// Instantiate the class
new Public_Functions();
