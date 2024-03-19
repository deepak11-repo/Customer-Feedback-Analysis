document.addEventListener("DOMContentLoaded", function() {
    var customPopup = document.getElementById("custom-popup");
    var submitBtn = document.getElementById("submit-btn");
    var closeButton = document.querySelector(".close-btn");
    if (customPopup) {
        customPopup.style.display = "block";
    }
    if (closeButton) {
        closeButton.addEventListener("click", function() {
            if (customPopup) {
                customPopup.style.display = "none";
            }
        });
    }
    
    if(submitBtn) {
        submitBtn.addEventListener("click", function() {
            if(customPopup) {
                customPopup.style.display = "none";
            }
        });
    }
    
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: {
            action: "get_selected_data" 
        },                
        success: function(response) {
            try {
                var data = JSON.parse(response);
                var inputCategory = data.input_category;
                var optionsWithInputCategory = [];
        
                data.selected_options.forEach(function(option) {
                    var optionWithCategory = {
                        category: option.category,
                        type: option.type,
                        label: option.label,
                        input_category: inputCategory
                    };
                    optionsWithInputCategory.push(optionWithCategory);
                });        
                console.log(optionsWithInputCategory);
                showLabelsInPopup(optionsWithInputCategory);
            } catch (error) {
                console.error("Error parsing JSON response:", error);
            }
        },        
        error: function(xhr, status, error) {
            console.error("AJAX request error:", error);
        }
    });    
    
});

function showLabelsInPopup(optionsWithInputCategory) {
    var container = jQuery('.label-container');
    var formData = []; // Array to store form data

    optionsWithInputCategory.forEach(function(option, index) {
        var uniqueId = 'container_' + index;
        console.log(uniqueId);
        var div = jQuery('<div></div>');
        var label = option.label;
        var category = option.category;
        var type = option.type;
        var inputCategory = option.input_category;

        div.append('<label class="mt-2">' + label + '</label>');

        // Conditional check for appending input category
        if (category !== 'General') {
            switch (inputCategory) {
                case 'Stars':
                    var starContainer = jQuery(getRateContainerHTML(uniqueId));
                    div.append(starContainer);
                    starContainer.on('change', 'input', function() {
                        const rating = parseInt(jQuery(this).val());
                        const ratingResult = handleRating(rating.toString()); 
                        console.log("Rating Result:", ratingResult);
                        formData[index] = { label: label, category: category, type: type, ratingResult: ratingResult };
                    });
                    break;      
                case 'Numbers':
                    var numContainer = jQuery(getRatingContainerHTML(uniqueId));
                    div.append(numContainer);
                    numContainer.on('change', 'input', function() {
                        const rating = parseInt(jQuery(this).val());
                        const ratingResult = handleRating(rating.toString());
                        console.log("Rating Result:", ratingResult);
                        formData[index] = { label: label, category: category, type: type, ratingResult: ratingResult };
                    });
                    break;
                case 'Emojis':
                    const emojiContainer = jQuery(getEmojiContainerHTML(uniqueId));
                    div.append(emojiContainer);
                    emojiContainer.on('change', 'input', function() {
                        const rating = jQuery(this).val();
                        const ratingResult = handleRating(rating);
                        console.log("Rating Result:", ratingResult);
                        formData[index] = { label: label, category: category, type: type, ratingResult: ratingResult };
                    });
                    break;
                default:
                    break;
            }
        } else {
            var inputBox = jQuery('<input type="text" class="form-control mt-1 mb-1" placeholder="Type Here">');
            inputBox.on('input', function() {
                formData[index] = { label: label, category: category, type: type, review: jQuery(this).val() };
            });
            div.append(inputBox);
        }

        container.append(div);
    });    

    var button = jQuery('#submit-btn');
    button.on('click', function() {
        console.log(formData);
        sendDataToServer(formData);
        Toastify({
            text: "Form submitted successfully!",
            duration: 2000, 
            gravity: "top", 
            position: "right", 
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)", 
            stopOnFocus: true 
        }).showToast();
    });
}

function sendDataToServer(formData) {
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'submit_feedback',
            formData: formData
        },
        success: function(response) {
            console.log('Feedback submitted successfully:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function getRateContainerHTML(uniqueId) {
    return `
        <div class="rate" id="rateContainer_${uniqueId}">
            <input type="radio" id="star5_${uniqueId}" name="rate_${uniqueId}" value="5" />
            <label for="star5_${uniqueId}" title="text">5 stars</label>
            <input type="radio" id="star4_${uniqueId}" name="rate_${uniqueId}" value="4" />
            <label for="star4_${uniqueId}" title="text">4 stars</label>
            <input type="radio" id="star3_${uniqueId}" name="rate_${uniqueId}" value="3" />
            <label for="star3_${uniqueId}" title="text">3 stars</label>
            <input type="radio" id="star2_${uniqueId}" name="rate_${uniqueId}" value="2" />
            <label for="star2_${uniqueId}" title="text">2 stars</label>
            <input type="radio" id="star1_${uniqueId}" name="rate_${uniqueId}" value="1" />
            <label for="star1_${uniqueId}" title="text">1 star</label>
        </div>
    `;
}

function getRatingContainerHTML(uniqueId) {
    return `
        <div class="rating" id="ratingContainer_${uniqueId}">
            <input type="radio" id="rating5_${uniqueId}" name="rating_${uniqueId}" value="5">
            <label for="rating5_${uniqueId}">5</label>
            <input type="radio" id="rating4_${uniqueId}" name="rating_${uniqueId}" value="4">
            <label for="rating4_${uniqueId}">4</label>
            <input type="radio" id="rating3_${uniqueId}" name="rating_${uniqueId}" value="3">
            <label for="rating3_${uniqueId}">3</label>
            <input type="radio" id="rating2_${uniqueId}" name="rating_${uniqueId}" value="2">
            <label for="rating2_${uniqueId}">2</label>
            <input type="radio" id="rating1_${uniqueId}" name="rating_${uniqueId}" value="1">
            <label for="rating1_${uniqueId}">1</label>
        </div>
    `;
}

function getEmojiContainerHTML(uniqueId) {
    return `
        <div class="emoji-container" id="emojiContainer_${uniqueId}">
            <form id="emoji_${uniqueId}">
                <input type="radio" id="emoji_${uniqueId}_love" name="emoji_${uniqueId}" class="emoji-radio" value="unimpressed by">
                <label for="emoji_${uniqueId}_love" class="emoji-label">😞</label>
                
                <input type="radio" id="emoji_${uniqueId}_sad" name="emoji_${uniqueId}" class="emoji-radio" value="are confused about">
                <label for="emoji_${uniqueId}_sad" class="emoji-label">😕</label>
                
                <input type="radio" id="emoji_${uniqueId}_neutral" name="emoji_${uniqueId}" class="emoji-radio" value="are neutral about">
                <label for="emoji_${uniqueId}_neutral" class="emoji-label">😐</label>
                
                <input type="radio" id="emoji_${uniqueId}_happy" name="emoji_${uniqueId}" class="emoji-radio" value="had fun playing">
                <label for="emoji_${uniqueId}_happy" class="emoji-label">😊</label>
                
                <input type="radio" id="emoji_${uniqueId}_excited" name="emoji_${uniqueId}" class="emoji-radio" value="loved">
                <label for="emoji_${uniqueId}_excited" class="emoji-label">😍</label>
            </form>
        </div>
    `;
}
function handleRating(value) {
    switch (value) {
        case "1":
        case "unimpressed by":
            return "Extremely Dissatisfied";
        case "2":
        case "are confused about":
            return "Dissatisfied";
        case "3":
        case "are neutral about":
            return "Neutral";
        case "4":
        case "had fun playing":
            return "Satisfied";
        case "5":
        case "loved":
            return "Extremely Satisfied";
        default:
            return "Invalid rating";
    }
}
