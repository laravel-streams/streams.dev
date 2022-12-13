import './bootstrap';
import '../scss/app.scss';

import Alpine from 'alpinejs';
 
window.Alpine = Alpine;
 
Alpine.start();

import { Client } from '@laravel-streams/api-client';

window.client = new Client({
    baseURL: 'http://127.0.0.1:8000/api',
});
