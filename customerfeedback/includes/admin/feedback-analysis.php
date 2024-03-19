<?php

/**
 * Renders the feedback analysis page.
 * 
 * Displays a header, then checks if a feedback type was specified. 
 * If so, displays metrics for that feedback type.
 * If not, displays default content.
 */
function render_feedback_analysis() {
    ?>
    <div class="container p-3 mt-4 bg-dark rounded text-white">
        <h4>Feeback Form Analysis</h4>
    </div>    
    <?php
        if(isset($_REQUEST['type'])) {
            display_metric_feedback($_REQUEST['type']);
        } 
        else {
            display_default_content();
        }
}   

/**
 * Displays default content on the feedback analysis page.
 * 
 * Shows metrics summary for 'Metric' category feedback types in a table. 
 * Shows general feedback in a table for 'General' category.
 * Provides a dropdown to toggle between the two tables.
 */
function display_default_content() {
    global $wpdb;
    ?>
    
    <div class="container mt-4" style="padding-left: 55px;">
        <div class="row">
            <div class="col-md-2">
                <label for="categorySelect">Select category:</label>                
            </div>
            <div class="col-md-2">
                <select class="form-control" id="categorySelect" onchange="toggleDiv()">
                    <option value="Metric">Metric</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>
    </div>

    <div class="container mt-4" id="metric" style="width: 92%; display: block;">
        <?php
            $query = "SELECT DISTINCT category, type FROM {$wpdb->prefix}customer_feedback WHERE category = 'Metric' ORDER BY type";
            $results = $wpdb->get_results($query);
        ?>    
        <table id="metric-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Extremely Dissatisfied 😞</th>
                    <th>Dissatisfied 😕</th>
                    <th>Neutral 😐</th>
                    <th>Satisfied 🙂</th>
                    <th>Extremely Satisfied 😊</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $row) {
                    $ratings_query = $wpdb->prepare(
                        "SELECT ratings FROM {$wpdb->prefix}customer_feedback WHERE category = %s AND type = %s",
                        $row->category,
                        $row->type
                    );
                    $ratings_results = $wpdb->get_results($ratings_query);
                    $rating_counts = array(
                        'Extremely Dissatisfied' => 0,
                        'Dissatisfied' => 0,
                        'Neutral' => 0,
                        'Satisfied' => 0,
                        'Extremely Satisfied' => 0
                    );
                    foreach ($ratings_results as $rating_row) {
                        $rating_counts[$rating_row->ratings]++;
                    }                
                    echo "<tr>";
                    echo "<td>{$row->category}</td>"; 
                    echo "<td><a href='" . esc_url(admin_url('admin.php?page=feedback-analysis&type=' . $row->type)) . "' class='type-link'>{$row->type}</a></td>"; // Link added to the type
                    echo "<td>{$rating_counts['Extremely Dissatisfied']}</td>";
                    echo "<td>{$rating_counts['Dissatisfied']}</td>";
                    echo "<td>{$rating_counts['Neutral']}</td>";
                    echo "<td>{$rating_counts['Satisfied']}</td>";
                    echo "<td>{$rating_counts['Extremely Satisfied']}</td>";                                
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="container mt-4" id="general" style="width: 92%; display: none;">
        <?php
            $query = "SELECT category, type, reviews FROM {$wpdb->prefix}customer_feedback WHERE category = 'General'";
            $results = $wpdb->get_results($query);
        ?>
        <table id="general-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Reviews</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($results) {
                    foreach ($results as $row) {
                        echo "<tr>";
                        echo "<td>{$row->category}</td>";
                        echo "<td>{$row->type}</td>";
                        echo "<td>{$row->reviews}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>       
    </div>

    <?php
}

/**
 * Displays metric feedback data and chart for the given feedback type.
 * 
 * Queries the database to get all distinct labels for the given feedback type. 
 * Counts the different rating values for each label. 
 * Calculates percentages for each rating.
 * Outputs a table with the counts per label.
 * Outputs a pie chart showing percentages of each rating.
 */
function display_metric_feedback($type) {
    ?>    
    <div class="container mt-4" style="width: 90%; position: relative;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=feedback-analysis')); ?>" class="position-absolute top-0 end-0" id="back-btn">Back</a>
    </div>

    <div class="container mt-4" style="width: 92%; padding-top: 55px;">
        <?php
            global $wpdb;
            $query = $wpdb->prepare(
                "SELECT DISTINCT label 
                FROM {$wpdb->prefix}customer_feedback 
                WHERE category = 'Metric' AND type = %s 
                ORDER BY label",
                $type
            );
            $results = $wpdb->get_results($query);

            $rating_counts = array(
                'Extremely Dissatisfied' => 0,
                'Dissatisfied' => 0,
                'Neutral' => 0,
                'Satisfied' => 0,
                'Extremely Satisfied' => 0
            );

            // Count each rating category
            foreach ($results as $row) {
                $ratings_query = $wpdb->prepare(
                    "SELECT ratings 
                    FROM {$wpdb->prefix}customer_feedback 
                    WHERE category = 'Metric' AND type = %s AND label = %s",
                    $type,
                    $row->label
                );
                $ratings_results = $wpdb->get_results($ratings_query);
                foreach ($ratings_results as $rating_row) {
                    $rating_counts[$rating_row->ratings]++;
                }
            }

            // Calculate total count of all ratings
            $total_count = array_sum($rating_counts);

            // Calculate the percentage of each rating category
            $percentage_counts = array();
            foreach ($rating_counts as $rating => $count) {
                $percentage_counts[$rating] = ($count / $total_count) * 100;
            }
        ?>

        <table id="metric-table">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Extremely Dissatisfied</th>
                    <th>Dissatisfied</th>
                    <th>Neutral</th>
                    <th>Satisfied</th>
                    <th>Extremely Satisfied</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($results as $row) {
                    echo "<tr>";
                    echo "<td>{$row->label}</td>"; 
                    echo "<td>{$rating_counts['Extremely Dissatisfied']}</td>";
                    echo "<td>{$rating_counts['Dissatisfied']}</td>";
                    echo "<td>{$rating_counts['Neutral']}</td>";
                    echo "<td>{$rating_counts['Satisfied']}</td>";
                    echo "<td>{$rating_counts['Extremely Satisfied']}</td>";                                
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
            
        <div id="container" style="height: 400px;"></div>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script>
            var ratingPercentages = <?php echo json_encode($percentage_counts); ?>;
            Highcharts.chart('container', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Distribution of Ratings'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' 
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f}%' 
                        }
                    }
                },
                series: [{
                    name: 'Rating',
                    data: Object.entries(ratingPercentages).map(([key, value]) => ({ name: key, y: value })) 
                }]
            });
        </script>
    </div>

    <?php
}

?>
