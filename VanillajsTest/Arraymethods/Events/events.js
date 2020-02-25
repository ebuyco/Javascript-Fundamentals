const butts = document.querySelector('.butts');
const coolButton = document.querySelector('.cool');


const handleClick = () => {
            console.log('🐛 IT GOT CLICKED!!!');
}

const hooray = () => console.log('HOORAY!!!!');


butts.addEventListener('click', function(){
        console.log('Im an anon!');
})

coolButton.addEventListener('click', hooray);

butts.removeEventListener('click', handleClick);

const buyButtons = document.querySelectorAll('button.buy');


const handleBuyButtonClick = (event) => {
        console.log('You clicked a button!')

        const button = event.target;
        console.log(button.textContent);
        console.log(parseFloat(event.target.dataset.price));

        console.log(event.target);
        console.log(event.currentTarget);
        console.log(event.target === event.currentTarget);
        event.stopPropagation();

}

buyButtons.forEach(function(buyButton) {
        buyButton.addEventListener('click', handleBuyButtonClick);
})

window.addEventListener(

    'click',
    function(event){
            console.log('YOU CLICKED THE WINDOW');
            console.log(event.target);
            console.log(event.type);
            console.log(event.bubbles);
            event.stopPropagation();
    },
    { capture: true }

)

const photoEl = document.querySelector('.photo');


photoEl.addEventListener('mouseenter', e => {
            console.log(e.currentTarget);
            console.log(this);
})