/**
 * File to handle the visibility of mobile navigation menu and add functionality to the slider in archive rt-movie.
 */
const openNavMenu = document.querySelector('.mobile-hamburger-menu');
const closeNavMenu = document.querySelector('.mobile-menu-close');
const mobileNav = document.querySelector('.mobile-nav');
if (openNavMenu && closeNavMenu) {
	openNavMenu.addEventListener('click', () => {
		mobileNav.classList.toggle('display-none');
		closeNavMenu.style.display = 'block';
		openNavMenu.style.display = 'none';
	});
	closeNavMenu.addEventListener('click', () => {
		mobileNav.classList.toggle('display-none');
		closeNavMenu.style.display = 'none';
		openNavMenu.style.display = 'block';
	});
}

let slideIndex = 0;
slideshow();

/**
 * Function to create a slideshow from the existing divs and make the slider automatic.
 */
function slideshow() {
	let i;
	const slides = document.getElementsByClassName('slide');
	const dots = document.getElementsByClassName('dot');
	if (typeof slides !== 'undefined' && typeof dots !== 'undefined') {
		for (i = 0; i < slides.length; i++) {
			slides[i].style.display = 'none';
		}
		slideIndex++;
		if (slideIndex > slides.length) {
			slideIndex = 1;
		}
		for (i = 0; i < dots.length; i++) {
			dots[i].className = dots[i].className.replace(' active', '');
		}
		slides[slideIndex - 1].style.display = 'block';
		dots[slideIndex - 1].className += ' active';
		// Set the time interval of the slider to 3s.
		setTimeout(slideshow, 3000);
	}
}
