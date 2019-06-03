<?php
/**
 * Dokan Dashbaord
 * variation table content
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<script type="text/html" id="tmpl-dokan-add-attribute">

    <li class="product-attribute-list">
        <div class="dokan-product-attribute-heading">
            <span><i class="fa fa-bars" aria-hidden="true"></i> <strong>Title</strong></span>
            <input type="hidden" name="attribute_position[]" value="">
            <a href="#" class="dokan-product-remove-attribute"><?php _e( 'Remove', 'dokan' ); ?></a>
            <a href="#" class="dokan-product-toggle-attribute">
                <i class="fa fa-sort-desc fa-flip-horizointal" aria-hidden="true"></i>
            </a>
        </div>
        <div class="dokan-product-attribute-item dokan-clearfix dokan-hide">
            <div class="content-half-part">
                <label class="form-label" for="">Name</label>
                <input type="text" class="dokan-form-control dokan-product-attribute-name" name="" value="">

                <label for="" class="checkbox-item form-label">
                    <input type="checkbox" name="" value="1">  Visible on the product page
                </label>

                <label for="" class="checkbox-item form-label">
                    <input type="checkbox" name="" value="1">  Use for variations
                </label>
            </div>

            <div class="content-half-part">
                <label for="" class="form-label">Values</label>
                <select name="" id="" multiple style="width:100%" class="dokan-select2" data-placeholder="Select name" data-tags="true" data-allow-clear="true" data-token-separators="[',', '|']"></select>
            </div>
        </div>
    </li>

</script>