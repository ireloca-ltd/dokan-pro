<?php
    $modules = dokan_pro_get_modules();
?>
<div class="wrap weforms-modules">
    <h1><?php _e( 'Modules', 'weforms' ); ?></h1>

    <div class="wp-list-table widefat dokan-modules">
        <?php if ( $modules ): ?>

            <?php foreach ( $modules as $slug => $module ): ?>
                <div class="plugin-card">
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
                                <span class="plugin-name"><?php echo $module['name']; ?></span>
                                <img class="plugin-icon" src="<?php echo DOKAN_PRO_PLUGIN_ASSEST . '/images/modules/' . $module['thumbnail'] ?>" alt="" />
                            </h3>
                        </div>

                        <div class="action-links">
                            <ul class="plugin-action-buttons">
                                <li data-module="<?php echo $slug; ?>">
                                    <label class="dokan-toggle-switch">
                                        <input type="checkbox" <?php echo dokan_pro_is_module_active( $slug ) ? 'checked' : ''; ?> name="module_toggle" class="dokan-toggle-module">
                                        <span class="slider round"></span>
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <div class="desc column-description">
                            <p>
                                <?php echo $module['description']; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>

        <?php else: ?>
            <p><?php _e( 'No modules found.', 'dokan' ) ?></p>
        <?php endif ?>

    </div>
</div>
