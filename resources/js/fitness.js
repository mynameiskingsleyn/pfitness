let _localCache = {
        remove: function (key) {
            localStorage.removeItem(key);
        },
        exist: function (key) {
            return localStorage.getItem(key) !== null;
        },
        get: function (key) {
            //alert('get called');
            return localStorage.getItem(key);
        },
        set: function (key, value) {
            localStorage.setItem(key, value);
            return true;
        }
    };

var _itemInArray = function(item, theArray){
    if(item && theArray){
        var result = theArray.indexOf(item);
        if(result >= 0){
            return true
        }
        return false;
    }
    return false;
};

var _findGeoPosition = function() {
    //alert('fitness find location');
    //$('.zipcodePopup_errorlocationInfo span').html('');
    var currentLocation = {}
    if (navigator.geolocation) {
        //alert('in navy');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.showPosition, this.showError);
        } else {
            module.exoprts.dd("Geolocation is not supported by this browser.");
            // alert('Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.');
            var title = 'Alert!';
            var contents = 'Geolocation is not supported by this browser so please enter Zip Code to search the latest inventory in your area.';
            //AlertMessage(title, contents);
            alert(contents);
            //lpage.landingRequest();
        }
    }
    //$('#selectedZipCodePopup_currentLocation').removeClass('disabled');
    //$('#selectedZipCodePopup_currentLocation').html('<i class="fa fa-map-marker" aria-hidden="true"></i>Use current location<i class="fa fa-angle-right"></i>');

};

var _dd = function(str){
    alert(str);
    console.log(str);
};

var _showPosition = function(position) {

    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    var location = {'latitude':latitude, 'longitude':longitude};
    alert(latitude);
    //localCache.set('latitude', latitude);
    //localCache.set('longitude', longitude);
    //ajax.promise('find_zip_by_cord', 'post', JSON.stringify({ latitude: latitude, longitude: longitude }));
    return true;

};

var _showError = function(error) {
    var err = ' so please enter Zip Code to search the latest inventory in your area.';
    switch (error.code) {
        case error.PERMISSION_DENIED:
            //this.dd("You denied the request for Geolocation.");
            module.exports.dd("You denied the request for Geolocation.");
            //alert('Location is disabled. Please enter Zip Code to search the latest inventory in your area.');
            var title = 'Alert!';
            var contents = 'Location is disabled. Please enter Zip Code to search the latest inventory in your area.';
            //AlertMessage(title, contents);

            // $('.zipcodePopup_errorlocationInfo span').html(contents);
            //lpage.landingRequest();
            break;
        case error.POSITION_UNAVAILABLE:
            module.exports.dd("Geo Location information is unavailable.");
            var title = 'Alert!';
            var contents = 'Location information is unavailable ' + err;
            // alert('Location information is unavailable '+err);
            //AlertMessage(title, contents);
            //lpage.landingRequest();
            break;
        case error.TIMEOUT:
            //module.exports.dd("The request to get user location timed out.");
            //alert('The request to get user location timed out '+err);
            title = 'Alert!';
            contents = 'The request to get user location timed out ' + err;
            //AlertMessage(title, contents);
            //$('.zipcodePopup_errorlocationInfo span').html(contents);
            //lpage.landingRequest();
            break;
        case error.UNKNOWN_ERROR:
            this.dd("An unknown error occurred. while trying to fetch location");
            //  alert('An unknown error occurred '+err);
            var title = 'Alert!';
            var contents = 'An unknown error occurred ' + err;
            break;
    }

    var report = {'title':title,'content':contents};
    _localCache.set('location_error', report);
    _localCache.set('geo_location_available',false);
    //alert (_localCache.get('location_error'));
};

var _AlertMessage= function(title, contents) {
    $.alert({
        buttons: {
            OK: {
                btnClass: 'alert-button',
                action: function() {}
            }
        },
        title: '<span style="font-size: 18px !important; font-family:Helvetica Neue,Helvetica,Arial,sans-serif;">' + title + '</span>',
        boxWidth: '35%',
        backgroundDismiss: false,
        bgOpacity: .1,
        useBootstrap: false,
        content: contents,
        draggable: false,
    });
}



module.exports = {

    itemInArray: _itemInArray,
    AlertMessage: _AlertMessage,
    dd: _dd,
    showError: _showError,
    showPosition: _showPosition,
    itemInArray: _itemInArray,
    findGeoPosition:_findGeoPosition,
    localCache: _localCache


}