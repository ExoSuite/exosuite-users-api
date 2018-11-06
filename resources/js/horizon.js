import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import _ from 'lodash';

// Manually edit the horizon prefix
// Only prefix since the horizon URL is hardcoded in Vue components $http calls
const HORIZON_PREFIX = '/monitoring';

// Force router base URL in a vue instance that is using a router
const vueInit = Vue.prototype._init;
Vue.prototype._init = function(options = {}, ...args) {
    if (options.router) {
        options.router = new VueRouter(_.merge(options.router.options, {
            base: `${HORIZON_PREFIX}/horizon`
        }));
    }

    return vueInit.bind(this)(options, ...args);
};

// Force Axios to use use the horizon prefix in every relative call
const axiosCreate = axios.create;
axios.create = function(config = {}, ...args) {
    config.baseURL = `${HORIZON_PREFIX}/`;

    return axiosCreate.bind(this)(config, ...args);
};

// Import Laravel AdministratorServices original JS file.
// Overwrite /public/vendor/horizon/js/app.js with the current file from Mix configuration file)
require('./../../vendor/laravel/horizon/resources/assets/js/app');
