window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.vue = require('vue');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

let pluralization = require('./plurarization');
let fitness = require('./fitness');

window.vue.prototype.pluralize = function(revert,count)
{
    //alert(revert+' coun is '+count);
    if(count < 2)
        return pluralization['getSingular'](revert);
    return revert;
}

window.vue.prototype.inArray= function(item,keyit,group){

    for(var prop in item) {
        if(prop == keyit){
            var value = item[prop];

            return fitness.itemInArray(value,group);
        }
    }

}

window.vue.prototype.userfunc=function(...params){
    if(typeof params[0] === 'string'){
        return fitness[params[0]](params[1])
    }
}

window.vue.prototype.findLocation = function(){
    var location = fitness.findGeoPosition();
    //alert('attempted to find location');
}

window.vue.prototype.localCache = function (...params) {
    if(typeof params[0] === 'string'){
        if(params[0]=='set'){
           return fitness.localCache[params[0]](params[1],params[2]);
        }
        return fitness.localCache[params[0]](params[1]);
    }
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
