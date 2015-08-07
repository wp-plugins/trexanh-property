/**
 * directive to make input focus bases on condition.
 * 
 * Usage:
 * <input focus='model'> 
 * If model == true then input get focus
 */
angular.module('Directives', [])
    .directive('focus', function ($timeout, $parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                scope.$watch(attrs.focus, function (newValue, oldValue) {
                    if (newValue) {
                        $timeout(function () {
                            element[0].focus();
                        }, 0);
                    }
                });
            }
        };
    });