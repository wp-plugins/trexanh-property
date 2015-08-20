<?php 
/**
 * Template for config property type and property attribute
 */
?>
<div class="tabs property-types-config-container">
    <ul class="tab-links">
        <li ng-class="{'active' : selectedTab == 'property_types' }">
            <a href="" ng-click="selectedTab = 'property_types'">
                <?php echo __( "Property Types", "txp" ); ?>
            </a>
        </li>
        <li ng-class="{'active' : selectedTab == 'config_attributes' }">
            <a href="" ng-click="selectedTab = 'config_attributes'">
                <?php echo __( "Config Attributes", "txp" ); ?>
            </a>
        </li>
    </ul>
    <input type="hidden" name="selected_tab" ng-value="selectedTab"/>
    <div class="tab-content">
        <div id="property-types" class="tab" ng-class="{'active' : selectedTab == 'property_types' }">
            <?php if ('edit_property_type' == $action) { ?>
                <h3 style="margin-top:0;">
                    <?php echo __( "Update property type", "txp" ); ?>
                </h3>
                <?php echo __( "Property type", "txp" ); ?>
                <select class="property-type-picker" ng-model="selectedPropertyTypeId" ng-options="type.id as type.name for type in propertyTypes" ng-change="changePropertyType()">
                </select>
                <div class="property-type-actions">
                    <a class="add-new-h2" href="<?php echo $clone_url; ?>">
                        <?php echo __( "Clone this type", "txp" ); ?>
                    </a>
                    <a class="add-new-h2" href="<?php echo $new_url; ?>">
                        <?php echo __( "Add new type", "txp" ); ?>
                    </a>
                </div>
            <?php } ?>
            <?php if ('clone_property_type' == $action) { ?>
                <h3>
                    <?php echo __( "Clone property type", "txp" ); ?>
                </h3>
            <?php } ?>
            <?php if ('new_property_type' == $action) { ?>
                <h3>
                    <?php echo __( "New property type", "txp" ); ?>
                </h3>
            <?php } ?>
            <div class="property-types-container">
                <div class="property-type-container">
                    <div class="inline-edit-container">
                        <span class="edit-label"><?php echo __( "Name", "txp" ); ?>:</span>
                        <span class="edit-view" ng-hide="showUpdateTypeNameEditor">
                            <strong>{{selectedPropertyType.name}}</strong>&nbsp;
                            <a href="" ng-click="showUpdatePropertyTypeNameEditor()"><span class="dashicons-before dashicons-edit"></span></a>
                        </span>
                        <span class="edit-editor" ng-show="showUpdateTypeNameEditor">
                            <input type="text" ng-model="tempPropertyTypeName" focus="showUpdateTypeNameEditor" ng-keydown="propertyTypeNameInputChanged($event)">&nbsp;
                            <a href="" ng-click="updatePropertyTypeName()"><span class="dashicons-before dashicons-yes"></span></a>
                            <a href="" ng-click="hideUpdatePropertyTypeNameEditor()"><span class="dashicons-before dashicons-no"></span></a>
                        </span>
                    </div><br>
                    <div>
                        <span><?php echo __( "Enable", "txp" ); ?>:</span>&nbsp;
                        <input type="checkbox" ng-model="selectedPropertyType.enabled" /> <i ng-show="!selectedPropertyType.enabled">(If you do not enable then you will not able to create new property of this type)</i>
                    </div>
                    <h4>
                        <?php echo __( "Config attributes", "txp" ); ?>
                    </h4>
                    <div class="property-type-groups-container">
                        <div class="property-type-group-container" ng-repeat="groupConfig in selectedPropertyType.groups track by groupConfig.id" id="{{groupConfig.id}}">
                            <div class="inline-edit-container">
                                <span class="edit-view" ng-hide="updateGroupNameEditorShown[groupConfig.id]">
                                    <strong>{{groupConfig.name}}</strong>&nbsp;
                                    <a href="" ng-click="showUpdateGroupNameEditor(groupConfig.id)" ng-if="groupConfig.id != ungroupedAttributesGroupId"><span class="dashicons-before dashicons-edit"></span></a>
                                </span>
                                <span class="edit-editor" ng-show="updateGroupNameEditorShown[groupConfig.id]">
                                    <input
                                        type="text"
                                        value="{{groupConfig.name}}"
                                        ng-model="tempGroupName[groupConfig.id]"
                                        focus="updateGroupNameEditorShown[groupConfig.id]"
                                        ng-keydown="groupNameInputChanged($event, groupConfig.id)"
                                    />&nbsp;
                                    <a href="" ng-click="updateGroupName(groupConfig.id)"><span class="dashicons-before dashicons-yes"></span></a>
                                    <a href="" ng-click="hideUpdateGroupNameEditor(groupConfig.id)"><span class="dashicons-before dashicons-no"></span></a>
                                </span>
                            </div>
                            <div class="property-type-group-attributes-container attributes-list" data-group-id="{{groupConfig.id}}">
                                <div
                                    class="attribute-item"
                                    ng-repeat="attributeId in groupConfig.attributes"
                                    data-attribute-id="{{attributeId}}"
                                    title="<?php echo __( "Drag and drop into other group to change group", "txp" ); ?>"
                                >
                                    <span class="draggable-icon"></span> {{attributeName(attributeId)}} <a href="" class="remove-attribute" ng-click="removeAttribute(attributeId, groupConfig.id)"><span class="dashicons-before dashicons-no"></span></a>
                                </div>
                            </div>
                            <div>
                                <a href="" ng-click="showAddAttributesForm(groupConfig.id)" ng-hide="addFormsShown[groupConfig.id]">
                                    <?php echo __( "Add attributes", "txp" ); ?>
                                </a>
                            </div>
                            <div class="add-attributes-form" ng-show="addFormsShown[groupConfig.id]">
                                <h4>
                                    <?php echo __( "Click on attributes to add", "txp" ); ?>
                                </h4>
                                <ul class="attributes-list">
                                    <li class="attribute-item" ng-repeat="attr in availableAttributes track by attr.id">
                                        <label ng-click="addAttributeToGroup(attr.id, groupConfig.id)">{{attr.name}}</label>
                                    </li>
                                </ul>
                                <div class="buttons">
                                    <a href="" ng-click="hideAddAttributesForm(groupConfig.id)">
                                        <?php echo __( "Finish", "txp" ); ?>
                                    </a>
                                </div>
                            </div>
                            <div
                                class="dashicons-before dashicons-no actions-menu-trigger"
                                ng-if="groupConfig.id != ungroupedAttributesGroupId"
                                ng-class="{'open' : actions_menu_shown[groupConfig.id]}"
                                ng-click="confirmRemoveGroup(groupConfig.id)"
                            ></div>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="button" class="button button-default" ng-click="addNewGroup()"><?php echo __( "New group", "txp" ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="property-attributes" class="tab custom-attributes-container" ng-controller="CustomAttributeConfigCtrl" ng-class="{'active' : selectedTab == 'config_attributes' }">
            <h3 style="margin-top:0;">
                <?php echo __( "Core Attributes", "txp" ); ?>
                <small><small>(You can not edit or delete these attributes)</small></small>
            </h3>
            <ul class="attributes-list">
                <li ng-repeat="attr in coreAttributes" class="attribute-item">{{attr.name}}&nbsp;&nbsp;&nbsp;&nbsp;</li>
            </ul>
            <hr>
            <h3>
                <?php echo __( "Custom Attributes", "txp" ); ?>
            </h3>
            <table>
                <tr>
                    <th>
                        <?php echo __( "Attribute name", "txp" ); ?>
                    </th>
                    <th>
                        <?php echo __( "Input type", "txp" ); ?>
                    </th>
                    <th>
                        <?php echo __( "Attribute options", "txp" ); ?>
                    </th>
                    <th>
                        <?php echo __( "Action", "txp" ); ?>
                    </th>
                </tr>
                <tr ng-repeat="customAttributeConfig in customAttributesConfig track by customAttributeConfig.id">
                    <td style="border-bottom:1px solid #ddd;width:1%;" data-th="<?php echo __( "Attribute name", "txp" ); ?>">
                        <input
                            type="text"
                            ng-init="customAttributeConfig.new_title = customAttributeConfig.title"
                            ng-model="customAttributeConfig.new_title"
                            ng-blur="customAttributeTitleChanged(customAttributeConfig)"
                            ng-keydown="attributeNameInputKepressed($event, customAttributeConfig)"
                        />
                    </td>
                    <td style="border-bottom:1px solid #ddd;width:1%;" data-th="<?php echo __( "Input type", "txp" ); ?>">
                        <select ng-model="customAttributeConfig.input" ng-change="customAttributeInputTypeChanged(customAttributeConfig)">
                            <option value="" ng-selected="!customAttributeConfig.input"> - </option>
                            <option
                                ng-repeat="inputType in attributeInputTypes track by inputType.id"
                                ng-value="inputType.id"
                                ng-selected="inputType.id == customAttributeConfig.input"
                            >
                                {{inputType.name}}
                            </option>
                        </select>
                    </td>
                    <td style="border-bottom:1px solid #ddd;" data-th="<?php echo __( "Attribute options", "txp" ); ?>">
                        <div ng-show="customAttributeConfig.input == 'select' || customAttributeConfig.input == 'multiselect'" class="attribute-option-values-container">
                            <span class="value-tag" ng-repeat="option in customAttributeConfig.options" >
                                {{option}}
                                <span
                                    class="dashicons-before dashicons-no remove-icon"
                                    ng-click="removeAttributeOption(customAttributeConfig, $index)"
                                ></span>
                            </span>
                            <input
                                type="text"
                                placeholder="Enter or comma to add"
                                ng-model="customAttributeConfig.new_option"
                                ng-keydown="attributeOptionInputKepressed($event, customAttributeConfig)"
                                focus="customAttributeConfig.input == 'select'"
                            />
                        </div>
                    </td>
                    <td style="border-bottom:1px solid #ddd;width:1%;text-align:center;" data-th="<?php echo __( "Action", "txp" ); ?>">
                        <a href="" ng-click="removeAttributeConfig(customAttributeConfig)" class="delete-icon">
                            <span class="dashicons-before dashicons-no"></span>
                        </a>
                        <a href="" ng-click="removeAttributeConfig(customAttributeConfig)" class="delete-link">
                            <?php echo __( "Delete", "txp" ); ?>
                        </a>
                        <hr class="mobile-divider">
                    </td>
                </tr>
            </table>
            <br>
            <a href="" class="button button-default" ng-click="addCustomAttribute()">
                <?php echo __( "Add attribute", "txp" ); ?>
            </a>
        </div>
        <script>
            if (window.TrexanhProperty === undefined) {
                window.TrexanhProperty = {};
            }
            window.TrexanhProperty.propertyConfigPageUrl = '<?php echo remove_query_arg( array( 'action', 'property_type', 'section') ) ?>';
            window.TrexanhProperty.propertyConfigAction = '<?php echo $action; ?>';
            window.TrexanhProperty.propertyTypesConfig = JSON.parse('<?php echo json_encode($options['property_types']); ?>');
            window.TrexanhProperty.propertyTypes = JSON.parse('<?php echo json_encode($property_types); ?>');
            window.TrexanhProperty.selectedTab = '<?php echo $selected_tab; ?>';
            window.TrexanhProperty.customAttributesConfig = JSON.parse('<?php echo json_encode($options['custom_attributes']); ?>');
            window.TrexanhProperty.selectedPropertyType = '<?php echo $property_type; ?>';
            window.TrexanhProperty.allAvailableAttributes = JSON.parse('<?php echo json_encode($all_available_attributes); ?>');
            window.TrexanhProperty.coreAttributes = JSON.parse('<?php echo json_encode($core_attributes); ?>');
            window.TrexanhProperty.attributeInputTypes = JSON.parse('<?php echo json_encode($attribute_input_types); ?>');
            window.TrexanhProperty.TxlDialog = new window.Txl.Dialog();
            window.TrexanhProperty.showAlertDialog = function(message, title) {
                var dialog = window.TrexanhProperty.TxlDialog;
                dialog.setMode("alert");
                if (!title) {
                    title = "Warning";
                }
                dialog.setTitle(title);
                dialog.setMessage(message);
                dialog.open();
            };
            window.TrexanhProperty.showConfirmDialog = function(message, title, okCallback, cancelCallback) {
                var dialog = window.TrexanhProperty.TxlDialog;
                dialog.setMode("confirm");
                if (!title) {
                    title = "Confirm";
                }
                dialog.setTitle(title);
                dialog.setMessage(message);
                if (okCallback) {
                    dialog.setOkCallback(okCallback);
                }
                if (cancelCallback) {
                    dialog.setCancelCallback(cancelCallback);
                }
                dialog.open();
            };
            jQuery(document).ready(function($) {
                // remove empty left column
                $(".form-table th[scope='row']").remove();
                var action = window.TrexanhProperty.propertyConfigAction;
                var util = window.TrexanhProperty.helperFunctions;
                if ('new_property_type' == action || 'clone_property_type' == action) {
                    // add button to allow to cancel add new or clone property type action
                    var backUrl = window.TrexanhProperty.propertyConfigPageUrl;
                    var propertyType = util.getUrlParameter('property_type');
                    if (propertyType) {
                        backUrl += "&property_type=" + propertyType;
                    }
                    var cancelButtonLink = "<a href='" + backUrl + "' class='button button-default' style='margin-left:10px;'>Cancel</a>";
                    $(".submit").append(cancelButtonLink);
                }
            });
        </script>
    </div>
</div>