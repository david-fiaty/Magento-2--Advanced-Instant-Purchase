require([
    'jquery', 'domReady!'
], function($) {
        $(document).on('click','.aip-button',function(e) {

            console.log($(e.currentTarget).attr('id'));
            //var pid = $(this).parent().find('input[name^="aip-pid"]').val();
            //console.log(pid);
        });
});