const Slider = (slider) => {
    if(!(slider instanceof Element)){
        throw new Error('NO slider passed in');
    }

    let prev;
    let current;
    let next;

    const slides = slider.querySelector('.slides');
    const prevButton = slider.querySelector('.goToPrev');
    const nextButton = slider.querySelector('.goToNext');

    const startSlider = () => {
        current = slider.querySelector('.current') || slides.firstElementChild;
        prev = current.previousElementSibling || slides.lastElementChild;
        next = current.nextElementSibling || slides.firstElementChild;
        console.log( {current, prev, next});
    }

    const applyClasses = () => {
        current.classList.add('current');
        prev.classList.add('prev');
        next.classList.add('next');
    }

    const move = (direction) => {
        const classesTORemove = ['prev', 'current', 'next'];
        prev.classList.remove(...classesTORemove);
        current.classList.remove(...classesTORemove);
        next.classList.remove(...classesTORemove);
        if( direction === 'back' ) {
            [prev, current, next] = [
                prev.previousElementSibling || slides.lastElementChild,
                prev,
                current
            ];
        } else {
            [prev, current, next] = [
                current,
                next,
                next.nextElementSibling || slides.firstElementChild,
            ];
        }

        applyClasses();

    }

    startSlider();
    applyClasses();

    prevButton.addEventListener('click', () => move('back'));
    nextButton.addEventListener('click', move);
}

const mySlider = Slider(document.querySelector('.slider'));
const dogSlider = Slider(document.querySelector('.dog-slider'));

