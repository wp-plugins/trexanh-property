angular.module('App', ['Directives']).controller('PropertyConfigCtrl', ['$scope', '$timeout', function($scope, $timeout) {
    $timeout(function() {
        // place this code in $timeout function to avoid input attribute options of attributes which have type is select to be focused
        $scope.selectedTab = window.TrexanhProperty.selectedTab;
    }, 800);

    // all configs
    var propertyTypesConfig = $scope.propertyTypesConfig =  window.TrexanhProperty.propertyTypesConfig;
    if ( ! propertyTypesConfig ) {
        return;
    }
    var selectedPropertyTypeId = window.TrexanhProperty.selectedPropertyType;

    $scope.propertyTypes = window.TrexanhProperty.propertyTypes;

    for (var id in propertyTypesConfig) {
        if (!selectedPropertyTypeId) {
            selectedPropertyTypeId = id;
        }
    }

    $scope.selectedPropertyTypeId = selectedPropertyTypeId;

    $scope.selectedPropertyType = propertyTypesConfig[selectedPropertyTypeId];

    var configChanged = false;

    $scope.$watch("selectedPropertyType", function(newValue, oldValue) {
        if (angular.equals(newValue, oldValue)) {
            configChanged = false;
        } else {
            configChanged = true;
        }
    }, true);

    // gather un-grouped attributes into a group
    var ungroupedAttributesGroupId = $scope.ungroupedAttributesGroupId = "ungrouped_attributes";
    var groupedAttributes = [];
    for (var groupId in $scope.selectedPropertyType.groups) {
        groupedAttributes.push($scope.selectedPropertyType.groups[groupId].attributes);
    }
    // flatten array of arrays
    if (groupedAttributes.length > 0) {
        groupedAttributes = groupedAttributes.reduce(function(a, b) {
            return a.concat(b);
        });        
    }
    var ungroupedAttributes = $scope.selectedPropertyType.attributes.filter(function(attrId) {
        return groupedAttributes.indexOf(attrId) === -1;
    });
    $scope.selectedPropertyType.groups.push({
        'id' : ungroupedAttributesGroupId,
        'name' : 'No group attributes',
        attributes : ungroupedAttributes
    });

    /**
     * helper functions
     */
    var util = angular.extend({}, window.TrexanhProperty.helperFunctions);

    util.getGroupConfig = function(groupId) {
        return util.getObjectById(groupId, $scope.selectedPropertyType.groups);
    };

    util.getGroupContainers = function() {
        var groupContainers = angular.element(".property-type-group-attributes-container");
        var containerElementArray = [];
        for (var i = 0; i < groupContainers.length; i++) {
            containerElementArray.push(groupContainers.eq(i)[0]);
        }
        return containerElementArray;
    };

    util.refreshDragAndDropContainers = function() {
        setTimeout(function() {
            drake.addContainer(util.getGroupContainers());
        }, 0);
    };

    util.isPropertyTypeExisted = function(id) {
        for (var i in $scope.propertyTypes) {
            if ($scope.propertyTypes[i].id == id) {
                return true;
            }
        }
        return false;
    };

    $scope.changePropertyType = function() {
        var location = util.insertParam(window.location.href, "property_type", $scope.selectedPropertyTypeId);
        location = util.insertParam(location, "section", "property_types");
        if (configChanged) {
            window.TrexanhProperty.showConfirmDialog("Your changes you made has not been save. Do you want to save them?", "Save before leaving?", function() {
                $timeout(function() {
                    angular.element("[type=submit]").click();
                }, 0);
            }, function() {
                window.location = location;
            });
            return;
        }
        window.location = location;
    };

    var allAvailableAttributes = window.TrexanhProperty.allAvailableAttributes;

    $scope.availableAttributes = [];

    for (var id in allAvailableAttributes) {
        if ($scope.selectedPropertyType.attributes.indexOf(id) === -1) {
            $scope.availableAttributes.push(allAvailableAttributes[id]);
        }
    }

    /**
     * get attribute name by id
     * @param string attributeId
     * @returns string
     */
    $scope.attributeName = function(attributeId) {
        if (allAvailableAttributes[attributeId]) {
            return allAvailableAttributes[attributeId].name;
        }
        return "";
    };

    $scope.removeAttribute = function(attributeId, groupId) {
        // remove attribute from attributes list of the property type
        $scope.selectedPropertyType.attributes = util.removeElementFromArray(attributeId, $scope.selectedPropertyType.attributes);
        // remove attribute from attributes list of the group
        var groupConfig = util.getGroupConfig(groupId);
        groupConfig.attributes = util.removeElementFromArray(attributeId, groupConfig.attributes);
        // add attribute back to available attributes list
        $scope.availableAttributes.push(allAvailableAttributes[attributeId]);
    };

    $scope.addAttribute = function(attributeId, groupId) {
        // add attribute to attributes list of the property type
        $scope.selectedPropertyType.attributes.push(attributeId);
        // add attribute to attributes list of the group
        var groupConfig = util.getGroupConfig(groupId);
        groupConfig.attributes.push(attributeId);
        // remove attribute from available attributes list
        for (var i = 0; i < $scope.availableAttributes.length; i++) {
            if ($scope.availableAttributes[i].id == attributeId) {
                $scope.availableAttributes.splice(i, 1);
                break;
            }
        }
    };

    $scope.addAttributeToGroup = function(attrId, groupId) {
        $scope.addAttribute(attrId, groupId);
    };

    /**
     * add new group with default name, e.g: Group 2, Group 3
     * @returns {undefined}
     */
    $scope.addNewGroup = function() {
        var id = angular.element(".property-type-group-container").length;
        var newGroupName = "Group " + id;
        var newGroupId = "group_" + id;
        // make sure ungrouped attributes at the end of the groups list
        var ungroupedAttributes = $scope.selectedPropertyType.groups.splice($scope.selectedPropertyType.groups.length - 1, 1);
        $scope.selectedPropertyType.groups.push({
            id: newGroupId,
            name: newGroupName,
            attributes: []
        });
        $scope.selectedPropertyType.groups.push(ungroupedAttributes[0]);

        // make new group dragable
        util.refreshDragAndDropContainers();

        // show edit group name editor
        $scope.updateGroupNameEditorShown[newGroupId] = true;

        $scope.tempGroupName[newGroupId] = newGroupName;
    };

    $scope.confirmRemoveGroup = function(groupId) {
        window.TrexanhProperty.showConfirmDialog("Are you sure you want to delete this group?", "Confirm delete", function() {
            $scope.$apply(function() {
                $scope.removeGroup(groupId);
            });
        });
    };

    $scope.removeGroup = function(groupId) {
        // remove all attributes of the group from the property types
        var removedAttributes = [];
        var groupConfig = util.getGroupConfig(groupId);
        removedAttributes = groupConfig.attributes;
        // remove the group
        for (var i = 0; i < $scope.selectedPropertyType.groups.length; i++) {
            if ($scope.selectedPropertyType.groups[i].id == groupId) {
                $scope.selectedPropertyType.groups.splice(i, 1);
                break;
            }
        }
        // remove attributes
        $scope.selectedPropertyType.attributes = $scope.selectedPropertyType.attributes.filter(function(attrId) {
            return removedAttributes.indexOf(attrId) === -1;
        });
        // add removed attributes back to available attributes list
        for (var i = 0; i < removedAttributes.length; i++) {
            $scope.availableAttributes.push(allAvailableAttributes[removedAttributes[i]]);
        }
    };        

    /**
     * array to control the displaying of the add attribute forms of groups
     */
    $scope.addFormsShown = [];

    $scope.showAddAttributesForm = function(groupId) {
        $scope.addFormsShown[groupId] = true;
    };

    $scope.hideAddAttributesForm = function(groupId) {
        $scope.addFormsShown[groupId] = false;
    };

    /**
     * array to control the displaying of the add edit group name editors of groups
     */
    $scope.updateGroupNameEditorShown = [];

    $scope.tempGroupName = [];

    $scope.showUpdateGroupNameEditor = function(groupId) {
        var groupConfig = util.getGroupConfig(groupId);
        $scope.tempGroupName[groupId] = groupConfig.name;
        $scope.updateGroupNameEditorShown[groupId] = true;
    };

    $scope.hideUpdateGroupNameEditor = function(groupId) {
        $scope.updateGroupNameEditorShown[groupId] = false;
    };

    $scope.updateGroupName = function(groupId) {
        var groupName = $scope.tempGroupName[groupId];
        if (ungroupedAttributesGroupId == groupId) {
            // do not allow to edit ungrouped attribites group
            $scope.updateGroupNameEditorShown[groupId] = false;
            return false;
        }
        groupName = groupName.trim();
        var newId = groupName.split(' ').join('_').toLowerCase();
        if ( newId == groupId ) {
            // group name no change - do nothing
            $scope.updateGroupNameEditorShown[groupId] = false;
            return false;
        }
        for (var i in $scope.selectedPropertyType.groups) {
            if ($scope.selectedPropertyType.groups[i].id == newId) {
                window.TrexanhProperty.showAlertDialog("Group name <strong>" + groupName + "</strong> is already existed. Please choose another name.");
                return;
            }
        }
        var groupConfig = util.getGroupConfig(groupId);
        groupConfig.name = groupName;
        groupConfig.id = newId;
        $scope.updateGroupNameEditorShown[groupId] = false;
    };
    $scope.groupNameInputChanged = function($event, groupId) {
        if (13 === $event.which) {
            // press enter to save group name
            $event.preventDefault();
            $scope.updateGroupName(groupId);
        }
        if (27 === $event.which) {
            // press escape to cancel editting
            $scope.hideUpdateGroupNameEditor(groupId);
        }
    };

    $scope.tempPropertyTypeName = null;

    $scope.showUpdatePropertyTypeNameEditor = function() {
        $scope.tempPropertyTypeName = $scope.selectedPropertyType.name;
        $scope.showUpdateTypeNameEditor = true;
    };

    $scope.hideUpdatePropertyTypeNameEditor = function() {
        $scope.showUpdateTypeNameEditor = false;
    };

    $scope.propertyTypeNameInputChanged = function($event) {
        if (13 === $event.which) {
            // press enter to save property type name
            $event.preventDefault();
            $scope.updatePropertyTypeName();
        }
        if (27 === $event.which) {
            // press escape to cancel
            $scope.hideUpdatePropertyTypeNameEditor();
        }
    };
    $scope.updatePropertyTypeName = function() {
        var oldId = $scope.selectedPropertyType.id;
        var newName = $scope.tempPropertyTypeName;
        var newId = newName.split(' ').join('_').toLowerCase();
        if ( newId == oldId ) {
            $scope.showUpdateTypeNameEditor = false;
            return false;
        }
        if (util.isPropertyTypeExisted(newId)) {
            window.TrexanhProperty.showAlertDialog("Property type <strong>" + newName + "</strong> is already existed. Please choose another name.");
            return;
        }
        // update text displayed in property type picker (if editting)
        var action = window.TrexanhProperty.propertyConfigAction;
        if ('new_property_type' != action && 'clone_property_type' != action) {
            var pickedPropertyType = util.getObjectById(oldId, $scope.propertyTypes);
            pickedPropertyType.name = $scope.tempPropertyTypeName;
            pickedPropertyType.id = newId;
        }

        $scope.selectedPropertyType.name = $scope.tempPropertyTypeName;
        $scope.selectedPropertyType.id = newId;

        // add new type with new name and remove the one with old name
        $scope.propertyTypesConfig[newId] = angular.copy($scope.propertyTypesConfig[$scope.selectedPropertyTypeId]);
        delete $scope.propertyTypesConfig[$scope.selectedPropertyTypeId];

        // update the selected property type
        $scope.selectedPropertyTypeId = newId;
        $scope.selectedPropertyType = $scope.propertyTypesConfig[newId];

        $scope.showUpdateTypeNameEditor = false;
    };

    $scope.moveAttribute = function(attributeId, sourceGroupId, destGroupId) {
        var souceGroupConfig = util.getGroupConfig(sourceGroupId);
        var destGroupConfig = util.getGroupConfig(destGroupId);
        souceGroupConfig.attributes = util.removeElementFromArray(attributeId, souceGroupConfig.attributes);
        destGroupConfig.attributes.push(attributeId);
        configChanged = true;
    };

    $scope.submit = function() {
        $scope.$broadcast('submitForm');
        var form = angular.element("form");
        var input = form.find("[name=property_types_config]");
        if ( ! input.length ) {
            input = angular.element("<input>").attr("type", "hidden").attr("name", "property_types_config");
            form.append(input);
        }
        var data = angular.copy($scope.propertyTypesConfig);
        // remove ungrouped attributes group from data which will be sent to server
        for (var i in data) {
            for (var j in data[i].groups) {
                if (data[i].groups[j].id == ungroupedAttributesGroupId) {
                    data[i].groups.splice(j, 1);
                }
            }
        }
        input.val(angular.toJson(data));
        // send selectedPropertyTypeId also
        // to help server to redirect to page with selectedPropertyTypeId pre-selected after saving
        var workingPropertyInput = form.find("[name=working_property_type_id]");
        if ( ! workingPropertyInput.length ) {
            workingPropertyInput = angular.element("<input>").attr("type", "hidden").attr("name", "working_property_type_id");
            form.append(workingPropertyInput);
        }
        workingPropertyInput.val($scope.selectedPropertyTypeId);
    };

    // dragula instance
    var drake = null;

    angular.element(document).ready(function () {
        // init drag and drop
        drake = dragula(util.getGroupContainers());
        drake.on ('drop', function(el, destination, source) {
            var $el = angular.element(el),
                $detination = angular.element(destination),
                $source = angular.element(source),
                attributeId = $el.attr("data-attribute-id"),
                destinationGroupId = $detination.closest(".property-type-group-attributes-container").attr("data-group-id"),
                sourceGroupId = $source.closest(".property-type-group-attributes-container").attr("data-group-id");
            $scope.moveAttribute(attributeId, sourceGroupId, destinationGroupId);

        });
        var action = window.TrexanhProperty.propertyConfigAction;
        if ('new_property_type' == action || 'clone_property_type' == action) {
            // show edit property type name editor for usability
            $timeout(function() {
                $scope.showUpdatePropertyTypeNameEditor();
            }, 0);
        }
    });
}]);