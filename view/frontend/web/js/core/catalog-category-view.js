require([
    'jquery', 'domReady!'
], function($) {
        $(document).on('click','.aip-button',function(e) {

            console.log($(e.currentTarget).attr('id'));

        });
});