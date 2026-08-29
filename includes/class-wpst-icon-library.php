<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPSoft native SVG icon library.
 * All icons are bundled with the plugin and inherit currentColor.
 */
final class WPST_Icon_Library {
    const VIEWBOX = '0 0 24 24';

    public static function init() {
        add_action('admin_menu',array(__CLASS__,'menu'),29);
    }

    public static function menu() {
        add_submenu_page(
            'wpsoft-site-tools',
            'WPSoft Icon & SVG Library',
            'Icon & SVG Library',
            'manage_options',
            'wpsoft-icon-library',
            array(__CLASS__,'page')
        );
    }

    public static function icons() {
        return array(
            'arrow-right'=>array('label'=>'Ok Sağ','category'=>'Arayüz','body'=>'<path d="M5 12h13"/><path d="m14 7 5 5-5 5"/>'),
            'arrow-up-right'=>array('label'=>'Ok Sağ Üst','category'=>'Arayüz','body'=>'<path d="M7 17 17 7"/><path d="M9 7h8v8"/>'),
            'chevron-right'=>array('label'=>'Chevron Sağ','category'=>'Arayüz','body'=>'<path d="m9 6 6 6-6 6"/>'),
            'chevron-down'=>array('label'=>'Chevron Aşağı','category'=>'Arayüz','body'=>'<path d="m6 9 6 6 6-6"/>'),
            'check'=>array('label'=>'Onay','category'=>'Arayüz','body'=>'<path d="m5 12 4 4 10-10"/>'),
            'check-circle'=>array('label'=>'Onay Daire','category'=>'Arayüz','body'=>'<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>'),
            'plus'=>array('label'=>'Artı','category'=>'Arayüz','body'=>'<path d="M12 5v14M5 12h14"/>'),
            'minus'=>array('label'=>'Eksi','category'=>'Arayüz','body'=>'<path d="M5 12h14"/>'),
            'menu'=>array('label'=>'Menü','category'=>'Arayüz','body'=>'<path d="M4 7h16M4 12h16M4 17h16"/>'),
            'close'=>array('label'=>'Kapat','category'=>'Arayüz','body'=>'<path d="m6 6 12 12M18 6 6 18"/>'),
            'search'=>array('label'=>'Arama','category'=>'Arayüz','body'=>'<circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 4 4"/>'),
            'filter'=>array('label'=>'Filtre','category'=>'Arayüz','body'=>'<path d="M4 6h16M7 12h10M10 18h4"/>'),
            'eye'=>array('label'=>'Göz','category'=>'Arayüz','body'=>'<path d="M3 12s3.2-5 9-5 9 5 9 5-3.2 5-9 5-9-5-9-5Z"/><circle cx="12" cy="12" r="2.5"/>'),
            'home'=>array('label'=>'Ana Sayfa','category'=>'Genel','body'=>'<path d="m3 11 9-7 9 7"/><path d="M5 10v10h14V10M9 20v-6h6v6"/>'),
            'user'=>array('label'=>'Kullanıcı','category'=>'Genel','body'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21c.8-4.2 3.5-6 8-6s7.2 1.8 8 6"/>'),
            'users'=>array('label'=>'Kullanıcılar','category'=>'Genel','body'=>'<circle cx="9" cy="8" r="3"/><path d="M3 20c.5-3.7 2.6-5.5 6-5.5S14.5 16.3 15 20"/><path d="M15 5.5c2.3.2 3.5 1.5 3.5 3.5S17.3 12.3 15 12.5M16 15c2.7.3 4.2 1.9 4.7 5"/>'),
            'heart'=>array('label'=>'Kalp','category'=>'Genel','body'=>'<path d="M20.5 9.5c0 5-8.5 10-8.5 10s-8.5-5-8.5-10A4.7 4.7 0 0 1 12 6.7a4.7 4.7 0 0 1 8.5 2.8Z"/>'),
            'star'=>array('label'=>'Yıldız','category'=>'Genel','body'=>'<path d="m12 3 2.7 5.5 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1-4.4-4.3 6.1-.9L12 3Z"/>'),
            'award'=>array('label'=>'Ödül','category'=>'Genel','body'=>'<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1 7 4.5-2.5 4.5 2.5-1-7"/>'),
            'sparkles'=>array('label'=>'Parıltı','category'=>'Genel','body'=>'<path d="M12 3c.7 4.1 2.9 6.3 7 7-4.1.7-6.3 2.9-7 7-.7-4.1-2.9-6.3-7-7 4.1-.7 6.3-2.9 7-7Z"/><path d="M19 3v4M21 5h-4"/>'),
            'bolt'=>array('label'=>'Hız','category'=>'İş','body'=>'<path d="m13 2-7 11h6l-1 9 7-12h-6l1-8Z"/>'),
            'rocket'=>array('label'=>'Roket','category'=>'İş','body'=>'<path d="M14 4c3-2 5-1 6-1 0 1 1 3-1 6l-6 6-5-5 6-6Z"/><path d="m8 10-4 1-2 3 6 1M13 15l1 6 3-2 1-4"/><circle cx="15.5" cy="7.5" r="1.5"/>'),
            'target'=>array('label'=>'Hedef','category'=>'İş','body'=>'<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>'),
            'chart'=>array('label'=>'Grafik','category'=>'İş','body'=>'<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'),
            'briefcase'=>array('label'=>'Çanta','category'=>'İş','body'=>'<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V4h6v3M3 12h18M10 12v2h4v-2"/>'),
            'building'=>array('label'=>'Bina','category'=>'İş','body'=>'<path d="M5 21V4h10v17M15 9h4v12M8 8h4M8 12h4M8 16h4"/>'),
            'layers'=>array('label'=>'Katmanlar','category'=>'Tasarım','body'=>'<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>'),
            'grid'=>array('label'=>'Grid','category'=>'Tasarım','body'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'),
            'palette'=>array('label'=>'Palet','category'=>'Tasarım','body'=>'<path d="M12 3a9 9 0 1 0 0 18h1.5a2 2 0 0 0 0-4H12a2 2 0 0 1 0-4h3a6 6 0 0 0 0-12h-3Z"/><circle cx="7.5" cy="9" r="1"/><circle cx="10" cy="6" r="1"/><circle cx="15" cy="6.5" r="1"/>'),
            'pen'=>array('label'=>'Kalem','category'=>'Tasarım','body'=>'<path d="m4 20 4.5-1 10-10-3.5-3.5-10 10L4 20Z"/><path d="m13.5 7 3.5 3.5"/>'),
            'monitor'=>array('label'=>'Monitör','category'=>'Teknoloji','body'=>'<rect x="2.5" y="4" width="19" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>'),
            'smartphone'=>array('label'=>'Telefon','category'=>'Teknoloji','body'=>'<rect x="6.5" y="2" width="11" height="20" rx="2"/><path d="M10 5h4M11 19h2"/>'),
            'code'=>array('label'=>'Kod','category'=>'Teknoloji','body'=>'<path d="m8 7-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/>'),
            'globe'=>array('label'=>'Dünya','category'=>'Teknoloji','body'=>'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>'),
            'settings'=>array('label'=>'Ayarlar','category'=>'Teknoloji','body'=>'<circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6 7 7M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/>'),
            'shield'=>array('label'=>'Kalkan','category'=>'Güvenlik','body'=>'<path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="m8 12 2.5 2.5L16 9"/>'),
            'lock'=>array('label'=>'Kilit','category'=>'Güvenlik','body'=>'<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>'),
            'phone'=>array('label'=>'Telefon','category'=>'İletişim','body'=>'<path d="M7 3h3l1.5 5-2 1.5a15 15 0 0 0 5 5L16 12.5l5 1.5v3c0 2-1.5 4-4 4C9 20 4 15 3 7c0-2.5 2-4 4-4Z"/>'),
            'mail'=>array('label'=>'E-posta','category'=>'İletişim','body'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>'),
            'map-pin'=>array('label'=>'Konum','category'=>'İletişim','body'=>'<path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/>'),
            'clock'=>array('label'=>'Saat','category'=>'İletişim','body'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'),
            'calendar'=>array('label'=>'Takvim','category'=>'İletişim','body'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/>'),
            'message'=>array('label'=>'Mesaj','category'=>'İletişim','body'=>'<path d="M4 5h16v12H9l-5 4V5Z"/><path d="M8 9h8M8 13h5"/>'),
            'cart'=>array('label'=>'Sepet','category'=>'E-Ticaret','body'=>'<path d="M3 4h2l2.5 11h10L20 8H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>'),
            'bag'=>array('label'=>'Çanta','category'=>'E-Ticaret','body'=>'<path d="M5 8h14l1 13H4L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>'),
            'package'=>array('label'=>'Paket','category'=>'E-Ticaret','body'=>'<path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/>'),
            'truck'=>array('label'=>'Kargo','category'=>'E-Ticaret','body'=>'<path d="M3 6h11v11H3V6Z"/><path d="M14 10h4l3 4v3h-7v-7Z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>'),
            'tag'=>array('label'=>'Etiket','category'=>'E-Ticaret','body'=>'<path d="m3 12 9-9h7l2 2v7l-9 9-9-9Z"/><circle cx="16.5" cy="7.5" r="1.5"/>'),
            'camera'=>array('label'=>'Kamera','category'=>'Medya','body'=>'<path d="M4 7h4l2-2h4l2 2h4v12H4V7Z"/><circle cx="12" cy="13" r="4"/>'),
            'image'=>array('label'=>'Görsel','category'=>'Medya','body'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8" cy="9" r="2"/><path d="m4 18 5-5 3 3 2-2 6 6"/>'),
            'play'=>array('label'=>'Oynat','category'=>'Medya','body'=>'<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4V8Z"/>'),
            'video'=>array('label'=>'Video','category'=>'Medya','body'=>'<rect x="3" y="6" width="13" height="12" rx="2"/><path d="m16 10 5-3v10l-5-3v-4Z"/>'),
            'link'=>array('label'=>'Bağlantı','category'=>'Medya','body'=>'<path d="M10 14 8.5 15.5a4 4 0 0 1-5.5-5.8L6 6.8a4 4 0 0 1 5.5 0"/><path d="m14 10 1.5-1.5a4 4 0 0 1 5.5 5.8L18 17.2a4 4 0 0 1-5.5 0"/>'),
            'download'=>array('label'=>'İndir','category'=>'Dosya','body'=>'<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>'),
            'upload'=>array('label'=>'Yükle','category'=>'Dosya','body'=>'<path d="M12 21V9M7 14l5-5 5 5M4 3h16"/>'),
            'wrench'=>array('label'=>'Anahtar','category'=>'Araçlar','body'=>'<path d="M14 6a5 5 0 0 0-7-2l3 3-3 3-3-3a5 5 0 0 0 6 7l7 7 4-4-7-7a5 5 0 0 0 0-4Z"/>'),
            'tool'=>array('label'=>'Araç','category'=>'Araçlar','body'=>'<path d="m4 20 8-8M13 5l6-2 2 2-2 6-3-3-4 4-3-3 4-4Z"/>'),
            'quote'=>array('label'=>'Alıntı','category'=>'İçerik','body'=>'<path d="M5 8h6v6H7c0 2-1 4-3 5 1-2 1-4 1-5V8ZM14 8h6v6h-4c0 2-1 4-3 5 1-2 1-4 1-5V8Z"/>'),
            'faq'=>array('label'=>'Soru','category'=>'İçerik','body'=>'<circle cx="12" cy="12" r="9"/><path d="M9.8 9a2.4 2.4 0 1 1 3.3 2.2c-.8.4-1.1.8-1.1 1.8M12 17h.01"/>'),

            'arrow-left'=>array('label'=>'Ok Sol','category'=>'Arayüz','body'=>'<path d="M19 12H6"/><path d="m10 7-5 5 5 5"/>'),
            'arrow-up'=>array('label'=>'Ok Yukarı','category'=>'Arayüz','body'=>'<path d="M12 19V6"/><path d="m7 10 5-5 5 5"/>'),
            'arrow-down'=>array('label'=>'Ok Aşağı','category'=>'Arayüz','body'=>'<path d="M12 5v13"/><path d="m7 14 5 5 5-5"/>'),
            'chevron-left'=>array('label'=>'Chevron Sol','category'=>'Arayüz','body'=>'<path d="m15 6-6 6 6 6"/>'),
            'chevrons-right'=>array('label'=>'Çift Chevron Sağ','category'=>'Arayüz','body'=>'<path d="m7 6 6 6-6 6M13 6l6 6-6 6"/>'),
            'external-link'=>array('label'=>'Harici Bağlantı','category'=>'Arayüz','body'=>'<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 13v7H4V6h7"/>'),
            'refresh'=>array('label'=>'Yenile','category'=>'Arayüz','body'=>'<path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M18 9a7 7 0 0 0-11-2L4 10M6 15a7 7 0 0 0 11 2l3-3"/>'),
            'copy'=>array('label'=>'Kopyala','category'=>'Arayüz','body'=>'<rect x="8" y="8" width="11" height="11" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/>'),
            'trash'=>array('label'=>'Sil','category'=>'Arayüz','body'=>'<path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14M10 11v6M14 11v6"/>'),
            'edit'=>array('label'=>'Düzenle','category'=>'Arayüz','body'=>'<path d="m4 20 4.5-1 10-10-3.5-3.5-10 10L4 20Z"/><path d="m13.5 7 3.5 3.5"/>'),
            'more-horizontal'=>array('label'=>'Daha Fazla','category'=>'Arayüz','body'=>'<circle cx="5" cy="12" r="1.3"/><circle cx="12" cy="12" r="1.3"/><circle cx="19" cy="12" r="1.3"/>'),
            'more-vertical'=>array('label'=>'Daha Fazla Dikey','category'=>'Arayüz','body'=>'<circle cx="12" cy="5" r="1.3"/><circle cx="12" cy="12" r="1.3"/><circle cx="12" cy="19" r="1.3"/>'),
            'sliders'=>array('label'=>'Kontroller','category'=>'Arayüz','body'=>'<path d="M4 7h10M18 7h2M4 17h2M10 17h10M14 4v6M8 14v6"/>'),
            'maximize'=>array('label'=>'Tam Ekran','category'=>'Arayüz','body'=>'<path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/>'),
            'minimize'=>array('label'=>'Küçült','category'=>'Arayüz','body'=>'<path d="M8 8H3V3M16 8h5V3M8 16H3v5M16 16h5v5"/>'),
            'info'=>array('label'=>'Bilgi','category'=>'Genel','body'=>'<circle cx="12" cy="12" r="9"/><path d="M12 10v6M12 7h.01"/>'),
            'help-circle'=>array('label'=>'Yardım','category'=>'Genel','body'=>'<circle cx="12" cy="12" r="9"/><path d="M9.8 9a2.4 2.4 0 1 1 3.3 2.2c-.8.4-1.1.8-1.1 1.8M12 17h.01"/>'),
            'bell'=>array('label'=>'Bildirim','category'=>'Genel','body'=>'<path d="M6 9a6 6 0 0 1 12 0c0 5 2 6 2 6H4s2-1 2-6"/><path d="M10 19h4"/>'),
            'bookmark'=>array('label'=>'Yer İmi','category'=>'Genel','body'=>'<path d="M6 4h12v17l-6-4-6 4V4Z"/>'),
            'flag'=>array('label'=>'Bayrak','category'=>'Genel','body'=>'<path d="M5 21V4M5 5h10l-2 4 2 4H5"/>'),
            'location'=>array('label'=>'Konum','category'=>'Genel','body'=>'<path d="M12 21s7-6 7-12a7 7 0 1 0-14 0c0 6 7 12 7 12Z"/><circle cx="12" cy="9" r="2.3"/>'),
            'compass'=>array('label'=>'Pusula','category'=>'Genel','body'=>'<circle cx="12" cy="12" r="9"/><path d="m15 9-2 5-5 2 2-5 5-2Z"/>'),
            'wifi'=>array('label'=>'Wi-Fi','category'=>'Teknoloji','body'=>'<path d="M4 9a12 12 0 0 1 16 0M7 12a7.5 7.5 0 0 1 10 0M10 15a3 3 0 0 1 4 0"/><circle cx="12" cy="19" r="1"/>'),
            'database'=>array('label'=>'Veritabanı','category'=>'Teknoloji','body'=>'<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/>'),
            'server'=>array('label'=>'Sunucu','category'=>'Teknoloji','body'=>'<rect x="4" y="3" width="16" height="7" rx="2"/><rect x="4" y="14" width="16" height="7" rx="2"/><circle cx="8" cy="6.5" r=".8"/><circle cx="8" cy="17.5" r=".8"/>'),
            'cpu'=>array('label'=>'İşlemci','category'=>'Teknoloji','body'=>'<rect x="7" y="7" width="10" height="10" rx="2"/><rect x="10" y="10" width="4" height="4"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>'),
            'cloud'=>array('label'=>'Bulut','category'=>'Teknoloji','body'=>'<path d="M7 18h10a4 4 0 0 0 .8-7.9A6 6 0 0 0 6.3 8.5 4.5 4.5 0 0 0 7 18Z"/>'),
            'zap'=>array('label'=>'Enerji','category'=>'Teknoloji','body'=>'<path d="m13 2-7 11h6l-1 9 7-12h-6l1-8Z"/>'),
            'fingerprint'=>array('label'=>'Parmak İzi','category'=>'Güvenlik','body'=>'<path d="M8 10a4 4 0 0 1 8 0c0 5-1 8-2 10M5 10a7 7 0 0 1 14 0c0 4-.5 7-1.5 10M11 13c0 3-.3 5-1 7M8 13c0 2-.2 4-.8 5.8"/>'),
            'key'=>array('label'=>'Anahtar','category'=>'Güvenlik','body'=>'<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8M16 7l2 2M14 9l2 2"/>'),
            'unlock'=>array('label'=>'Kilidi Aç','category'=>'Güvenlik','body'=>'<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M9 10V7a4 4 0 0 1 7-2"/>'),
            'shield-alert'=>array('label'=>'Uyarı Kalkanı','category'=>'Güvenlik','body'=>'<path d="M12 3 20 6v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6l8-3Z"/><path d="M12 8v5M12 16h.01"/>'),
            'credit-card'=>array('label'=>'Kredi Kartı','category'=>'E-Ticaret','body'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/>'),
            'receipt'=>array('label'=>'Fiş','category'=>'E-Ticaret','body'=>'<path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"/><path d="M9 8h6M9 12h6"/>'),
            'percent'=>array('label'=>'Yüzde','category'=>'E-Ticaret','body'=>'<path d="m7 17 10-10"/><circle cx="8" cy="8" r="2"/><circle cx="16" cy="16" r="2"/>'),
            'gift'=>array('label'=>'Hediye','category'=>'E-Ticaret','body'=>'<rect x="3" y="9" width="18" height="12" rx="2"/><path d="M12 9v12M3 13h18"/><path d="M12 9H8.5A2.5 2.5 0 1 1 11 6.5L12 9Zm0 0h3.5A2.5 2.5 0 1 0 13 6.5L12 9Z"/>'),
            'store'=>array('label'=>'Mağaza','category'=>'E-Ticaret','body'=>'<path d="M4 10v10h16V10M3 10l2-6h14l2 6"/><path d="M3 10c0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0 0 2 3 2 3 0"/>'),
            'wallet'=>array('label'=>'Cüzdan','category'=>'E-Ticaret','body'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M15 10h6v4h-6a2 2 0 0 1 0-4Z"/>'),
            'headphones'=>array('label'=>'Kulaklık','category'=>'İletişim','body'=>'<path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h4v6H6a2 2 0 0 1-2-2v-4ZM20 14h-4v6h2a2 2 0 0 0 2-2v-4Z"/>'),
            'send'=>array('label'=>'Gönder','category'=>'İletişim','body'=>'<path d="m3 11 18-8-8 18-2-7-8-3Z"/><path d="m11 14 4-4"/>'),
            'at-sign'=>array('label'=>'@ İşareti','category'=>'İletişim','body'=>'<circle cx="12" cy="12" r="8"/><path d="M16 12v-2a4 4 0 1 0-1.2 2.8C16 14 19 14 20 12"/>'),
            'mic'=>array('label'=>'Mikrofon','category'=>'Medya','body'=>'<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3M9 21h6"/>'),
            'music'=>array('label'=>'Müzik','category'=>'Medya','body'=>'<path d="M9 18V5l10-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="16" cy="16" r="3"/>'),
            'volume'=>array('label'=>'Ses','category'=>'Medya','body'=>'<path d="M5 10h4l5-4v12l-5-4H5v-4Z"/><path d="M17 9a4 4 0 0 1 0 6M19 6a8 8 0 0 1 0 12"/>'),
            'pause'=>array('label'=>'Duraklat','category'=>'Medya','body'=>'<path d="M9 7v10M15 7v10"/>'),
            'skip-forward'=>array('label'=>'Sonraki','category'=>'Medya','body'=>'<path d="m6 5 8 7-8 7V5ZM17 5v14"/>'),
            'skip-back'=>array('label'=>'Önceki','category'=>'Medya','body'=>'<path d="m18 5-8 7 8 7V5ZM7 5v14"/>'),
            'folder'=>array('label'=>'Klasör','category'=>'Dosya','body'=>'<path d="M3 6h7l2 2h9v11H3V6Z"/>'),
            'file'=>array('label'=>'Dosya','category'=>'Dosya','body'=>'<path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v5h5"/>'),
            'file-text'=>array('label'=>'Metin Dosyası','category'=>'Dosya','body'=>'<path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>'),
            'archive'=>array('label'=>'Arşiv','category'=>'Dosya','body'=>'<path d="M4 7h16v13H4V7ZM3 3h18v4H3V3Z"/><path d="M9 11h6"/>'),
            'printer'=>array('label'=>'Yazıcı','category'=>'Dosya','body'=>'<path d="M7 8V3h10v5M7 17h10v4H7v-4Z"/><path d="M5 8h14a2 2 0 0 1 2 2v7h-4v-4H7v4H3v-7a2 2 0 0 1 2-2Z"/>'),
            'scissors'=>array('label'=>'Makas','category'=>'Araçlar','body'=>'<circle cx="6" cy="7" r="3"/><circle cx="6" cy="17" r="3"/><path d="m8.5 8.5 11 8.5M8.5 15.5 19 7"/>'),
            'hammer'=>array('label'=>'Çekiç','category'=>'Araçlar','body'=>'<path d="m4 20 8-8M10 6l4-4 6 6-4 4-6-6Z"/>'),
            'ruler'=>array('label'=>'Cetvel','category'=>'Araçlar','body'=>'<path d="m4 17 13-13 3 3L7 20l-3-3Z"/><path d="m10 11 2 2M13 8l2 2M7 14l2 2"/>'),
            'lightbulb'=>array('label'=>'Fikir','category'=>'İçerik','body'=>'<path d="M9 18h6M10 21h4"/><path d="M8 14a6 6 0 1 1 8 0c-1 1-1 2-1 2H9s0-1-1-2Z"/>'),
            'book-open'=>array('label'=>'Kitap','category'=>'İçerik','body'=>'<path d="M3 5h7a2 2 0 0 1 2 2v12a2 2 0 0 0-2-2H3V5ZM21 5h-7a2 2 0 0 0-2 2v12a2 2 0 0 1 2-2h7V5Z"/>'),
            'clipboard'=>array('label'=>'Pano','category'=>'İçerik','body'=>'<rect x="5" y="5" width="14" height="16" rx="2"/><path d="M9 5V3h6v2M9 10h6M9 14h6"/>'),
            'list'=>array('label'=>'Liste','category'=>'İçerik','body'=>'<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>'),
            'layout'=>array('label'=>'Yerleşim','category'=>'Tasarım','body'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 9v12"/>'),
            'columns'=>array('label'=>'Kolonlar','category'=>'Tasarım','body'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M10 4v16M15 4v16"/>'),
            'move'=>array('label'=>'Taşı','category'=>'Tasarım','body'=>'<path d="M12 2v20M2 12h20M12 2l-3 3M12 2l3 3M12 22l-3-3M12 22l3-3M2 12l3-3M2 12l3 3M22 12l-3-3M22 12l-3 3"/>'),
            'crop'=>array('label'=>'Kırp','category'=>'Tasarım','body'=>'<path d="M6 2v14a2 2 0 0 0 2 2h14M2 6h14a2 2 0 0 1 2 2v14"/>'),
            'circle'=>array('label'=>'Daire','category'=>'Şekiller','body'=>'<circle cx="12" cy="12" r="8"/>'),
            'square'=>array('label'=>'Kare','category'=>'Şekiller','body'=>'<rect x="5" y="5" width="14" height="14" rx="2"/>'),
            'triangle'=>array('label'=>'Üçgen','category'=>'Şekiller','body'=>'<path d="m12 4 8 15H4L12 4Z"/>'),
            'hexagon'=>array('label'=>'Altıgen','category'=>'Şekiller','body'=>'<path d="m8 3 8 0 5 9-5 9H8l-5-9 5-9Z"/>'),
        );
    }

    public static function categories() {
        $out=array();
        foreach(self::icons() as $slug=>$icon){
            $cat=$icon['category'];
            if(!isset($out[$cat]))$out[$cat]=array();
            $out[$cat][$slug]=$icon['label'];
        }
        return $out;
    }

    public static function options($grouped=false) {
        if($grouped)return self::categories();
        $out=array();
        foreach(self::icons() as $slug=>$icon)$out[$slug]=$icon['label'];
        return $out;
    }

    public static function exists($slug) {
        $icons=self::icons();
        return isset($icons[$slug]);
    }

    public static function svg($slug,$args=array()) {
        $icons=self::icons();
        if(!isset($icons[$slug]))$slug='sparkles';
        $icon=$icons[$slug];
        $size=isset($args['size'])?max(8,min(256,(int)$args['size'])):24;
        $class=isset($args['class'])?sanitize_html_class($args['class']):'wpst-icon';
        $label=isset($args['label'])?sanitize_text_field($args['label']):'';
        $aria=$label!==''?' role="img" aria-label="'.esc_attr($label).'"':' aria-hidden="true" focusable="false"';
        return '<svg class="'.esc_attr($class).' wpst-icon-svg wpst-icon-'.esc_attr($slug).'" width="'.esc_attr($size).'" height="'.esc_attr($size).'" viewBox="'.self::VIEWBOX.'" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"'.$aria.'>'.$icon['body'].'</svg>';
    }

    public static function render($slug,$args=array()) {
        echo self::svg($slug,$args); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted internal SVG registry.
    }

    public static function elementor_control($widget,$id='wpst_icon',$args=array()) {
        if(!$widget || !method_exists($widget,'add_control'))return;
        $defaults=array(
            'label'=>'WPSoft İkon',
            'type'=>\Elementor\Controls_Manager::SELECT2,
            'options'=>self::options(),
            'default'=>'sparkles',
            'label_block'=>true,
        );
        $widget->add_control($id,array_merge($defaults,$args));
    }

    public static function page() {
        if(!current_user_can('manage_options'))return;
        $icons=self::icons();
        $categories=self::categories();
        $shapes=class_exists('WPST_SVG_Library')?WPST_SVG_Library::options():array();
        ?>
        <div class="wrap wpst-icon-library-page">
            <div class="wpst-il-head">
                <div>
                    <span class="wpst-il-kicker">WPSOFT DESIGN SYSTEM</span>
                    <h1>Icon & SVG Library</h1>
                    <p>Widget, bölüm ve şablonlarda kullanılacak yerleşik WPSoft görsel kütüphanesi.</p>
                </div>
                <div class="wpst-il-stats">
                    <span><b><?php echo absint(count($icons)); ?></b> Icon</span>
                    <span><b><?php echo absint(count($shapes)); ?></b> SVG Shape</span>
                </div>
            </div>

            <div class="wpst-il-toolbar">
                <div class="wpst-il-tabs">
                    <button type="button" class="is-active" data-il-tab="icons">Iconlar</button>
                    <button type="button" data-il-tab="shapes">SVG Shapes</button>
                </div>
                <label class="wpst-il-search"><span class="dashicons dashicons-search"></span><input type="search" placeholder="Icon veya kategori ara..." data-il-search></label>
            </div>

            <section class="wpst-il-pane is-active" data-il-pane="icons">
                <?php foreach($categories as $category=>$items): ?>
                    <div class="wpst-il-group" data-il-group="<?php echo esc_attr(strtolower($category)); ?>">
                        <h2><?php echo esc_html($category); ?><small><?php echo absint(count($items)); ?></small></h2>
                        <div class="wpst-il-grid">
                            <?php foreach($items as $slug=>$label): ?>
                                <button type="button" class="wpst-il-card" data-il-item="<?php echo esc_attr(strtolower($slug.' '.$label.' '.$category)); ?>" data-copy="<?php echo esc_attr($slug); ?>">
                                    <i><?php echo self::svg($slug,array('size'=>28)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></i>
                                    <strong><?php echo esc_html($label); ?></strong>
                                    <code><?php echo esc_html($slug); ?></code>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="wpst-il-pane" data-il-pane="shapes">
                <div class="wpst-il-shape-grid">
                    <?php foreach($shapes as $slug=>$label): ?>
                        <button type="button" class="wpst-il-shape-card" data-il-item="<?php echo esc_attr(strtolower($slug.' '.$label)); ?>" data-copy="<?php echo esc_attr($slug); ?>">
                            <span><?php echo WPST_SVG_Library::inline($slug,array('class'=>'wpst-il-shape')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            <strong><?php echo esc_html($label); ?></strong>
                            <code><?php echo esc_html($slug); ?></code>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>
            <div class="wpst-il-toast" aria-live="polite">Kopyalandı</div>
        </div>
        <style>
        .wpst-icon-library-page{max-width:1440px;margin:28px 26px 40px 8px}
        .wpst-il-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px;padding:26px 28px;border:1px solid #e2e8f0;border-radius:20px;background:#fff}
        .wpst-il-kicker{font-size:10px;font-weight:800;letter-spacing:.12em;color:#2563eb}.wpst-il-head h1{margin:6px 0;font-size:30px;letter-spacing:-.04em}.wpst-il-head p{margin:0;color:#64748b}
        .wpst-il-stats{display:flex;gap:8px}.wpst-il-stats span{min-width:96px;padding:12px 14px;border-radius:12px;background:#f8fafc;color:#64748b;font-size:11px}.wpst-il-stats b{display:block;color:#0f172a;font-size:20px}
        .wpst-il-toolbar{position:sticky;top:32px;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:16px;margin:14px 0;padding:8px;border:1px solid #e2e8f0;border-radius:14px;background:rgba(255,255,255,.96);backdrop-filter:blur(14px)}
        .wpst-il-tabs{display:flex;gap:4px;padding:3px;border-radius:10px;background:#f1f5f9}.wpst-il-tabs button{min-height:36px;padding:0 14px;border:0;border-radius:8px;background:transparent;font-weight:700;color:#64748b;cursor:pointer}.wpst-il-tabs button.is-active{background:#fff;color:#1d4ed8;box-shadow:0 3px 10px rgba(15,23,42,.08)}
        .wpst-il-search{position:relative;width:min(360px,46vw)}.wpst-il-search span{position:absolute;left:11px;top:10px;color:#94a3b8}.wpst-il-search input{width:100%;min-height:38px;padding-left:34px;border:1px solid #dfe6ee;border-radius:10px;background:#f8fafc}
        .wpst-il-pane{display:none}.wpst-il-pane.is-active{display:block}.wpst-il-group{margin:18px 0}.wpst-il-group h2{display:flex;align-items:center;gap:8px;margin:0 0 9px;font-size:14px}.wpst-il-group h2 small{padding:2px 6px;border-radius:999px;background:#e2e8f0;color:#64748b;font-size:9px}
        .wpst-il-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px}.wpst-il-card,.wpst-il-shape-card{min-width:0;padding:14px;border:1px solid #e2e8f0;border-radius:13px;background:#fff;text-align:left;cursor:pointer;transition:.18s ease}.wpst-il-card:hover,.wpst-il-shape-card:hover{transform:translateY(-2px);border-color:#bfdbfe;box-shadow:0 10px 26px rgba(15,23,42,.07)}
        .wpst-il-card i{width:42px;height:42px;display:grid;place-items:center;margin-bottom:10px;border-radius:11px;background:#eff6ff;color:#2563eb}.wpst-il-card strong,.wpst-il-shape-card strong{display:block;font-size:12px;color:#0f172a}.wpst-il-card code,.wpst-il-shape-card code{display:block;margin-top:4px;padding:0;background:none;color:#94a3b8;font-size:9px}
        .wpst-il-shape-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px}.wpst-il-shape-card span{height:150px;display:grid;place-items:center;margin-bottom:12px;border-radius:10px;background:#f8fafc;color:#2563eb;overflow:hidden}.wpst-il-shape{width:128px;height:128px;fill:none;stroke:currentColor}.wpst-il-shape-card:nth-child(-n+4) .wpst-il-shape{fill:currentColor;stroke:none;opacity:.2}
        .wpst-il-toast{position:fixed;right:24px;bottom:24px;padding:10px 14px;border-radius:10px;background:#0f172a;color:#fff;opacity:0;transform:translateY(8px);pointer-events:none;transition:.2s}.wpst-il-toast.is-show{opacity:1;transform:none}
        @media(max-width:782px){.wpst-il-head{align-items:flex-start;flex-direction:column}.wpst-il-toolbar{top:46px;align-items:stretch;flex-direction:column}.wpst-il-search{width:100%}.wpst-il-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.wpst-il-shape-grid{grid-template-columns:1fr 1fr}}
        </style>
        <script>
        document.addEventListener('DOMContentLoaded',function(){
            var page=document.querySelector('.wpst-icon-library-page');if(!page)return;
            var tabs=page.querySelectorAll('[data-il-tab]'),panes=page.querySelectorAll('[data-il-pane]'),search=page.querySelector('[data-il-search]'),toast=page.querySelector('.wpst-il-toast');
            tabs.forEach(function(btn){btn.addEventListener('click',function(){tabs.forEach(x=>x.classList.toggle('is-active',x===btn));panes.forEach(x=>x.classList.toggle('is-active',x.dataset.ilPane===btn.dataset.ilTab));if(search){search.value='';search.dispatchEvent(new Event('input'));}})});
            if(search)search.addEventListener('input',function(){var q=(search.value||'').toLowerCase().trim();page.querySelectorAll('[data-il-item]').forEach(function(card){card.style.display=!q||card.dataset.ilItem.indexOf(q)!==-1?'':'none';});page.querySelectorAll('.wpst-il-group').forEach(function(group){group.style.display=Array.from(group.querySelectorAll('[data-il-item]')).some(x=>x.style.display!=='none')?'':'none';});});
            page.querySelectorAll('[data-copy]').forEach(function(card){card.addEventListener('click',function(){var text=card.dataset.copy;if(navigator.clipboard)navigator.clipboard.writeText(text);if(toast){toast.textContent=text+' kopyalandı';toast.classList.add('is-show');setTimeout(()=>toast.classList.remove('is-show'),1200);}})});
        });
        </script>
        <?php
    }
}
