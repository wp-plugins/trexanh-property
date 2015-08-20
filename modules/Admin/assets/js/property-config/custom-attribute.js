/**
 * Manage custom attribute tab.
 * Create, update, delete property attribute.
 */    
angular.module('App').controller('CustomAttributeConfigCtrl', ['$scope', function($scope) {
    $scope.propertyTypesConfig =  window.TrexanhProperty.propertyTypesConfig;

    $scope.coreAttributes =  window.TrexanhProperty.coreAttributes;

    $scope.$on('submitForm', function() {
        var form = angular.element("form");
        var input = form.find("[name=custom_attributes_config]");
        if ( ! input.length ) {
            input = angular.element("<input>")
                        .attr("type", "hidden")
                        .attr("name", "custom_attributes_config");
            form.append(input);
        }
        var data = angular.copy($scope.customAttributesConfig);
        input.val(angular.toJson(data));
        // tell server to delete in-use attributes
        var deletedInUseAttributesInput = form.find("[name=deleted_in_use_attributes]");
        if ( ! deletedInUseAttributesInput.length ) {
            deletedInUseAttributesInput = angular.element("<input>")
                        .attr("type", "hidden")
                        .attr("name", "deleted_in_use_attributes");
            form.append(deletedInUseAttributesInput);
        }
        deletedInUseAttributesInput.val(deletedInUseAttributes.join(","));
        // tell server to update in-use attributes
        var updatedInUseAttributesInput = form.find("[name=updated_in_use_attributes]");
        if ( ! updatedInUseAttributesInput.length ) {
            updatedInUseAttributesInput = angular.element("<input>")
                        .attr("type", "hidden")
                        .attr("name", "updated_in_use_attributes");
            form.append(updatedInUseAttributesInput);
        }
        updatedInUseAttributesInput.val(angular.toJson(updateInUseAttributes));
    });

    // custom attributes
    $scope.customAttributesConfig = window.TrexanhProperty.customAttributesConfig;
    $scope.attributeInputTypes = window.TrexanhProperty.attributeInputTypes;
    var attributeInputPrefix = "txp_property_";
    $scope.addCustomAttribute = function() {
        var newId = $scope.customAttributesConfig.length + 1;
        $scope.customAttributesConfig.push({
            id: 'txp_property_attribute_' + newId,
            input: '',
            title: 'Attribute ' + newId,
            new_title: 'Attribute ' + newId
        });
    };

    var getUsedByPropertyTypes = function(attributeId) {
        var propertyTypes = [];
        attributeId = attributeId.replace(attributeInputPrefix, "");
        for (var i in $scope.propertyTypesConfig) {
            if ($scope.propertyTypesConfig[i].attributes.indexOf(attributeId) !== -1) {
                propertyTypes.push($scope.propertyTypesConfig[i]);
            }
        }
        return propertyTypes;
    };

    var deletedInUseAttributes = [];
    var removeAttributeFromPropertyTypesConfig = function(attributeId) {
        attributeId = attributeId.replace(attributeInputPrefix, "");
        var usedByPropertyTypes = getUsedByPropertyTypes(attributeId);
        // remove attribute from property type config
        for (var i in usedByPropertyTypes) {
            usedByPropertyTypes[i].attributes.splice(usedByPropertyTypes[i].attributes.indexOf(attributeId), 1);
            for (var j in usedByPropertyTypes[i].groups) {
                var index = usedByPropertyTypes[i].groups[j].attributes.indexOf(attributeId);
                if (index !== -1) {
                    usedByPropertyTypes[i].groups[j].attributes.splice(index, 1);
                }
            }
        }
        deletedInUseAttributes.push(attributeId);
    };

    var updateInUseAttributes = [];
    var updateAttributeFromPropertyTypesConfig = function(customAttributeConfig) {
        var oldAttributeId = customAttributeConfig.id.replace(attributeInputPrefix, "");
        customAttributeConfig.id = customAttributeConfig.new_id;
        customAttributeConfig.title = customAttributeConfig.new_title;
        var newAttributeId = customAttributeConfig.new_id.replace(attributeInputPrefix, "");
        var usedByPropertyTypes = getUsedByPropertyTypes(oldAttributeId);
        // update attribute from property type config
        for (var i in usedByPropertyTypes) {
            usedByPropertyTypes[i].attributes[usedByPropertyTypes[i].attributes.indexOf(oldAttributeId)] = newAttributeId;
            for (var j in usedByPropertyTypes[i].groups) {
                var index = usedByPropertyTypes[i].groups[j].attributes.indexOf(oldAttributeId);
                if (index !== -1) {
                    usedByPropertyTypes[i].groups[j].attributes[index] = newAttributeId;
                }
            }
        }
        updateInUseAttributes.push({
            old_id: oldAttributeId,
            new_id: newAttributeId
        });
    };

    $scope.removeAttributeConfig = function(customAttributeConfig) {
        var attributeId = customAttributeConfig.id;
        var usedByPropertyTypes = getUsedByPropertyTypes(attributeId);
        if(usedByPropertyTypes.length) {
            var propertyTypeNames = [];
            for (var i in usedByPropertyTypes) {
                propertyTypeNames.push(usedByPropertyTypes[i].name);
            }
            var dialogMessage = "<h4 style='color:red'>Pay Attention!!!</h4>"
            dialogMessage += "<strong><big>" + customAttributeConfig.title + "</big></strong> attribute is used in config of property types: <strong><big>" + propertyTypeNames.join(", ") + "</big></strong>.<br><br>If you choose to remove the attribute, it will be:<ul><li>- Deleted from config of all property types</li>- Deleted from all properties have set it up</li></ul>";
            var dialogTitle = "Confirm delete";
            window.TrexanhProperty.showConfirmDialog(dialogMessage, dialogTitle, function() {
                $scope.$apply(function() {
                    removeAttributeFromPropertyTypesConfig(attributeId);
                    for (var i in $scope.customAttributesConfig) {
                        if ($scope.customAttributesConfig[i].id == attributeId) {
                            $scope.customAttributesConfig.splice(i, 1);
                            break;
                        }
                    }
                });
            });
        } else {
            for (var i in $scope.customAttributesConfig) {
                if ($scope.customAttributesConfig[i].id == attributeId) {
                    $scope.customAttributesConfig.splice(i, 1);
                    break;
                }
            }
        }
    };
    $scope.customAttributeTitleChanged = function(customAttributeConfig) {
        customAttributeConfig.new_id = attributeInputPrefix + customAttributeConfig.new_title.trim().split(' ').join('_').toLowerCase();
        if (customAttributeConfig.new_title == customAttributeConfig.title) {
            return true;
        }
        for (var i in $scope.customAttributesConfig) {
            var attr = $scope.customAttributesConfig[i];
            if (attr.id != customAttributeConfig.id && attr.title == customAttributeConfig.new_title) {
                window.TrexanhProperty.showAlertDialog("Duplicate attribute name. Please choose another one.");
                return false;
            }
        }
        var attributeId = customAttributeConfig.id;
        var usedByPropertyTypes = getUsedByPropertyTypes(attributeId);
        if(usedByPropertyTypes.length) {
            var propertyTypeNames = [];
            for (var i in usedByPropertyTypes) {
                propertyTypeNames.push(usedByPropertyTypes[i].name);
            }
            var dialogMessage = "<h4 style='color:red'>Pay Attention!!!</h4>"
            dialogMessage += "<strong><big>" + customAttributeConfig.title + "</big></strong> attribute is used in config of property types: <strong><big>" + propertyTypeNames.join(", ") + "</big></strong>.<br><br>If you edit the attribute name, it will be:<ul><li>- Updated in config of all property types</li><li>- Updated in all properties have set it up</li></ul>";
            var dialogTitle = "Confirm update";
            window.TrexanhProperty.showConfirmDialog(dialogMessage, dialogTitle, function() {
                $scope.$apply(function() {
                    updateAttributeFromPropertyTypesConfig(customAttributeConfig);
                });
            }, function() {
                $scope.$apply(function() {
                    customAttributeConfig.new_id = customAttributeConfig.id;
                    customAttributeConfig.new_title = customAttributeConfig.title;
                });
            });
        } else {
            customAttributeConfig.id = customAttributeConfig.new_id;
            customAttributeConfig.title = customAttributeConfig.new_title;
        }
        return true;
    };
    $scope.attributeOptionInputKepressed = function($event, customAttributeConfig) {
        // 188 = comma, 13 = enter
        if ($event.which == 13 || $event.which == 188) {
            $event.preventDefault();
            if (!customAttributeConfig.options) {
                customAttributeConfig.options = [];
            }
            if (customAttributeConfig.options.indexOf(customAttributeConfig.new_option) === -1) {
                customAttributeConfig.options.push(customAttributeConfig.new_option);
            }
            delete customAttributeConfig.new_option;
        }
    };
    $scope.removeAttributeOption = function(customAttributeConfig, $index) {
        customAttributeConfig.options.splice($index, 1);
    };
    $scope.customAttributeInputTypeChanged = function(customAttributeConfig) {
        if (customAttributeConfig.input != 'select' && customAttributeConfig.input != 'multiselect') {
            if (customAttributeConfig.options !== undefined) {
                delete customAttributeConfig.options;
            }
            if (customAttributeConfig.new_option !== undefined) {
                delete customAttributeConfig.new_option;
            }
        }
    };
    $scope.attributeNameInputKepressed = function($event, customAttributeConfig) {
        if ($event.which == 13) {
            $event.preventDefault();
            $scope.customAttributeTitleChanged(customAttributeConfig);
        }
    };
}]);