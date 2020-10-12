require([
    'jquery', 'aip', 'domReady!'
], function($, Aip) {
    $(document).ready(function() {

        // Set the button click event
        $('.aip-button').on('click touch', function(e) {
            Aip.handleButtonClick(e);
        }); 

        // Set the purchase button state
        $('.aip-button').prop(
            'disabled',
            !window.advancedInstantPurchase.guest.click_event
        );
    });
});


/**
 * Set the button click event.
 */
function setButtonState(elt) {

}


/**
 */
function setButtonState(elt) {

}