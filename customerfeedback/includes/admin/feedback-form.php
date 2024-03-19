<?php
function render_feedback_form() {   
    ?>

    <div class="container mx-auto p-3 mt-5 p-3 bg-light position-relative" style="width: 90%;">
        <h5>General Setting</h5>
        <div class="row pt-3">
            <div class="col-md-3">
                <label for="inputCategory" class="form-label">Select Input Option Category</label>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="inputCategory" name="inputCategory">
                    <option>Select Option</option>
                    <option value="Numbers">Numbers</option>
                    <option value="Stars">Stars</option>
                    <option value="Emojis">Emojis</option>
                </select>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-3 mt-4" style="width: 90%;">
        <p>Please choose the set of questions to be displayed to gather insights from the feedback form.</p>
        <div class="table-container">
            <table class="table table-light table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Category</th>
                        <th scope="col">Type</th>
                        <th scope="col">Label</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php
                        $categories = array(
                            'General' => array('Reason', 'Suggestion'),
                            'Metric' => array('NPS', 'CSAT', 'CES')
                        );

                        // Array of questions
                        $questions = array(
                            'NPS' => 'How likely are you to recommend our website to a friend or colleague?',
                            'CSAT' => 'Please rate your overall satisfaction with your experience on our website',
                            'CES' => 'How easy was it for you to complete your purchase on our website?',
                            'Reason' => 'What was the primary reason behind your recent experience with our website?',
                            'Suggestion' => 'Do you have any suggestions for how we could improve it in the future?'
                        );

                        foreach ($categories as $category => $types) {
                            foreach ($types as $type) {
                                echo "<tr>";
                                echo "<td><input type='checkbox'></td>";
                                echo "<td>$category</td>";
                                echo "<td>$type</td>";
                                // Retrieve the question from the questions array
                                $label = isset($questions[$type]) ? $questions[$type] : 'Label';
                                echo "<td>$label</td>";
                                echo "</tr>";
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>        
    </div>

    <div class="container mx-auto p-3 mt-3 position-relative" style="width: 90%;">
        <div class="p-3 position-absolute bottom-0 end-0">
            <button id="saveOptionsButton" class="btn btn-primary">Save &#x1F4BE;</button>
        </div>
    </div>

    <?php

}
