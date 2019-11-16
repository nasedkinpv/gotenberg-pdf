<?
global $post;
$yacht = new Yacht;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>
    <?= get_the_title() . ' | ID:' . get_the_ID(); ?>
  </title>
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link href="<?php echo get_template_directory_uri(); ?>/dist/main.css?v=<?php echo rand(1, 100) * rand(1, 5) ?>" rel="stylesheet">
  <meta name="viewport" content="initial-scale=1, maximum-scale=1.0, user-scalable=no">
</head>
<style type="text/css">
  :root {
    font-size: 80%;
  }

  div.header {
    display: block;
    position: running(header);
  }

  @page {
    @top-center {
      content: element(header)
    }
  }

  @page {
    @bottom-center {
      content: element(footer)
    }
  }
</style>

<body class="A4 PDF">
  <div class="container">
    <?php if (have_posts()) : while (have_posts()) :
        the_post();
        $yacht = new Yacht; ?>
        <!-- header -->
        <table>
          <thead>
            <tr>
              <td>

                <div class="header">
                  <div class="header-logo">
                    <div class="header-logo__image"></div>
                  </div>
                  <div class="header-contacts">
                    <div class="header-contacts__wrapper">
                      <div class="header-contacts__col">
                        <p>Москва, Ленинградское шоссе, 39с7<br /><strong>Royal Yacht Club</strong></p>
                      </div>
                      <div class="header-contacts__col">
                        <p>+7 (495) 258-84-48<br />sales@fairline-russia.com</p>
                      </div>
                    </div>
                  </div>
                </div>

              </td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="content">
                  <div class="avoid-break">

                    <!-- image -->
                    <div class="yacht-header">
                      <div class="yacht-image" style="background-image: url('')">
                        <?= $yacht->featured_image ?>
                      </div>
                      <div class="yacht-block">
                        <div class="yacht-block__wrapper">
                          <div class="yacht-title">
                            <?= get_the_title() ?>
                          </div>
                          <div class="yacht-price">
                            <?= $yacht->fields->getPrice()['value'] ?>
                          </div>
                        </div>
                        <div class="yacht-block__status">
                          <div class="yacht-status <?= $yacht->getStatusClass() ?>">
                            <?= $yacht->getStatusLabel() ?>
                          </div>
                        </div>
                      </div>
                      <!-- yacht text -->

                    </div>
                    <div class="pdf-specs">
                      <?
                          $specs = $yacht->fields->getFields();
                          $specs_column = array_chunk($specs, ceil(count($specs) / 3), true);
                          ?>
                      <div class="pdf-specs">
                        <div class="pdf-specs__header">Основные характеристики
                        </div>
                        <div class="pdf-specs__row">
                          <?
                              foreach ($specs_column as $key => $col) {
                                ?>
                            <div class="pdf-specs__col">
                              <?
                                    $html = '';
                                    foreach ($col as $key => $spec) {
                                      foreach ($spec as $key => $value) {
                                        $html .= '<div class="' . $key . '">' . $value . '</div>';
                                      }
                                    }
                                    echo $html;
                                    ?>
                            </div>
                          <?
                              } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="avoid-break">
                    <!-- <div class="page-break"></div> -->
                    <? if ($yacht->post_content) : ?>
                      <div class="pdf-text">
                        <div class="pdf-text__header">Описание</div>
                        <div class="pdf-text__content">
                          <?= $yacht->post_content ?>
                        </div>
                      </div>
                    <? endif;
                        ?>
                    <!-- yacht notes -->
                    <? if (tr_posts_field('notes')) : ?>
                      <div class="pdf-more">
                        <div class="pdf-more__header">Дополнительно</div>
                        <div class="pdf-more__content">
                          <?= apply_filters('the_content', tr_posts_field('notes')); ?>
                        </div>
                      </div>
                    <? endif;
                        ?>
                    <!-- yacht equipment -->
                    <? if (!empty(tr_posts_field('options_list'))) : ?>
                      <div class="pdf-equipment">
                        <div class="pdf-equipment__wrapper">
                          <div class="pdf-equipment__header waypoint-title">Оборудование
                          </div>
                          <div class="pdf-equipment__content">
                            <?= apply_filters('the_content', tr_posts_field('options_list')); ?>
                          </div>
                        </div>
                      </div>
                      <!-- <div class="page-break"></div> -->
                    <? endif; ?>
                  </div>
                  <div class="avoid-break">
                    <!-- options list -->
                    <div class="pdf-options">
                      <?
                          if ($yacht->options->options_list) :
                            ?>

                        <div class="pdf-options__wrapper">
                          <div class="yacht-options__header">Двигатели и цены</div>
                          <?
                                foreach ($yacht->options->options_list as $key => $option) {
                                  echo '<div class="pdf-options-card">';
                                  echo '<div class="pdf-options-card__header">' . $option['header'] . '</div>';
                                  foreach ($option['fields'] as $label => $value) {
                                    echo '<div class="pdf-options-card__col">';

                                    echo '<div class="label">' . $label . '</div>';
                                    echo '<div class="value">' . $value . '</div>';
                                    echo '</div>';
                                  }
                                  echo '<div class="pdf-options__price">' . $option['price'] . '</div>';
                                  echo '</div>';
                                }
                                ?>
                        </div>

                      <? endif; ?>
                    </div>
                  </div>
                  <!-- <div class="page-break">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->

                  <? if (array_key_exists('exterior', $yacht->media->galleries)) : ?>
                    <div class="avoid-break">
                      <div class="gallery-exterior">
                        <div class="gallery-header">Экстерьер</div>
                      </div>
                      <? $i = 0;
                            foreach ($yacht->media->galleries['exterior'] as $key => $id) {
                              $i++;
                              $image = wp_get_attachment_image_url($id, 'gallery');
                              echo '<div class="gallery-image" style="background-image: url(' . $image . ')"></div>';
                              if ($i % 2 == 0) echo '<div class="page-break"></div>';
                              if ($i == 4) break;
                            } ?>
                    </div>
                    <div class="page-break"></div>
                  <? endif; ?>

                  <? if (array_key_exists('interior', $yacht->media->galleries)) : ?>

                    <div class="avoid-break">
                      <div class="gallery-interior">
                        <div class="gallery-header">Интерьер</div>
                        <? $i = 0;
                              foreach ($yacht->media->galleries['interior'] as $key => $id) {
                                $i++;
                                $image = wp_get_attachment_image_url($id, 'gallery');
                                echo '<div class="gallery-image" style="background-image: url(' . $image . ')"></div>';
                                if ($i % 2 == 0) echo '<div class="page-break"></div>';
                                if ($i == 8) break;
                              } ?>
                      </div>
                    </div>
                    <div class="page-break"></div>
                  <? endif; ?>

                  <?
                      if (!empty($yacht->plans)) {
                        ?>
                    <div class="pdf-plans">
                      <div class="pdf-plans__header">
                        Планы палуб
                      </div>
                      <? foreach ($yacht->plans as $key => $plan) {
                              $plan_image = wp_get_attachment_image_url(
                                $plan['plan'],
                                'gallery'
                              );
                              $plan_name = $plan['plan_name'];
                              $plan_html = <<<HTML
<div class="pdf-plans__wrapper">
<div class="pdf-plans__name">{$plan_name}</div>
    <div class="pdf-plans__image" style="background-image: url({$plan_image});">
    </div>
</div>

HTML;
                              echo $plan_html;
                            } ?>

                    </div>
                    <!-- <div class="page-break"></div> -->
                  <?
                      } ?>

                </div>
              <? endwhile;
              else : ?>
              <article>
                <p>Nothing to see.</p>
              </article>
  </div>
  </td>
  </tr>
  </tbody>
  <tfoot>
    <tr>
      <td>
        <div class="footer">...</div>
      </td>
    </tr>
  </tfoot>
  </table>

<?php endif; ?>
</body>


</html>