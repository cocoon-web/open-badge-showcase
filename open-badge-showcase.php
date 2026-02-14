<?php
/**
 * Plugin Name: Open Badge Showcase (Aura CIAN Edition)
 * Description: A portable achievement system for researchers. Integrates ACF metadata with BuddyPress profiles.
 * Version: 1.0
 * Author: Aura CIAN / El Pez Volador
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 1. HEADER DISPLAY: Featured Badges (The "Top 5")
 * This hook displays the first 5 badges right in the member header for immediate visibility.
 */
add_action('bp_before_member_header_meta', function() {
    $user_id = bp_displayed_user_id();
    $acf_user_id = 'user_' . $user_id;

    echo '';
    echo '<div class="mochila-favoritos" style="display:flex; align-items:center; gap:8px; margin-top:12px; flex-wrap: wrap;">';
    
    for ($i = 1; $i <= 5; $i++) {
        $n = str_pad($i, 2, '0', STR_PAD_LEFT); 
        $img = get_field('imagen_' . $n, $acf_user_id);
        $tit = get_field('titulo_' . $n, $acf_user_id);

        if ( $img ) {
            $src = is_array($img) ? $img['url'] : wp_get_attachment_image_url($img, 'thumbnail');
            echo '<img src="' . esc_url($src) . '" title="' . esc_attr($tit) . '" alt="' . esc_attr($tit) . '" style="width:45px; height:45px; object-fit:contain; border:1px solid #ddd; border-radius:8px; padding:3px; background:#fff; box-shadow: 2px 2px 5px rgba(0,0,0,0.05);">';
        }
    }

    echo '<a href="' . bp_displayed_user_domain() . 'insignias/" style="font-size:11px; color:#1A9CA6; margin-left:10px; text-decoration:none; font-weight:bold;">View All / Ver todas →</a>';
    echo '</div>';
}, 12);

/**
 * 2. NAVIGATION: Add "Insignias" Tab to BuddyPress
 */
add_action( 'bp_setup_nav', function() {
    global $bp;
    bp_core_new_nav_item( array( 
        'name' => 'Insignias', 
        'slug' => 'insignias', 
        'position' => 50, 
        'screen_function' => 'open_badge_showcase_screen_loader',
        'default_subnav_slug' => 'insignias',
        'parent_url' => trailingslashit( bp_displayed_user_domain() . $bp->profile->slug ),
        'parent_slug' => $bp->profile->slug
    ) );
}, 100 );

/**
 * 3. SCREEN LOADER: Prevents 404 by loading the proper template
 */
function open_badge_showcase_screen_loader() {
    add_action( 'bp_template_content', 'open_badge_showcase_tab_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_display', 'members/single/plugins' ) );
}

/**
 * 4. TAB CONTENT: Full Collection Grid
 */
function open_badge_showcase_tab_content() {
    $user_id = bp_displayed_user_id();
    $acf_user_id = 'user_' . $user_id;

    echo '<div class="badge-showcase-full">';
    echo '<h3 style="margin-bottom:20px;">Trayectoria y Certificaciones / Achievements</h3>';
    echo '<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap:25px;">';

    // Loop through 20 possible badge slots (ACF: imagen_01 to imagen_20)
    for ($i = 1; $i <= 20; $i++) {
        $n = str_pad($i, 2, '0', STR_PAD_LEFT); 
        $img = get_field('imagen_' . $n, $acf_user_id);
        $url = get_field('url_' . $n, $acf_user_id);
        $tit = get_field('titulo_' . $n, $acf_user_id);

        if ( $img ) {
            $src = is_array($img) ? $img['url'] : wp_get_attachment_image_url($img, 'thumbnail');
            echo '<div style="text-align:center;">';
            if($url) echo '<a href="' . esc_url($url) . '" target="_blank" style="text-decoration:none;">';
            echo '<img src="' . esc_url($src) . '" style="width:90px; height:90px; object-fit:contain; border:2px solid #5F8D8D; border-radius:12px; padding:6px; background:#fff; transition: transform 0.2s;">';
            if($url) echo '</a>';
            if($tit) echo '<p style="font-size:12px; margin-top:8px; line-height:1.2; color:#333;">' . esc_html($tit) . '</p>';
            echo '</div>';
        }
    }
    echo '</div></div>';
}
