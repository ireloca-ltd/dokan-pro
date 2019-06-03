<div class="jssocials-shares">
<?php foreach ( $providers as $provider ) : ?>
    <div class="jssocials-share jssocials-share-<?php echo $provider ?>">
        <a href="<?php echo add_query_arg( array( 'dokan_reg' => $provider ), $base_url ); ?>" class="jssocials-share-link">
            <i class="fa fa-<?php echo $provider ?> jssocials-share-logo"></i>
        </a>
    </div>
<?php  endforeach; ?>
</div>