document.addEventListener("DOMContentLoaded", function() {
    const items = document.querySelectorAll('ol > li');

    items.forEach(item => item.addEventListener('click', function(){
            console.log(this);
    }));
});