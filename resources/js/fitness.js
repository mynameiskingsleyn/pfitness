let localCache = {
        remove: function (key) {
            localStorage.removeItem(key);
        },
        exist: function (key) {
            return localStorage.getItem(key) !== null;
        },
        get: function (key) {
            return localStorage.getItem(key);
        },
        set: function (key, value) {
            localStorage.setItem(key, value);
            return true;
        }
    };



module.exports = {

    itemInArray(item, theArray){
        //console.log('item to be compared is '+item);
        //console.log(theArray);
        if(item && theArray){
            item = item.toLowerCase();
            var result = theArray.indexOf(item);
            if(result >= 0){
                return true
            }
            return false;
        }
        return false;
    },

    findGeoPosition() {
        //$('.zipcodePopup_errorlocationInfo span').html('');
        var currentLocation = {}
        if (navigator.geolocation) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.showPosition, this.showError);
            } else {
                this.dd("Geolocation is not supported by this browser.");
                // alert('Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.');
                var title = 'Alert!';
                var contents = 'Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.';
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
            }
        }
        //$('#selectedZipCodePopup_currentLocation').removeClass('disabled');
        //$('#selectedZipCodePopup_currentLocation').html('<i class="fa fa-map-marker" aria-hidden="true"></i>Use current location<i class="fa fa-angle-right"></i>');

    },

    showPosition: function(position) {
        var location = [];
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        localCache.set('latitude', latitude);
        localCache.set('longitude', longitude);
        //ajax.promise('find_zip_by_cord', 'post', JSON.stringify({ latitude: latitude, longitude: longitude }));
        return true;

    },

    showError: function(error) {
        var err = ' so please enter Zip Code to search the latest inventory in your area.';
        switch (error.code) {
            case error.PERMISSION_DENIED:
                this.dd("You denied the request for Geolocation.");
                //alert('Location is disabled. Please enter Zip Code to search the latest inventory in your area.');
                var title = 'Alert!';
                var contents = 'Location is disabled. Please enter Zip Code to search the latest inventory in your area.';
                //AlertMessage(title, contents);
               // $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.POSITION_UNAVAILABLE:
                this.dd("Location information is unavailable.");
                var title = 'Alert!';
                var contents = 'Location information is unavailable ' + err;
                // alert('Location information is unavailable '+err);
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.TIMEOUT:
                this.dd("The request to get user location timed out.");
                //alert('The request to get user location timed out '+err);
                title = 'Alert!';
                contents = 'The request to get user location timed out ' + err;
                //AlertMessage(title, contents);
                $('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
            case error.UNKNOWN_ERROR:
                this.dd("An unknown error occurred.");
                //  alert('An unknown error occurred '+err);
                var title = 'Alert!';
                var contents = 'An unknown error occurred ' + err;
                //AlertMessage(title, contents);
                //$('.zipcodePopup_errorlocationInfo span').html(contents);
                //lpage.landingRequest();
                break;
        }
    },

    dd: function(str) {
        console.log(str);
    },



}