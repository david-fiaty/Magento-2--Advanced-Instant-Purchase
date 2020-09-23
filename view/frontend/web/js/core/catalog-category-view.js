require([
    'jquery', 'domReady!'
], function($) {
        $(document).on('click','.aip-button',function() {
            var pid = $(this).parent().find('input[name^="aip-pid"]').val();
            console.log(pid);
        });
});