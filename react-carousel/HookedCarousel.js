// upcoming implementation follows this medium article of @ilonacodes
// https://medium.com/@ilonacodes/simple-image-carousel-with-react-5e20933001bf
// only main difference of this implementation: uses React hooks

import React, { useState } from 'react';

const HookedCarousel = () => {
  const [ images, setImages ] = useState([
    'https://via.placeholder.com/200x150?text=1',
    'https://via.placeholder.com/200x150?text=2',
    'https://via.placeholder.com/200x150?text=3',
    'https://via.placeholder.com/200x150?text=4',
    'https://via.placeholder.com/200x150?text=5',
    'https://via.placeholder.com/200x150?text=6',
    'https://via.placeholder.com/200x150?text=7',
    'https://via.placeholder.com/200x150?text=8',
    'https://via.placeholder.com/200x150?text=9',
    'https://via.placeholder.com/200x150?text=10',
    'https://via.placeholder.com/200x150?text=11',
    'https://via.placeholder.com/200x150?text=12',
    'https://via.placeholder.com/200x150?text=13',
    'https://via.placeholder.com/200x150?text=14',
    'https://via.placeholder.com/200x150?text=15',
    'https://via.placeholder.com/200x150?text=16',
    'https://via.placeholder.com/200x150?text=17',
    'https://via.placeholder.com/200x150?text=18',
  ]);

  const [ currentImageIdx, setCurrentImagIdx ] = useState(0);

  const prevSlide = () => {
    // find out whether currentImageIdx eqals 0 and thus user reached beginning of carousel
    const resetToVeryBack = currentImageIdx === 0;

    const index = resetToVeryBack ? images.length - 1 : currentImageIdx - 1;

    // assign the logical index to current image index that will be used in render method
    setCurrentImagIdx(index);
  };

  const nextSlide = () => {
    // check if we need to start over from the first index
    const resetIndex = currentImageIdx === images.length - 1;

    const index = resetIndex ? 0 : currentImageIdx + 1;

    // assign the logical index to current image index that will be used in render method
    setCurrentImagIdx(index);
  }

  // create a new array with 5 elements from the source images
  const activeImageSourcesFromState = images.slice(currentImageIdx, currentImageIdx + 5);

  // check the length of the new array (it’s less than 5 when index is at the end of the imagge sources array)
  const imageSourcesToDisplay = activeImageSourcesFromState.length < 5
    // if the imageSourcesToDisplay's length is lower than 5 images than append missing images from the beginning of the original array
    ? [...activeImageSourcesFromState, ...images.slice(0, 5 - activeImageSourcesFromState.length) ]
    : activeImageSourcesFromState;

  return (
    <div>
      <button onClick={prevSlide}>Prev</button>
      {/* render images */}
      {imageSourcesToDisplay.map((image, index) =>
        <img key={index} src={image} alt="" style={{ maxWidth: '15%' }} />
      )}
      <button onClick={nextSlide}>Next</button>
    </div>
  );
};

export default HookedCarousel