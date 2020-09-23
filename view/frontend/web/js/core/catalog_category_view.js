require([
    'jquery', 'domReady!'
], function($) {
    $(document).ready(function() {
        $(document).on('click','.aip-button',function() {
            var pid = $(this).find('input[name^="aip-pid"]');
            console.log(pid);
        });
    });
});