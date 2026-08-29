<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WPST_Widget_Advanced_Table extends WPST_Elementor_Widget_Base {
 public function get_name(){ return 'wpsoft-advanced-table'; }
 public function get_title(){ return 'WPSoft · Advanced Table 2.0'; }
 public function get_icon(){ return 'eicon-table'; }
 public function get_keywords(){ return array('table','comparison','pricing','features','data','wpsoft'); }
 protected function register_controls(){
  $this->start_controls_section('content',array('label'=>'Tablo'));
  $this->wpst_signature_preset_control();
  $this->add_control('caption',array('label'=>'Başlık','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Paket Karşılaştırması','dynamic'=>array('active'=>true)));
  $this->add_control('headers',array('label'=>'Sütun Başlıkları','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Özellik|Starter|Professional','description'=>'Sütunları | ile ayırın.'));
  $this->add_control('rows',array('label'=>'Satırlar','type'=>\Elementor\Controls_Manager::TEXTAREA,'rows'=>8,'default'=>"Kurulum|Dahil|Dahil\nResponsive|Dahil|Dahil\nDestek|E-posta|Öncelikli\nFiyat|₺4.900|₺9.900",'description'=>'Her satırı yeni satıra, hücreleri | ile ayırın.'));
  $this->add_control('layout',array('label'=>'Görünüm','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'cards','options'=>array('cards'=>'Modern Table','minimal'=>'Minimal','striped'=>'Striped','comparison'=>'Comparison'),'prefix_class'=>'wpst-table-layout-'));
  $this->add_control('highlight_column',array('label'=>'Öne Çıkan Sütun','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>2,'min'=>0,'max'=>8,'description'=>'0 = kapalı. İlk sütun 1.'));
  $this->add_control('mobile_mode',array('label'=>'Mobil Davranış','type'=>\Elementor\Controls_Manager::SELECT,'default'=>'scroll','options'=>array('scroll'=>'Yatay Kaydır','stack'=>'Kartlara Dönüştür'),'prefix_class'=>'wpst-table-mobile-'));
  $this->add_control('sticky_header',array('label'=>'Başlık Satırı Yapışkan','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'','prefix_class'=>'wpst-table-sticky-'));
  $this->end_controls_section();

  $this->start_controls_section('style',array('label'=>'Tablo Biçimi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_control('accent',array('label'=>'Vurgu','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-accent:{{VALUE}};')));
  $this->add_control('surface',array('label'=>'Tablo Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-surface:{{VALUE}};')));
  $this->add_control('header_bg',array('label'=>'Başlık Satırı Arka Plan','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-head-bg:{{VALUE}};')));
  $this->add_control('header_text',array('label'=>'Başlık Satırı Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-head-text:{{VALUE}};')));
  $this->add_control('body_text',array('label'=>'Hücre Yazı Rengi','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-text:{{VALUE}};')));
  $this->add_control('first_col_text',array('label'=>'İlk Sütun Yazı','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-strong:{{VALUE}};')));
  $this->add_control('border',array('label'=>'Kenarlık','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-border:{{VALUE}};')));
  $this->add_control('row_hover',array('label'=>'Satır Hover','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>array('{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-row-hover:{{VALUE}};')));
  $this->add_responsive_control('cell_padding',array('label'=>'Hücre Boşluğu','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>8,'max'=>36)),'default'=>array('size'=>16),'selectors'=>array('{{WRAPPER}} .wpst-advanced-table th,{{WRAPPER}} .wpst-advanced-table td'=>'padding:{{SIZE}}px;')));
  $this->add_responsive_control('radius',array('label'=>'Tablo Radius','type'=>\Elementor\Controls_Manager::SLIDER,'range'=>array('px'=>array('min'=>0,'max'=>50)),'default'=>array('size'=>20),'selectors'=>array('{{WRAPPER}} .wpst-advanced-table-wrap,{{WRAPPER}} .wpst-advanced-table'=>'--wpst-table-radius:{{SIZE}}px;')));
  $this->end_controls_section();

  $this->start_controls_section('typography',array('label'=>'Tipografi','tab'=>\Elementor\Controls_Manager::TAB_STYLE));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'caption_typography','label'=>'Tablo Başlığı','selector'=>'{{WRAPPER}} .wpst-advanced-table caption'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'header_typography','label'=>'Sütun Başlıkları','selector'=>'{{WRAPPER}} .wpst-advanced-table thead th'));
  $this->add_group_control(\Elementor\Group_Control_Typography::get_type(),array('name'=>'body_typography','label'=>'Hücreler','selector'=>'{{WRAPPER}} .wpst-advanced-table tbody'));
  $this->end_controls_section();
  $this->standard_responsive_controls();
 }
 private function cells($line){ return array_map('trim',explode('|',(string)$line)); }
 protected function render(){
  $s=$this->get_settings_for_display();
  $heads=$this->cells($s['headers']);
  $lines=preg_split('/\r\n|\r|\n/',(string)$s['rows']);
  $highlight=absint($s['highlight_column']);
  echo '<div class="wpst-advanced-table-wrap" role="region" aria-label="'.esc_attr($s['caption']?:'Tablo').'" tabindex="0"><table class="wpst-advanced-table">';
  if(trim((string)$s['caption'])!=='') echo '<caption>'.esc_html($s['caption']).'</caption>';
  if($heads){ echo '<thead><tr>'; foreach($heads as $i=>$h) echo '<th scope="col"'.(($i+1)===$highlight?' class="is-highlight"':'').'>'.esc_html($h).'</th>'; echo '</tr></thead>'; }
  echo '<tbody>';
  foreach($lines as $line){
   if(trim($line)==='') continue;
   $cells=$this->cells($line);
   echo '<tr>';
   foreach($heads as $i=>$head){
    $val=isset($cells[$i])?$cells[$i]:'';
    $tag=0===$i?'th':'td';
    $scope=0===$i?' scope="row"':'';
    $hl=(($i+1)===$highlight)?' class="is-highlight"':'';
    echo '<'.$tag.$scope.$hl.' data-label="'.esc_attr($head).'">'.esc_html($val).'</'.$tag.'>';
   }
   echo '</tr>';
  }
  echo '</tbody></table></div>';
 }
}
