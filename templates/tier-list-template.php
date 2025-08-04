<?php if(!empty($tiers) && !empty($groups)): ?>
<div class="tier-grid" style="--count:<?= count($groups) ?>;">
  <div class="grid-header empty-cell"></div>
  <?php foreach($groups as $group): ?>
    <div class="grid-header" style="color: <?= esc_attr($group['color']) ?>;"><?= esc_html($group['name']) ?></div>
  <?php endforeach; ?>
  <?php 
    // Prebuild tier colors map for tooltip
    $tierColors = [];
    foreach($tiers as $tt){ $tierColors[$tt['name']] = $tt['color']; }
  ?>
  <?php foreach($tiers as $tier): ?>
    <div class="tier-label-cell" style="background: <?= esc_attr($tier['color']) ?>;"><?= esc_html($tier['name']) ?></div>
    <?php foreach($groups as $group): ?>
      <div class="tier-cell">
        <?php foreach($tier['items'] as $item): if(($item['group'] ?? '')==$group['name']): ?>
          <?php
            // Rareté color
            $rarityColor = '';
            if(!empty($item['rarity'])){
              foreach($rarities as $ra){ if($ra['name']===$item['rarity']){ $rarityColor=$ra['color']; break; } }
            }
          ?>
          <a href="#" class="tier-item" data-group="<?= esc_attr($item['group']) ?>" data-element="<?= esc_attr($item['element']) ?>" data-rarity="<?= esc_attr($item['rarity']) ?>" data-force-tiers="<?= isset($item['forces']) ? esc_attr(implode(',', array_column($item['forces'], 'tier'))) : '' ?>" data-force-names="<?= isset($item['forces']) ? esc_attr(implode(',', array_column($item['forces'], 'name'))) : '' ?>">
            <?php if(!empty($item['image'])): ?>
              <img src="<?= esc_url($item['image']) ?>" alt="<?= esc_attr($item['label']) ?>" style="<?= $rarityColor ? 'border:2px solid '.$rarityColor.';' : '' ?>">
            <?php endif; ?>
            <div class="tier-item-label">
              <?= esc_html($item['label']) ?>
              <?php if(!empty($item['element'])):
                foreach($elements as $el){
                  if($el['name']==$item['element']){
                    echo '<img src="'.esc_url($el['image']).'" class="element-icon" style="width:16px;height:16px;object-fit:contain;" alt="'.esc_attr($el['name']).'" />';
                    break;
                  }
                }
              endif; ?>
            </div>
            <?php if(!empty($item['strength_links'])): ?>
              <?php endif; ?>

            <!-- TOOLTIP -->
            <?php
              $groupColor = '';
              if(!empty($item['group'])){
                foreach($groups as $g){ if($g['name']==$item['group']){ $groupColor=$g['color']; break; } }
              }
            ?>
            <div class="tier-tooltip">
              <div class="tier-tooltip-inner" style="<?= $rarityColor ? 'border:0.5px solid '.$rarityColor.';' : 'border:0.5px solid #444;' ?> <?= isset($rarityColor)&&$rarityColor?'border:2px solid '.$rarityColor:'' ?>">
                <?php if(!empty($item['image'])): ?>
                  <img src="<?= esc_url($item['image']) ?>" alt="" class="tooltip-avatar" />
                <?php endif; ?>
                <div class="tooltip-name"><?= esc_html($item['label']) ?></div>
                <div class="tooltip-meta">
                  <?php if($rarityColor): ?>
                    <span class="tooltip-rarity" style="border-color:<?= esc_attr($rarityColor) ?>"><?= esc_html($item['rarity']) ?></span>
                  <?php endif; ?>
                  <?php if(!empty($item['element'])):
                    foreach($elements as $el){ if($el['name']==$item['element']){ echo '<span class="tooltip-element"><img src="'.esc_url($el['image']).'" alt="'.esc_attr($el['name']).'" /></span>'; break; } }
                  endif; ?>
                  <?php if($groupColor): ?>
                    <span class="tooltip-group" style="color:<?= esc_attr($groupColor) ?>"><?= esc_html($item['group']) ?></span>
                  <?php endif; ?>
                </div>
                <?php if(!empty($item['strength_links'])): ?>
                  <div class="tooltip-strengths">
                    <?php foreach($item['strength_links'] as $st): if(!empty($st['name']) && !empty($st['tier'])): 
                      $tc = $tierColors[$st['tier']] ?? '#666'; ?>
                      <div class="tooltip-strength">
                        <span class="square" style="background:<?= esc_attr($tc) ?>"></span>
                        <span class="sname"><?= esc_html($st['name']) ?></span>
                        <span class="stier"><?= esc_html($st['tier']) ?></span>
                      </div>
                    <?php endif; endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <!-- /TOOLTIP -->

          </a>
        <?php endif; endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>