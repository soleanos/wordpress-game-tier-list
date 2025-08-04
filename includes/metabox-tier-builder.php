<?php
add_action('add_meta_boxes', function(){
    add_meta_box('tierbuilder','Tier List Builder',function($post){
        $data = get_post_meta($post->ID, '_tierbuilder_data', true);
        $obj = $data ? json_decode($data, true) : ['groups'=>[], 'elements'=>[], 'rarities'=>[], 'strengths'=>[], 'tiers'=>[]];

        // Backward compatibility
        if(isset($obj['groups'][0]) && is_string($obj['groups'][0])){
            $obj['groups'] = array_map(function($g){ return ['name'=>$g,'color'=>'#ffffff']; }, $obj['groups']);
        }
        if(!isset($obj['elements'])) $obj['elements'] = [];
        if(!isset($obj['rarities'])) $obj['rarities'] = [];
        if(!isset($obj['strengths'])) $obj['strengths'] = [];
        if(!isset($obj['tiers'])) $obj['tiers'] = [];
        foreach($obj['tiers'] as &$t){
            if(!isset($t['color']) || !$t['color']) $t['color'] = '#e74c3c';
            if(!isset($t['items'])) $t['items'] = [];
            foreach($t['items'] as &$it){ if(!isset($it['rarity'])) $it['rarity'] = ''; if(!isset($it['strength_links'])) $it['strength_links']=[]; }
        }
        ?>
        <div id="tier-builder">
          <h4>Groupes (colonnes)</h4>
          <button type="button" id="add-group" class="button">+ Ajouter un groupe</button>
          <ul id="groups-list">
            <?php foreach($obj['groups'] as $gidx => $group): ?>
            <li class="group" data-group="<?= esc_attr($gidx) ?>">
              <input type="text" name="group_name[<?= $gidx ?>]" value="<?= esc_attr($group['name']) ?>" placeholder="Nom du groupe" />
              <input type="color" name="group_color[<?= $gidx ?>]" value="<?= esc_attr($group['color']) ?>" />
              <button type="button" class="remove-group">×</button>
            </li>
            <?php endforeach; ?>
          </ul>

          <h4>Éléments (icônes)</h4>
          <button type="button" id="add-element" class="button">+ Ajouter un élément</button>
          <ul id="elements-list">
            <?php foreach($obj['elements'] as $eidx => $element): ?>
            <li class="element" data-element="<?= esc_attr($eidx) ?>">
              <input type="text" name="element_name[<?= $eidx ?>]" value="<?= esc_attr($element['name']) ?>" placeholder="Nom de l'élément" />
              <input type="text" name="element_image[<?= $eidx ?>]" value="<?= esc_attr($element['image']) ?>" placeholder="URL icône" />
              <button type="button" class="remove-element">×</button>
            </li>
            <?php endforeach; ?>
          </ul>

          <h4>Raretés (couleur)</h4>
          <button type="button" id="add-rarity" class="button">+ Ajouter une rareté</button>
          <ul id="rarities-list">
            <?php foreach($obj['rarities'] as $ridx => $rarity): ?>
            <li class="rarity" data-rarity="<?= esc_attr($ridx) ?>">
              <input type="text" name="rarity_name[<?= $ridx ?>]" value="<?= esc_attr($rarity['name']) ?>" placeholder="Nom de la rareté" />
              <input type="color" name="rarity_color[<?= $ridx ?>]" value="<?= esc_attr($rarity['color'] ?? '#ffffff') ?>" />
              <button type="button" class="remove-rarity">×</button>
            </li>
            <?php endforeach; ?>
          </ul>

          <h4>Forces de combats (globales)</h4>
          <p style="margin-top:0">Définis les noms disponibles (ex: PVP, PVE...).</p>
          <button type="button" id="add-strength-global" class="button">+ Ajouter une force</button>
          <ul id="strengths-list">
            <?php foreach($obj['strengths'] as $sidx => $strength): ?>
            <li class="strength-global" data-strength="<?= esc_attr($sidx) ?>">
              <input type="text" name="strength_name[<?= $sidx ?>]" value="<?= esc_attr($strength['name']) ?>" placeholder="Nom (ex: PVP)" />
              <button type="button" class="remove-strength-global">×</button>
            </li>
            <?php endforeach; ?>
          </ul>

          <h4>Paliers (lignes) & personnages</h4>
          <button type="button" id="add-tier" class="button">+ Ajouter un palier</button>
          <ul id="tiers-list">
            <?php foreach($obj['tiers'] as $tidx => $tier): ?>
            <li class="tier" data-tier="<?= esc_attr($tidx) ?>">
              <input type="text" name="tier_name[<?= $tidx ?>]" value="<?= esc_attr($tier['name']) ?>" placeholder="Nom du palier" />
              <input type="color" name="tier_color[<?= $tidx ?>]" value="<?= esc_attr($tier['color']) ?>" />
              <button type="button" class="remove-tier">×</button>
              <ul class="items">
                <?php foreach($tier['items'] as $iidx => $item): ?>
                <li class="item" data-item="<?= esc_attr($iidx) ?>">
                  <input type="text" name="item_label[<?= $tidx ?>][<?= $iidx ?>]" value="<?= esc_attr($item['label']) ?>" placeholder="Nom du personnage" />
                  <select name="item_group[<?= $tidx ?>][<?= $iidx ?>]">
                    <option value="">-- Groupe --</option>
                    <?php foreach($obj['groups'] as $g): ?>
                      <option value="<?= esc_attr($g['name']) ?>" <?= selected($item['group'] ?? '', $g['name'], false) ?>><?= esc_html($g['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="item_element[<?= $tidx ?>][<?= $iidx ?>]">
                    <option value="">-- Élément --</option>
                    <?php foreach($obj['elements'] as $el): ?>
                      <option value="<?= esc_attr($el['name']) ?>" <?= selected($item['element'] ?? '', $el['name'], false) ?>><?= esc_html($el['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="item_rarity[<?= $tidx ?>][<?= $iidx ?>]">
                    <option value="">-- Rareté --</option>
                    <?php foreach($obj['rarities'] as $ra): ?>
                      <option value="<?= esc_attr($ra['name']) ?>" <?= selected($item['rarity'] ?? '', $ra['name'], false) ?>><?= esc_html($ra['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="text" name="item_image[<?= $tidx ?>][<?= $iidx ?>]" value="<?= esc_attr($item['image']) ?>" placeholder="URL image" />
                  <input type="text" name="item_link[<?= $tidx ?>][<?= $iidx ?>]" value="<?= esc_attr($item['link']) ?>" placeholder="Lien" />
                  <div class="item-strength-links" data-wrapper>
                      <strong>Forces de combat :</strong>
                      <button type="button" class="add-strength-link button">+ Ajouter</button>
                      <ul class="strength-links-list">
                        <?php if(!empty($item['strength_links'])): foreach($item['strength_links'] as $lsidx => $link): ?>
                          <li class="strength-link" data-link="<?= esc_attr($lsidx) ?>">
                            <select name="item_strength_link_name[<?= $tidx ?>][<?= $iidx ?>][<?= $lsidx ?>]">
                              <option value="">-- Force --</option>
                              <?php foreach($obj['strengths'] as $gs): ?>
                                <option value="<?= esc_attr($gs['name']) ?>" <?= selected($link['name'] ?? '', $gs['name'], false) ?>><?= esc_html($gs['name']) ?></option>
                              <?php endforeach; ?>
                            </select>
                            <select name="item_strength_link_tier[<?= $tidx ?>][<?= $iidx ?>][<?= $lsidx ?>]">
                              <option value="">-- Palier --</option>
                              <?php foreach($obj['tiers'] as $tp): ?>
                                <option value="<?= esc_attr($tp['name']) ?>" <?= selected($link['tier'] ?? '', $tp['name'], false) ?>><?= esc_html($tp['name']) ?></option>
                              <?php endforeach; ?>
                            </select>
                            <button type="button" class="remove-strength-link">×</button>
                          </li>
                        <?php endforeach; endif; ?>
                      </ul>
                    </div>
                  <button type="button" class="remove-item">×</button></li>
                <?php endforeach; ?>
              </ul>
              <button type="button" class="add-item button">+ Ajouter un personnage</button>
            </li>
            <?php endforeach; ?>
          </ul>
          <input type="hidden" id="tierbuilder_data" name="tierbuilder_data" value="<?= esc_attr(json_encode($obj)) ?>" />
        </div>
        <?php
    }, 'tierlist','normal','high');
});
add_action('save_post_tierlist', function($post_id){
    if(isset($_POST['tierbuilder_data'])){
        update_post_meta($post_id, '_tierbuilder_data', wp_unslash($_POST['tierbuilder_data']));
    }
});
