import 'tippy.js/dist/tippy.css'
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.appName = import.meta.env.VITE_APP_NAME || "Omdat we... reizen";
