/**
 * Enable search bar display and hide on click functionality.
 */
const openSearch = document.querySelector('.search-title');
const searchContainer = document.querySelector('.search-form-container');
const searchBar = document.querySelector('.search-form');
const closeSearch = document.querySelector('.close-search');
const searchIcon = document.querySelector('.search-image-click');

if (closeSearch && openSearch && searchIcon) {
	closeSearch.addEventListener('click', () => {
		searchContainer.classList.toggle('display-none');
	});
	openSearch.addEventListener('click', () => {
		searchContainer.classList.remove('display-none');
		searchBar.style.display = 'inline';
		searchContainer.style.display = 'block';
	});
	searchIcon.addEventListener('click', () => {
		searchContainer.classList.remove('display-none');
		searchBar.style.display = 'inline';
		searchContainer.style.display = 'block';
	});
}
