import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// CSRF setup: read token from meta and configure axios defaults
const csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfMeta) {
	const token = csrfMeta.getAttribute('content');
	if (token) {
		window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
	}
}
// Align with Laravel's default XSRF cookie/header
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
// Ensure cookies are sent on same-origin requests
window.axios.defaults.withCredentials = true;
