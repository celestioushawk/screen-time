/**
 * Enable the div progress bar to move forward as we scroll accross movies in the archive section.
 */
const sliderOne = document.querySelector('.movie-grid-one');

const progBarOne = document.querySelector('.inner-progress-bar-one');

if (sliderOne && progBarOne) {
	progBarOne.style.width = '10%';
	sliderOne.addEventListener('scroll', () => {
		progBarOne.style.width = `${getScrollPercentage(sliderOne)}%`;
	});
}

const sliderTwo = document.querySelector('.movie-grid-two');

const progBarTwo = document.querySelector('.inner-progress-bar-two');

if (sliderTwo && progBarTwo) {
	progBarTwo.style.width = '10%';
	sliderTwo.addEventListener('scroll', () => {
		progBarTwo.style.width = `${getScrollPercentage(sliderTwo)}%`;
	});
}
/**
 * Get the scroll percentage of the parent div using the clientWidth and scrollWidth.
 *
 * @param {Element} slider
 */
function getScrollPercentage(slider) {
	return (
		(slider.scrollLeft / (slider.scrollWidth - slider.clientWidth)) * 100
	);
}
