<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPSoft Display Conditions 2.0
 *
 * Backward compatibility:
 * - Legacy rules without a mode are treated as "include" rules.
 * - Empty conditions still mean "show everywhere".
 *
 * Matching model:
 * - Context include rules (page, post, archive, WooCommerce etc.) use OR logic.
 * - Audience/device include rules (logged in/out, mobile/desktop) use AND logic.
 * - Any matching exclude rule wins and hides the template.
 */
final class WPST_Display_Conditions {
    private static function context_types(){
        return array(
            'entire_site','home','page','post','post_type','portfolio','portfolio_archive','portfolio_category','blog','single_post','blog_archive',
            'category','tag','author','date_archive','search','404',
            'shop','product','product_category','product_tag','cart','checkout','my_account'
        );
    }

    private static function audience_types(){
        return array('logged_in','logged_out','mobile','desktop');
    }

    public static function allowed_types(){
        return array_merge(self::context_types(),self::audience_types());
    }

    public static function labels(){
        return array(
            'entire_site'=>'Tüm Site','home'=>'Sadece Anasayfa','page'=>'Belirli Sayfa','post'=>'Belirli Yazı',
            'post_type'=>'İçerik Türü','portfolio'=>'WPSoft Portföy Sayfası','portfolio_archive'=>'WPSoft Portföy Arşivi','portfolio_category'=>'Portföy Kategorisi','blog'=>'Blog (Tümü)','single_post'=>'Tek Yazılar','blog_archive'=>'Blog Arşivleri',
            'category'=>'Kategori','tag'=>'Etiket','author'=>'Yazar Arşivi','date_archive'=>'Tarih Arşivi',
            'search'=>'Arama Sonuçları','404'=>'404 Sayfası',
            'shop'=>'WooCommerce Mağaza','product'=>'WooCommerce Ürün','product_category'=>'Ürün Kategorisi',
            'product_tag'=>'Ürün Etiketi','cart'=>'Sepet','checkout'=>'Ödeme','my_account'=>'Hesabım',
            'logged_in'=>'Giriş Yapmış Kullanıcı','logged_out'=>'Giriş Yapmamış Kullanıcı','mobile'=>'Mobil','desktop'=>'Masaüstü'
        );
    }

    public static function match($conditions=array(),$groups_relation='or'){
        if(empty($conditions) || !is_array($conditions)) return true;

        $conditions=self::sanitize($conditions);
        if(empty($conditions)) return true;

        // Any matching exclude rule wins globally.
        foreach($conditions as $rule){
            if(isset($rule['mode']) && 'exclude'===$rule['mode'] && self::rule_matches($rule)) return false;
        }

        $groups=array();
        foreach($conditions as $rule){
            if(isset($rule['mode']) && 'exclude'===$rule['mode']) continue;
            $gid=isset($rule['group']) ? max(1,absint($rule['group'])) : 1;
            if(!isset($groups[$gid])) $groups[$gid]=array();
            $groups[$gid][]=$rule;
        }
        if(empty($groups)) return true;

        $group_results=array();
        foreach($groups as $gid=>$rules){
            $result=null;
            foreach($rules as $rule){
                $matched=self::rule_matches($rule);
                if(null===$result){
                    $result=$matched;
                    continue;
                }
                $operator=(isset($rule['operator']) && 'and'===strtolower($rule['operator'])) ? 'and' : 'or';
                $result=('and'===$operator) ? ($result && $matched) : ($result || $matched);
            }
            $group_results[]=(bool)$result;
        }

        $groups_relation=('and'===strtolower((string)$groups_relation))?'and':'or';
        if('and'===$groups_relation){
            foreach($group_results as $matched){ if(!$matched) return false; }
            return true;
        }
        foreach($group_results as $matched){ if($matched) return true; }
        return false;
    }

    public static function match_template($template_id){
        $template_id=absint($template_id);
        if(!$template_id) return false;
        $rules=get_post_meta($template_id,'_wpst_display_conditions',true);
        $groups_relation=get_post_meta($template_id,'_wpst_condition_groups_relation',true);
        return self::match(is_array($rules)?$rules:array(),$groups_relation);
    }

    public static function priority($template_id){
        $priority=(int)get_post_meta(absint($template_id),'_wpst_condition_priority',true);
        return $priority ? $priority : 10;
    }

    private static function ids($value){
        $ids=array_filter(array_map('absint',preg_split('/\s*,\s*/',(string)$value)));
        return array_values(array_unique($ids));
    }

    private static function any_id_matches($value,$callback){
        $ids=self::ids($value);
        if(empty($ids)) return call_user_func($callback,0);
        foreach($ids as $id){ if(call_user_func($callback,$id)) return true; }
        return false;
    }

    private static function rule_matches($rule){
        if(empty($rule['type'])) return false;
        $type=sanitize_key($rule['type']);
        $value=isset($rule['value'])?$rule['value']:'';

        switch($type){
            case 'entire_site': return true;
            case 'home': return is_front_page();
            case 'page': return self::any_id_matches($value,function($id){ return $id ? is_page($id) : is_page(); });
            case 'post': return self::any_id_matches($value,function($id){ return $id ? is_single($id) : is_singular('post'); });
            case 'post_type':
                $post_type=sanitize_key($value);
                return $post_type ? is_singular($post_type) : is_singular();
            case 'portfolio':
                $portfolio_type=class_exists('WPST_Portfolio_Manager') ? WPST_Portfolio_Manager::POST_TYPE : 'wpst_portfolio';
                return self::any_id_matches($value,function($id) use ($portfolio_type){
                    return $id ? (is_singular($portfolio_type) && get_queried_object_id()===$id) : is_singular($portfolio_type);
                });
            case 'portfolio_archive':
                $portfolio_type=class_exists('WPST_Portfolio_Manager') ? WPST_Portfolio_Manager::POST_TYPE : 'wpst_portfolio';
                return is_post_type_archive($portfolio_type);
            case 'portfolio_category':
                $portfolio_tax=class_exists('WPST_Portfolio_Manager') ? WPST_Portfolio_Manager::TAXONOMY : 'wpst_portfolio_category';
                return self::any_id_matches($value,function($id) use ($portfolio_tax){
                    if($id){
                        return is_tax($portfolio_tax,$id) || (is_singular() && has_term($id,$portfolio_tax,get_queried_object_id()));
                    }
                    return is_tax($portfolio_tax);
                });
            case 'blog': return (is_home() || is_singular('post') || is_category() || is_tag() || is_author() || is_date());
            case 'single_post': return is_singular('post');
            case 'blog_archive': return (is_home() || is_category() || is_tag() || is_author() || is_date());
            case 'author': return self::any_id_matches($value,function($id){ return $id ? is_author($id) : is_author(); });
            case 'date_archive': return is_date();
            case 'category':
                return self::any_id_matches($value,function($id){ return $id ? (is_category($id) || (is_singular('post') && has_category($id))) : is_category(); });
            case 'tag':
                return self::any_id_matches($value,function($id){ return $id ? (is_tag($id) || (is_singular('post') && has_tag($id))) : is_tag(); });
            case 'search': return is_search();
            case '404': return is_404();
            case 'logged_in': return is_user_logged_in();
            case 'logged_out': return !is_user_logged_in();
            case 'mobile': return wp_is_mobile();
            case 'desktop': return !wp_is_mobile();
            case 'shop': return function_exists('is_shop') && is_shop();
            case 'product':
                if(!function_exists('is_product')) return false;
                if(!$value) return is_product();
                return self::any_id_matches($value,function($id){ return is_product() && get_queried_object_id()===$id; });
            case 'product_category':
                return function_exists('is_product_category') && self::any_id_matches($value,function($id){ return $id ? is_product_category($id) : is_product_category(); });
            case 'product_tag':
                return function_exists('is_product_tag') && self::any_id_matches($value,function($id){ return $id ? is_product_tag($id) : is_product_tag(); });
            case 'cart': return function_exists('is_cart') && is_cart();
            case 'checkout': return function_exists('is_checkout') && is_checkout();
            case 'my_account': return function_exists('is_account_page') && is_account_page();
        }
        return false;
    }

    public static function sanitize($raw){
        $out=array();
        if(!is_array($raw)) return $out;
        $allowed=self::allowed_types();
        foreach($raw as $rule){
            if(empty($rule['type'])) continue;
            $type=sanitize_key($rule['type']);
            if(!in_array($type,$allowed,true)) continue;
            $mode=(isset($rule['mode']) && 'exclude'===sanitize_key($rule['mode']))?'exclude':'include';
            $out[]=array(
                'mode'=>$mode,
                'type'=>$type,
                'value'=>isset($rule['value'])?sanitize_text_field($rule['value']):'',
                'group'=>isset($rule['group'])?max(1,min(20,absint($rule['group']))):1,
                'operator'=>(isset($rule['operator']) && 'and'===sanitize_key($rule['operator']))?'and':'or'
            );
        }
        return $out;
    }
}
