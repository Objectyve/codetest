
function AddressController($scope, Address) {

    var currentResource;
    var resetForm = function () {
        $scope.addMode = true;
        $scope.street = undefined;
        $scope.city = undefined;
        $scope.state = undefined;
        $scope.selectedIndex = undefined;
    }

    $scope.addresses = Address.query();
    $scope.addMode = true;

    $scope.add = function () {
        var key = {};
        var value = {street: $scope.street, city: $scope.city, state: $scope.state}

        Address.save(key, value, function (data) {
            $scope.addresses.push(data);
            resetForm();
        });
    };

    $scope.update = function () {
        var key = {id: currentResource.id};
        var value = {street: $scope.street, city: $scope.city, state: $scope.state}
        Address.save(key, value, function (data) {
            currentResource.street = data.street;
            currentResource.city = data.city;
            currentResource.state = data.state;
            currentResource.postalcode = data.postalcode;
            resetForm();
        });
    }

    $scope.refresh = function () {
        $scope.addresses = Address.query();
        resetForm();
    };

    $scope.deleteAddress = function (index, id) {
        Address.delete({id: id}, function () {
            $scope.addresses.splice(index, 1);
            resetForm();
        });
    };

    $scope.selectAddress = function (index) {
        currentResource = $scope.addresses[index];
        $scope.addMode = false;
        $scope.street = currentResource.street;
        $scope.city = currentResource.city;
        $scope.state = currentResource.state;
    }

    $scope.cancel = function () {
        resetForm();
    }
}