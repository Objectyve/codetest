
angular.module('AddressService', ['ngResource']).factory('Address', ['$resource', function ($resource) {
    return $resource('/app/address/resource/:id');
}]);