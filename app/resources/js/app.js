import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// resources/js/app.js

import { autoUpdateSlotStatus } from './autoUpdateSlot';

autoUpdateSlotStatus(); // AJAXリクエストを1分ごとに実行