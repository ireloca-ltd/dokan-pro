<?php
/**
 * Dokan Dashbaord Settings SEO Form Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<div class="dokan-alert dokan-hide" id="dokan-seo-feedback"></div>

<form method="post" id="dokan-store-seo-form"  action="" class="dokan-form-horizontal">

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-meta-title"><?php _e( 'SEO Title :', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" title="" data-placement="bottom" data-original-title="<?php _e( 'SEO Title is shown as the title of your store page', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w5 dokan-text-left">
            <input id="dokan-seo-meta-title" value="<?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-meta-title'] ) ?>" name="dokan-seo-meta-title" placeholder=" " class="dokan-form-control input-md" type="text">
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-meta-desc"><?php _e( 'Meta Description :', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" title="" data-placement="bottom" data-original-title="<?php _e( 'The meta description is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for and should be less than 156 chars.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w5 dokan-text-left">
            <textarea class="dokan-form-control" rows="3" id="dokan-seo-meta-desc" name="dokan-seo-meta-desc"><?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-meta-desc'] ) ?></textarea>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-meta-keywords"><?php _e( 'Meta Keywords :', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" title="" data-placement="bottom" data-original-title="<?php _e( 'Insert some comma separated keywords for better ranking of your store page.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w5 dokan-text-left">
            <input id="dokan-seo-meta-keywords" value="<?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-meta-keywords'] ) ?>" name="dokan-seo-meta-keywords" placeholder=" " class="dokan-form-control input-md" type="text">
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-og-title"><?php _e( 'Facebook Title :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input id="dokan-seo-og-title" value="<?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-og-title'] ) ?>" name="dokan-seo-og-title" placeholder=" " class="dokan-form-control input-md" type="text">
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-og-desc"><?php _e( 'Facebook Description :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <textarea class="dokan-form-control" rows="3" id="dokan-seo-og-desc" name="dokan-seo-og-desc"><?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-og-desc'] ) ?></textarea>
        </div>
    </div>
    <?php
    $og_image     = $seo_meta['dokan-seo-og-image'] ? $seo_meta['dokan-seo-og-image'] : 0;
    $og_image_url = $og_image ? wp_get_attachment_thumb_url( $og_image ) : '';
    ?>
    <div class="dokan-form-group ">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-og-image"><?php _e( 'Facebook Image :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-gravatar dokan-seo-image">
            <div class="dokan-left gravatar-wrap<?php echo $og_image ? '' : ' dokan-hide'; ?>">
                <input type="hidden" class="dokan-file-field" value="<?php echo $og_image; ?>" name="dokan-seo-og-image">
                <img class="dokan-gravatar-img" src="<?php echo esc_url( $og_image_url ); ?>">
                <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
            </div>

            <div class="gravatar-button-area <?php echo $og_image ? ' dokan-hide' : ''; ?>">
                <a href="#" class="dokan-gravatar-drag dokan-btn dokan-btn-default dokan-left"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan' ); ?></a>
            </div>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-twitter-title"><?php _e( 'Twitter Title :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <input id="dokan-seo-twitter-title" value="<?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-twitter-title'] ) ?>" name="dokan-seo-twitter-title" placeholder=" " class="dokan-form-control input-md" type="text">
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-twitter-desc"><?php _e( 'Twitter Description :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <textarea class="dokan-form-control" rows="3" id="dokan-seo-twitter-desc" name="dokan-seo-twitter-desc"><?php echo $seo->print_saved_meta( $seo_meta['dokan-seo-twitter-desc'] ) ?></textarea>
        </div>
    </div>
    <?php
    $twitter_image     = $seo_meta['dokan-seo-twitter-image'] ? $seo_meta['dokan-seo-twitter-image'] : 0;
    $twitter_image_url = $twitter_image ? wp_get_attachment_thumb_url( $twitter_image ) : '';
    ?>
    <div class="dokan-form-group ">
        <label class="dokan-w3 dokan-control-label" for="dokan-seo-twitter-image"><?php _e( 'Twitter Image :', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-gravatar dokan-seo-image">
            <div class="dokan-left gravatar-wrap<?php echo $twitter_image ? '' : ' dokan-hide'; ?>">
                <input type="hidden" class="dokan-file-field" value="<?php echo $twitter_image; ?>" name="dokan-seo-twitter-image">
                <img class="dokan-gravatar-img" src="<?php echo esc_url( $twitter_image_url ); ?>">
                <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
            </div>

            <div class="gravatar-button-area <?php echo $twitter_image ? ' dokan-hide' : ''; ?>">
                <a href="#" class="dokan-gravatar-drag dokan-btn dokan-btn-default dokan-left"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan' ); ?></a>
            </div>
        </div>
    </div>

    <?php wp_nonce_field( 'dokan_store_seo_form_action', 'dokan_store_seo_form_nonce' ); ?>

    <div class="dokan-form-group" style="margin-left: 23%">
        <input type="submit" id='dokan-store-seo-form-submit' class="dokan-left dokan-btn dokan-btn-theme" value="<?php esc_attr_e( 'Save Changes', 'dokan' ); ?>">
    </div>
</form>
