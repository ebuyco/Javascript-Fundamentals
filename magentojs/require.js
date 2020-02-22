define(["jquery"], function($) {
    $('ol').on('click', 'li', function() {
        console.log(this);
    })
})