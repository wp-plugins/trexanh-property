<?php
    // this is an underscore-formatted-template
    // @link: http://underscorejs.org/#template
?>
<script type="text/template" id="map-info-box-template">
    <% var type = property.type; %>
    <table class="table ct-itemProducts map-info-box">
        <tr>
            <% if ( property.image_url ) { %>
                <td rowspan="2" style="width:160px;">
                    <% if ( 'sale' == type ) { %>
                        <label class="control-label sale">Sale</label>
                    <% } %>
                    <% if ( 'rent' == type ) { %>
                        <label class="control-label new">Rent</label>
                    <% } %>
                    <a href="<%= property.url %>"><img src="<%= property.image_url %>" /></a>
                </td>
            <% } %>
            <td>
                <h3>
                    <a href="<%= property.url %>"><%= property.title %></a>
                </h3>
                <% if ( property.location_string ) { %>
                    <p>
                        <span class="fa fa-map-marker"></span> <%= property.location_string %>
                    </p>
                <% } %>
                <% if ( property.category ) { %>
                    <big>
                        <span class="label label-warning"> <%= property.category %>
                            <% if ( ! property.image_url ) { %>
                                 for <%= type %>
                            <% } %>
                        </span>
                    </big>
                <% } %>
            </td>
        </tr>
        <tr>
            <td style="vertical-align:bottom;">
                <% if ( 'sale' == type && property.price ) { %>
                    <div class="pull-left ct-product--price"><%= property.price_string %></div>
                <% } %>
                <% if ( 'rent' == type && property.rent_amount ) { %>
                    <div class="pull-left ct-product--price"><%= property.rent_amount_string %></div>
                <% } %>
                <div class="pull-right">
                    <big>
                        <% if ( property.bedrooms ) { %>
                            <span class="fa fa-bed"></span> <%= property.bedrooms %> beds
                        <% } %>
                        <% if ( property.bathrooms ) { %>
                            <span class="fa fa-tint"></span> <%= property.bathrooms %> paths
                        <% } %>
                    </big>
                </div>
            </td>
        </tr>
    </table>
</script>