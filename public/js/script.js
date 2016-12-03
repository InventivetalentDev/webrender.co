var apiBaseUrl = "https://webrender-api.inventivetalent.org";

var webrenderApp = angular.module("webrenderApp", ["ngRoute", "ui.bootstrap", "angular-loading-bar"]);

webrenderApp.controller("renderController", ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
    /* +Alerts */
    $scope.alerts = [];
    $scope.addAlert = function (msg, type, timeout) {
        var newAlert = {type: type, msg: msg, timeout: timeout};
        $scope.safeApply(function () {
            $scope.alerts.push(newAlert);
        });
        return {
            alert: newAlert,
            close: function () {
                var index = $scope.alerts.indexOf(newAlert);
                if (index !== -1) {
                    $scope.closeAlert(index);
                }
            }
        }
    };
    $scope.closeAlert = function (index) {
        $scope.safeApply(function () {
            $scope.alerts.splice(index, 1);
        });
    };
    $scope.clearAlerts = function () {
        $scope.safeApply(function () {
            $scope.alerts.splice(0, $scope.alerts.length);
        });
    };
    /* -Alerts */

    $scope.renderStarted = false;
    $scope.renderFinished = false;

    $scope.renderUrl = undefined;
    $scope.renderFormat = "png";
    $scope.renderOptions = [];

    $scope.renderedImage = "";
    $scope.renderedData = "";

    $scope.renderDirectLink = undefined;
    $scope.renderDirectLinkVisible = false;

    $scope.initRenderOptions = function () {
        $http({
            url: apiBaseUrl + "/getoptions",
            method: "GET"
        }).then(function (response) {
            $scope.safeApply(function () {
                $scope.renderOptions = response.data;
                $.each($scope.renderOptions, function (index, option) {
                    if (option.defaultValue) {
                        // if (option.defaultValue === true) {
                        //     option.defaultValue = "true";
                        // }
                        // if (option.defaultValue === false) {
                        //     option.defaultValue = "false";
                        // }

                        option.value = option.defaultValue;
                    }
                });
                console.log($scope.renderOptions);
            });
        });
    };
    $scope.startRender = function () {
        var startAlert = $scope.addAlert("Rendering Website...", "info");
        $scope.renderStarted = true;

        $http({
            url: apiBaseUrl + "/render",
            method: "POST",
            data: $.param({
                url: $scope.renderUrl,
                format: $scope.renderFormat,
                options: (function () {
                    var options = {};
                    $.each($scope.renderOptions, function (index, option) {
                        // if (option.value) {
                        //     if (option.value === "true")
                        //         option.value = true;
                        //     if (option.value === "false")
                        //         option.value = false;
                        options[option.key] = option.value;
                        // }
                    });
                    return JSON.stringify(options);
                })()
            }),
            headers: {"Content-Type": "application/x-www-form-urlencoded"}
        }).then(function (response) {
            var data = response.data;
            console.info(data);

            if (data.error) {
                startAlert.close();
                $scope.addAlert(data.error, "danger", 10000);
                $scope.renderStarted = false;
            } else {
                startAlert.close();
                $scope.addAlert("Render successful!", "success", 2500);
                $scope.renderFinished = true;

                $scope.renderedData = data;
                $scope.renderedDataString = JSON.stringify(data, null, 2);
                $scope.renderedImage = data.image;
            }
        }, function (response) {
            var data = response.data;
            console.info(data);

            if (data.error) {
                startAlert.close();
                $scope.addAlert(data.error, "danger", 10000);
                $scope.renderStarted = false;
            } else {
                $scope.addAlert("Unknown Error", "danger", 10000);
            }
        })
    };
    $scope.showDirectLink = function () {
        $scope.renderDirectLink = "https://webrender-api.inventivetalent.org/render"
            + "?url=" + $scope.renderUrl
            + "&format=" + $scope.renderFormat
            + "&options=" + JSON.stringify($scope.renderOptions)
            + "&redirect=true";
        $scope.renderDirectLinkVisible = true;
    };


    $scope.safeApply = function (fun) {
        // if (!$scope.$$phase) {
        //     $scope.$apply(fun);
        // }
        $timeout(fun);
    };
}]);