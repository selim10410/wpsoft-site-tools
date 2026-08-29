<?php
if(!defined('ABSPATH'))exit;

class WPST_Widget_WPForms extends WPST_Elementor_Widget_Base {
    public function get_name(){ return 'wpsoft-wpforms'; }
    public function get_title(){ return 'WPSoft · WPForms 2.0'; }
    public function get_icon(){ return 'eicon-form-horizontal'; }
    public function get_keywords(){ return array('wpforms','form','iletişim','contact','teklif','lead','wpsoft'); }

    private function form_options(){
        $options=array(''=>'Form seçin');
        if(post_type_exists('wpforms')){
            $forms=get_posts(array(
                'post_type'=>'wpforms',
                'post_status'=>'publish',
                'posts_per_page'=>100,
                'orderby'=>'title',
                'order'=>'ASC'
            ));
            foreach($forms as $form){
                $options[(string)$form->ID]=$form->post_title.' (#'.$form->ID.')';
            }
        }
        return $options;
    }

    protected function register_controls(){
        $this->start_controls_section('content',array('label'=>'WPForms'));

        $this->add_control('info',array(
            'type'=>\Elementor\Controls_Manager::RAW_HTML,
            'raw'=>'<strong>WPForms entegrasyonu</strong><br>WPForms kuruluysa formu listeden seçin. Liste boşsa Form ID alanını kullanabilirsiniz.',
            'content_classes'=>'wpst-control-note'
        ));

        $this->add_control('form_id',array(
            'label'=>'WPForms Formu',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'options'=>$this->form_options(),
            'default'=>''
        ));

        $this->add_control('manual_form_id',array(
            'label'=>'Form ID (Manuel)',
            'type'=>\Elementor\Controls_Manager::NUMBER,
            'min'=>1,
            'description'=>'Yukarıdaki listede form görünmüyorsa WPForms form ID değerini girin.'
        ));

        $this->add_control('show_title',array(
            'label'=>'Form Başlığını Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>''
        ));

        $this->add_control('show_description',array(
            'label'=>'Form Açıklamasını Göster',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>''
        ));

        $this->add_control('empty_title',array(
            'label'=>'Editör Placeholder Başlığı',
            'type'=>\Elementor\Controls_Manager::TEXT,
            'default'=>'İletişim Formunuzu Seçin'
        ));

        $this->add_control('empty_text',array(
            'label'=>'Editör Placeholder Açıklaması',
            'type'=>\Elementor\Controls_Manager::TEXTAREA,
            'default'=>'WPForms formunu oluşturduktan sonra bu widgettan form ID seçin.'
        ));


        $this->add_control('missing_plugin_text',array(
            'label'=>'WPForms Eksik Uyarısı',
            'type'=>\Elementor\Controls_Manager::TEXTAREA,
            'default'=>'WPForms eklentisi etkin değil veya form shortcode sistemi yüklenmedi.',
            'description'=>'Editör önizlemesinde WPForms kullanılamadığında gösterilir.'
        ));
        $this->add_control('shell_style',array(
            'label'=>'Form Kart Stili',
            'type'=>\Elementor\Controls_Manager::SELECT,
            'default'=>'clean',
            'options'=>array('clean'=>'Clean','card'=>'Card','soft'=>'Soft','dark'=>'Dark'),
            'prefix_class'=>'wpst-wpforms-style-'
        ));

        $this->end_controls_section();

        $this->start_controls_section('form_style',array(
            'label'=>'WPForms Biçimi',
            'tab'=>\Elementor\Controls_Manager::TAB_STYLE
        ));

        $this->add_control('label_color',array(
            'label'=>'Alan Etiketi Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-field-label, {{WRAPPER}} .wpforms-field-sublabel'=>'color:{{VALUE}}!important;'
            )
        ));

        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array(
            'name'=>'label_typography',
            'label'=>'Etiket Tipografisi',
            'selector'=>'{{WRAPPER}} .wpforms-field-label, {{WRAPPER}} .wpforms-field-sublabel'
        ));

        $this->add_control('input_bg',array(
            'label'=>'Alan Arka Planı',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .wpforms-form textarea, {{WRAPPER}} .wpforms-form select'=>'background-color:{{VALUE}}!important;'
            )
        ));

        $this->add_control('input_color',array(
            'label'=>'Alan Yazı Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-form input:not([type="submit"]), {{WRAPPER}} .wpforms-form textarea, {{WRAPPER}} .wpforms-form select'=>'color:{{VALUE}}!important;'
            )
        ));

        $this->add_control('input_border',array(
            'label'=>'Alan Kenarlık Rengi',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .wpforms-form textarea, {{WRAPPER}} .wpforms-form select'=>'border-color:{{VALUE}}!important;'
            )
        ));

        $this->add_responsive_control('input_height',array(
            'label'=>'Alan Yüksekliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px'),
            'range'=>array('px'=>array('min'=>36,'max'=>90)),
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .wpforms-form select'=>'min-height:{{SIZE}}{{UNIT}};'
            )
        ));

        $this->add_responsive_control('input_radius',array(
            'label'=>'Alan Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>40)),
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-form input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]), {{WRAPPER}} .wpforms-form textarea, {{WRAPPER}} .wpforms-form select'=>'border-radius:{{SIZE}}px;'
            )
        ));

        $this->add_responsive_control('field_gap',array(
            'label'=>'Alanlar Arası Boşluk',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>60)),
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-field'=>'margin-bottom:{{SIZE}}px;'
            )
        ));

        $this->add_control('button_bg',array(
            'label'=>'Gönder Butonu Arka Plan',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-submit'=>'background:{{VALUE}} !important;'
            )
        ));

        $this->add_control('button_color',array(
            'label'=>'Gönder Butonu Yazı',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-submit'=>'color:{{VALUE}} !important;'
            )
        ));

        $this->add_responsive_control('button_radius',array(
            'label'=>'Gönder Butonu Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>50)),
            'selectors'=>array(
                '{{WRAPPER}} .wpforms-submit'=>'border-radius:{{SIZE}}px !important;'
            )
        ));

        $this->add_responsive_control('textarea_height',array(
            'label'=>'Mesaj Alanı Yüksekliği',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'size_units'=>array('px'),
            'range'=>array('px'=>array('min'=>100,'max'=>420)),
            'default'=>array('unit'=>'px','size'=>160),
            'selectors'=>array(
                '{{WRAPPER}} .wpst-wpforms-shell .wpforms-field-textarea textarea'=>'min-height:{{SIZE}}{{UNIT}};'
            )
        ));

        $this->add_control('button_full',array(
            'label'=>'Buton Tam Genişlik',
            'type'=>\Elementor\Controls_Manager::SWITCHER,
            'return_value'=>'yes',
            'default'=>'',
            'prefix_class'=>'wpst-wpforms-button-full-'
        ));

        $this->add_control('shell_bg',array(
            'label'=>'Kart Arka Planı',
            'type'=>\Elementor\Controls_Manager::COLOR,
            'selectors'=>array('{{WRAPPER}} .wpst-wpforms-shell'=>'--wpst-form-shell-bg:{{VALUE}};')
        ));
        $this->add_responsive_control('shell_padding',array(
            'label'=>'Kart İç Boşluk',
            'type'=>\Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=>array('px'),
            'selectors'=>array('{{WRAPPER}} .wpst-wpforms-shell'=>'padding:{{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;')
        ));
        $this->add_responsive_control('shell_radius',array(
            'label'=>'Kart Köşe',
            'type'=>\Elementor\Controls_Manager::SLIDER,
            'range'=>array('px'=>array('min'=>0,'max'=>50)),
            'default'=>array('size'=>20),
            'selectors'=>array('{{WRAPPER}} .wpst-wpforms-shell'=>'border-radius:{{SIZE}}px;')
        ));

        $this->end_controls_section();

        $this->standard_responsive_controls();
    }

    protected function render(){
        $s=$this->get_settings_for_display();
        $id=absint(!empty($s['form_id'])?$s['form_id']:$s['manual_form_id']);

        echo '<div class="wpst-wpforms-shell">';

        if($id && shortcode_exists('wpforms')){
            $shortcode='[wpforms id="'.$id.'" title="'.('yes'===$s['show_title']?'true':'false').'" description="'.('yes'===$s['show_description']?'true':'false').'"]';
            echo do_shortcode($shortcode);
        }else{
            echo '<div class="wpst-wpforms-placeholder">';
            echo '<span>WPForms</span>';
            echo '<strong>'.esc_html($s['empty_title']).'</strong>';
            echo '<p>'.esc_html($s['empty_text']).'</p>';
            if(!shortcode_exists('wpforms')){
                echo '<small>'.esc_html($s['missing_plugin_text']).'</small>';
            }
            echo '</div>';
        }

        echo '</div>';
    }
}
