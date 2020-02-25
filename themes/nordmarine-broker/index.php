<?
global $post;
$post_term = wp_get_post_terms($post->ID, 'sale_type', array("fields" => "all"));
$sale_type = $post_term[0]->slug;

if (get_field('yacht_dol') > 0) {
  $price['dol'] = number_format(str_replace(' ', '', get_field('yacht_dol')), 0, '.', ' ') . ' $';
}

if (get_field('yacht_eur') > 0) {
  $price['eur'] = number_format(str_replace(' ', '', get_field('yacht_eur')), 0, '.', ' ') . ' €';
}

if (get_field('yacht_rub') > 0) {
  $price['rub'] = number_format(str_replace(' ', '', get_field('yacht_rub')), 0, '.', ' ') . ' ₽';
}

if (get_field('yacht_pound') > 0) {
  $price['pound'] = number_format(str_replace(' ', '', get_field('yacht_pound')), 0, '.', ' ') . ' £';
}

// Базовая валюта указанная в параметрах яхты
$yCurrency = get_field('yacht_currency');

//$price = $price[$_SESSION['currency_brockerage']];
$price = $price[$yCurrency];

$img = get_field('yacht_img');
$state = get_field('state');
$pdfRows = [
  'yacht_pdf_year' => [
    'unit' => 'год',
    'substitute' => 'yacht_year',
  ],
  'yacht_pdf_length' => [
    'unit' => 'м',
    'substitute' => 'yacht_length',
  ],
  'yacht_pdf_maxwidth' => [
    'unit' => 'м',
    'substitute' => 'yacht_width',
  ],
  'yacht_pdf_draft' => [
    'unit' => 'м',
    'substitute' => 'yacht_osadka',
  ],
  'yacht_pdf_draught' => [
    'unit' => 'т',
    'substitute' => 'yacht_izm',
  ],
  'yacht_pdf_fuel' => [
    'unit' => 'л',
    'substitute' => 'yacht_fuel',
  ],
  'yacht_pdf_water' => [
    'unit' => 'л',
    'substitute' => 'yacht_water',
  ],
  'yacht_pdf_cabins' => [
    'unit' => '',
    'substitute' => '',
  ],
  'yacht_pdf_couchette' => [
    'unit' => '',
    'substitute' => '',
  ],
  'yacht_pdf_passengers' => [
    'unit' => '',
    'substitute' => '',
  ],
  'yacht_pdf_capacity' => [
    'unit' => '',
    'substitute' => '',
  ],
  'yacht_pdf_engines' => [
    'unit' => '',
    'substitute' => 'yacht_engines',
  ],
  'yacht_pdf_enginepower' => [
    'unit' => 'л.с.',
    'substitute' => '',
  ],
  'yacht_pdf_operating_hours' => [
    'unit' => 'м/ч',
    'substitute' => 'yacht_motohours',
  ],
  'yacht_max_speed' => [
    'unit' => 'уз',
    'substitute' => '',
  ],
  'yacht_pdf_location' => [
    'unit' => '',
    'substitute' => 'country_name',
  ],
];
$img = get_field('yacht_img');
if (have_rows('yacht_pdf_more')) :
  while (have_rows('yacht_pdf_more')) : the_row();
    $yacht_pdf_more_label = get_sub_field('yacht_pdf_more_label');
    $yacht_pdf_more_value = get_sub_field('yacht_pdf_more_field');
    $yacht_pdf_more = array($yacht_pdf_more_label => $yacht_pdf_more_value);
  endwhile;
endif;

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title><? get_field('meta_h1') ? the_field('meta_h1') : the_title(); ?></title>
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="format-detection" content="telephone=no">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <style>
    @font-face {
      font-family: 'medium';
      src: url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.eot');
      src: url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.eot?#iefix') format('embedded-opentype'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.woff2') format('woff2'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.woff') format('woff'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.ttf') format('truetype'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTMedium-Reg.svg#FuturaPTMedium-Reg') format('svg');
      font-weight: normal;
      font-style: normal;
    }

    @font-face {
      font-family: 'light';
      src: url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.eot');
      src: url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.eot?#iefix') format('embedded-opentype'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.woff2') format('woff2'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.woff') format('woff'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.ttf') format('truetype'),
        url('<?= esc_url(plugins_url('/nordmarine/fonts/', dirname(__FILE__))) ?>hinted-FuturaPTLightRegular.svg#FuturaPTLightRegular') format('svg');
      font-weight: normal;
      font-style: normal;
    }

    <? include_once(plugin_dir_path(__FILE__) . 'css/styles.css'); ?>
  </style>
</head>

<body>

  <div class="pdf">
    <div class="pdf-header">
      <!-- <div class="pdf-header__left">
        <div class="pdf-header__left-body">
          <div class="pdf-header__logo"></div>
        </div>
      </div>
      <div class="pdf-header__right">
        <div class="pdf-header__right-body">
          <div class="pdf-contacts">
            <p class="pdf-contacts__text">119034, Москва, Бутиковский пер., 7<br><span>Т</span>+7 495 775 11 00<br><span>M</span>+7 985 201 01 01<br><span>E</span>brokerage@nordmarine.ru<br><span>W</span>nordmarine.ru</p>
          </div>
        </div>
      </div> -->
    </div>
    <div class="pdf-header">
      <div class="pdf-header__left">
        <div class="pdf-header__left-body">
          <p class="pdf-header__yacht"><? the_title(); ?></p>
        </div>
      </div>
      <div class="pdf-header__right">
        <div class="pdf-header__right-body">
          <p class="pdf-header__price"><?= @$price ?></p>
        </div>
      </div>
    </div>
    <h2 class="pdf-characteristics__topic">Основные характеристики</h2>
    <div class="pdf-characteristics">
      <div class="pdf-characteristics__body">
        <div class="pdf-characteristics__left">
          <div class="pdf-characteristics__left-body">
            <div class="pdf-characteristics__photo"><img src="<? echo $img['sizes']['gallery-images']; ?>"></div>
          </div>
        </div>
        <div class="pdf-characteristics__right">
          <div class="pdf-characteristics__right-body">
            <div class="pdf-characteristics-table">

              <? foreach ($pdfRows as $field_name => $field_value) : ?>

                <? $field = (get_field($field_name, $post->ID) != '')
                  ? get_field_object($field_name, $post->ID)
                  : get_field_object($field_value['substitute'], $post->ID); ?>

                <? if ($field['value']) : ?>
                  <div class="pdf-characteristics-table__row">

                    <div class="pdf-characteristics-table__left">
                      <div class="pdf-characteristics-table__left-body">
                        <p class="pdf-characteristics-table__text"><?= $field['label'] ?></p>
                      </div>
                    </div>

                    <div class="pdf-characteristics-table__right">
                      <div class="pdf-characteristics-table__right-body">
                        <p class="pdf-characteristics-table__text"><?= strip_tags($field['value']) ?> <?= $field_value['unit'] ?></p>
                      </div>
                    </div>

                  </div>
                <? endif; ?>

              <? endforeach; ?>

              <? if (@$yacht_pdf_more) : ?>
                <? foreach ($yacht_pdf_more as $key => $value) : ?>
                  <div class="pdf-characteristics-table__row">

                    <div class="pdf-characteristics-table__left">
                      <div class="pdf-characteristics-table__left-body">
                        <p class="pdf-characteristics-table__text"><?= $key ?></p>
                      </div>
                    </div>

                    <div class="pdf-characteristics-table__right">
                      <div class="pdf-characteristics-table__right-body">
                        <p class="pdf-characteristics-table__text"><?= $value ?></p>
                      </div>
                    </div>

                  </div>
                <? endforeach; ?>
              <? endif; ?>

            </div>
          </div>
        </div>
      </div>
    </div>

    <? if ($ys = get_field('yacht_specs')) : ?>
      <? $arrays = explode('<br />', strip_tags($ys, '<br>')) ?>
      <? $arrays = array_chunk($arrays, 4) ?>
      <? $arrays = array_chunk($arrays, ceil(sizeof($arrays) / 2)) ?>
      <h2 class="pdf-equipment__topic">Дополнительное оборудование</h2>
      <div class="pdf-equipment">
        <div class="pdf-equipment__body">
          <? if (key_exists(0, $arrays)) : ?>
            <div class="pdf-equipment__left">
              <div class="pdf-equipment__left-body">
                <? foreach (@$arrays[0] as $key => $rows) : ?>

                  <? foreach ($rows as $row1) : ?>
                    <p class="pdf-equipment__text"><?= $row1 ?></p>
                  <? endforeach; ?>

                <? endforeach; ?>
              </div>
            </div>
          <? endif;
          if (key_exists(1, $arrays)) : ?>

            <div class="pdf-equipment__right">
              <div class="pdf-equipment__right-body">
                <? foreach (@$arrays[1] as $key => $rows) : ?>

                  <? foreach ($rows as $row1) : ?>
                    <p class="pdf-equipment__text"><?= $row1 ?></p>
                  <? endforeach; ?>

                <? endforeach; ?>
              </div>
            </div>
          <? endif; ?>
        </div>
      </div>
    <? endif; ?>


    <? if ($yp = get_field('yacht_plans')) : ?>
      <div class="pdf-break"></div>
      <div class="pdf-header">
        <!-- <div class="pdf-header__left">
          <div class="pdf-header__left-body">
            <div class="pdf-header__logo"></div>
          </div>
        </div>
        <div class="pdf-header__right">
          <div class="pdf-header__right-body">
            <div class="pdf-contacts">
              <p class="pdf-contacts__text">119034, Москва, Бутиковский пер., 7<br><span>Т</span>+7 495 775 11 00<br><span>M</span>+7 985 201 01 01<br><span>E</span>brokerage@nordmarine.ru<br><span>W</span>nordmarine.ru</p>
            </div>
          </div>
        </div> -->
      </div>
      <div class="pdf-header">
        <div class="pdf-header__left">
          <div class="pdf-header__left-body">
            <p class="pdf-header__yacht"><? the_title(); ?></p>
          </div>
        </div>
        <div class="pdf-header__right">
          <div class="pdf-header__right-body">
            <p class="pdf-header__price"><?= @$price ?></p>
          </div>
        </div>
      </div>

      <div class="pdf-deck">
        <div class="pdf-deck">
          <div class="pdf-deck__topic">План палуб</div>
          <? foreach ($yp as $planKey => $plan) :
            $field_name = 'yacht_plan_header_' . ($planKey + "1");
          ?>
            <div class="pdf-deck__item">
              <div class="pdf-deck__img"><img src="<? echo $plan['url']; ?>" alt="" title=""></div>
              <p class="pdf-deck__text"><? the_field($field_name, @$post->ID) ?></p>
            </div>
          <? endforeach; ?>
        </div>
      </div>
    <? endif; ?>
    <? if (get_field('yacht_pdf_gallery_default') == FALSE) {
      $gallery1 = is_array(get_field('yaсht_exterior', $post->ID)) ? get_field('yaсht_exterior', $post->ID) : [];
      $gallery2 = is_array(get_field('yacht_gallsery', $post->ID)) ? get_field('yacht_gallsery', $post->ID) : [];
      $gallery = array_merge($gallery1, $gallery2);
    } else {
      $gallery = get_field('yacht_pdf_gallery');
    }
    ?>
    <? if ($gallery != NULL && sizeof($gallery)) : ?>
      <div class="pdf-break"></div>
      <div class="pdf-gallery">
        <div class="pdf-gallery__topic">Галерея</div>
        <? foreach (array_chunk($gallery, 2) as $i => $chunk) : ?>
          <div class="pdf-gallery__body">
            <? foreach ($chunk as $gallery_image) : ?>
              <div class="pdf-gallery__left">
                <div class="pdf-gallery__left-body">
                  <div class="pdf-gallery__img"><img src="<?= $gallery_image['sizes']['gallery-images'] ?>"></div>
                </div>
              </div>
            <? endforeach; ?>
          </div>
          <?php if ($i == "3" or $i == "7" or $i == "11" or $i == "15") : ?>
            <div class="pdf-break"></div>
          <?php endif ?>
        <? endforeach; ?>
      </div>
    <? endif; ?>
  </div>
</body>

</html>