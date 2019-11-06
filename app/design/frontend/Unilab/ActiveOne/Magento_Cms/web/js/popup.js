require(
    ['jquery'],
    function($) {
        // modal({
        //     autoOpen: true,
        //     responsive: true,
        //     clickableOverlay: false,
        //     modalClass: 'modal-custom',
        //     title: 'Popup'
        // }, $("#popup-content"));
        $(function(){
            $("#loginModal").modal("show");
        })
    }
);