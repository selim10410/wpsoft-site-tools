<?php
if ( ! defined( 'ABSPATH' ) ) exit;
final class WPST_SVG_Library {
    public static function shapes() {
        return array(
            'blob-soft'=>'Blob Soft',
            'blob-wide'=>'Blob Wide',
            'wave'=>'Wave',
            'wave-double'=>'Wave Double',
            'dots'=>'Dots',
            'grid'=>'Grid',
            'rings'=>'Rings',
            'orbit'=>'Orbit',
            'corner-lines'=>'Corner Lines',
            'diagonal-lines'=>'Diagonal Lines',
            'cross-grid'=>'Cross Grid',
            'spotlight'=>'Spotlight',
            'arc'=>'Arc',
            'stairs'=>'Stairs',
            'frame'=>'Frame',
            'burst'=>'Burst',
        );
    }
    public static function options() { return self::shapes(); }
    public static function url($slug) {
        $all=self::shapes();
        if(!isset($all[$slug]))$slug='blob-soft';
        return WPST_URL.'assets/svg/shapes/'.rawurlencode($slug).'.svg';
    }
    public static function inline($slug,$args=array()) {
        $all=self::shapes(); if(!isset($all[$slug]))$slug='blob-soft';
        $file=WPST_PATH.'assets/svg/shapes/'.$slug.'.svg';
        if(!is_readable($file))return '';
        $svg=file_get_contents($file);
        $class=isset($args['class'])?sanitize_html_class($args['class']):'wpst-shape-svg';
        return preg_replace('/<svg\s/','<svg class="'.esc_attr($class).'" aria-hidden="true" focusable="false" ', $svg,1);
    }
}
