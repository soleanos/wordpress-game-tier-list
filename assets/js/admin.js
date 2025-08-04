jQuery(function($){
  function rebuildSelects(){
    var groupOpts='<option value="">-- Groupe --</option>';
    $('#groups-list .group').each(function(){var n=$(this).find('input[name^="group_name"]').val();if(n)groupOpts+='<option value="'+n+'">'+n+'</option>';});
    var elemOpts='<option value="">-- Élément --</option>';
    $('#elements-list .element').each(function(){var n=$(this).find('input[name^="element_name"]').val();if(n)elemOpts+='<option value="'+n+'">'+n+'</option>';});
    var rarityOpts='<option value="">-- Rareté --</option>';
    $('#rarities-list .rarity').each(function(){var n=$(this).find('input[name^="rarity_name"]').val();if(n)rarityOpts+='<option value="'+n+'">'+n+'</option>';});
    var strengthOpts='<option value="">-- Force --</option>';
    $('#strengths-list .strength-global').each(function(){var n=$(this).find('input[name^="strength_name"]').val();if(n)strengthOpts+='<option value="'+n+'">'+n+'</option>';});

    $('select[name^="item_group"]').each(function(){var c=$(this).val();$(this).html(groupOpts).val(c);});
    $('select[name^="item_element"]').each(function(){var c=$(this).val();$(this).html(elemOpts).val(c);});
    $('select[name^="item_rarity"]').each(function(){var c=$(this).val();$(this).html(rarityOpts).val(c);});

    $('ul.strength-links-list').each(function(){
      var tOpts='<option value="">-- Palier --</option>';
      $('#tiers-list .tier > input[name^="tier_name"]').each(function(){var n=$(this).val();if(n)tOpts+='<option value="'+n+'">'+n+'</option>';});
      $(this).find('select[name^="item_strength_link_name"]').each(function(){var c=$(this).val();$(this).html(strengthOpts).val(c);});
      $(this).find('select[name^="item_strength_link_tier"]').each(function(){var c=$(this).val();$(this).html(tOpts).val(c);});
    });
  }

  function saveData(){
    rebuildSelects();
    var groups=[],elements=[],rarities=[],strengths=[],tiers=[];
    $('#groups-list .group').each(function(){groups.push({name:$(this).find('input[name^="group_name"]').val(),color:$(this).find('input[name^="group_color"]').val()});});
    $('#elements-list .element').each(function(){elements.push({name:$(this).find('input[name^="element_name"]').val(),image:$(this).find('input[name^="element_image"]').val()});});
    $('#rarities-list .rarity').each(function(){rarities.push({name:$(this).find('input[name^="rarity_name"]').val(),color:$(this).find('input[name^="rarity_color"]').val()});});
    $('#strengths-list .strength-global').each(function(){strengths.push({name:$(this).find('input[name^="strength_name"]').val()});});

    $('#tiers-list .tier').each(function(){
      var $t=$(this),name=$t.find('>input[name^="tier_name"]').val(),color=$t.find('>input[type="color"]').val(),items=[];
      $t.find('.item').each(function(){
        var $i=$(this),links=[];
        $i.find('.strength-link').each(function(){
          links.push({name:$(this).find('select[name^="item_strength_link_name"]').val(),tier:$(this).find('select[name^="item_strength_link_tier"]').val()});
        });
        items.push({
          label:$i.find('input[name^="item_label"]').val(),
          group:$i.find('select[name^="item_group"]').val(),
          element:$i.find('select[name^="item_element"]').val(),
          rarity:$i.find('select[name^="item_rarity"]').val(),
          image:$i.find('input[name^="item_image"]').val(),
          link:$i.find('input[name^="item_link"]').val(),
          strength_links:links
        });
      });
      tiers.push({name:name,color:color,items:items});
    });
    $('#tierbuilder_data').val(JSON.stringify({groups:groups,elements:elements,rarities:rarities,strengths:strengths,tiers:tiers}));
  }

  saveData();

  $('#tier-builder')
    .on('click','#add-group',function(){
      var idx=$('#groups-list .group').length;
      $('#groups-list').append('<li class="group" data-group="'+idx+'"><input type="text" name="group_name['+idx+']" placeholder="Nom du groupe" /><input type="color" name="group_color['+idx+']" value="#ffffff" /><button type="button" class="remove-group">×</button></li>');
      saveData();
    }).on('click','.remove-group',function(){ $(this).closest('.group').remove(); saveData(); })
    .on('click','#add-element',function(){
      var idx=$('#elements-list .element').length;
      $('#elements-list').append('<li class="element" data-element="'+idx+'"><input type="text" name="element_name['+idx+']" placeholder="Nom de l\'élément" /><input type="text" name="element_image['+idx+']" placeholder="URL icône" /><button type="button" class="remove-element">×</button></li>');
      saveData();
    }).on('click','.remove-element',function(){ $(this).closest('.element').remove(); saveData(); })
    .on('click','#add-rarity',function(){
      var idx=$('#rarities-list .rarity').length;
      $('#rarities-list').append('<li class="rarity" data-rarity="'+idx+'"><input type="text" name="rarity_name['+idx+']" placeholder="Nom de la rareté" /><input type="color" name="rarity_color['+idx+']" value="#ffffff" /><button type="button" class="remove-rarity">×</button></li>');
      saveData();
    }).on('click','.remove-rarity',function(){ $(this).closest('.rarity').remove(); saveData(); })
    .on('click','#add-strength-global',function(){
      var idx=$('#strengths-list .strength-global').length;
      $('#strengths-list').append('<li class="strength-global" data-strength="'+idx+'"><input type="text" name="strength_name['+idx+']" placeholder="Nom (ex: PVP)" /><button type="button" class="remove-strength-global">×</button></li>');
      saveData();
    }).on('click','.remove-strength-global',function(){ $(this).closest('.strength-global').remove(); saveData(); })
    .on('click','#add-tier',function(){
      var idx=$('#tiers-list .tier').length;
      $('#tiers-list').append('<li class="tier" data-tier="'+idx+'"><input type="text" name="tier_name['+idx+']" placeholder="Nom du palier" /><input type="color" name="tier_color['+idx+']" value="#e74c3c" /><button type="button" class="remove-tier">×</button><ul class="items"></ul><button type="button" class="add-item button">+ Ajouter un personnage</button></li>');
      saveData();
    }).on('click','.remove-tier',function(){ $(this).closest('.tier').remove(); saveData(); })
    .on('click','.add-item',function(){
      var $t=$(this).closest('.tier'),tIdx=$t.data('tier'),iIdx=$t.find('.item').length;
      var gOpts='<option value="">-- Groupe --</option>'; $('#groups-list .group').each(function(){var n=$(this).find('input[name^="group_name"]').val();if(n)gOpts+='<option value="'+n+'">'+n+'</option>';});
      var eOpts='<option value="">-- Élément --</option>'; $('#elements-list .element').each(function(){var n=$(this).find('input[name^="element_name"]').val();if(n)eOpts+='<option value="'+n+'">'+n+'</option>';});
      var rOpts='<option value="">-- Rareté --</option>'; $('#rarities-list .rarity').each(function(){var n=$(this).find('input[name^="rarity_name"]').val();if(n)rOpts+='<option value="'+n+'">'+n+'</option>';});
      $t.find('.items').append('<li class="item" data-item="'+iIdx+'"><input type="text" name="item_label['+tIdx+']['+iIdx+']" placeholder="Nom du personnage" /><select name="item_group['+tIdx+']['+iIdx+']">'+gOpts+'</select><select name="item_element['+tIdx+']['+iIdx+']">'+eOpts+'</select><select name="item_rarity['+tIdx+']['+iIdx+']">'+rOpts+'</select><div class="item-strength-links" data-wrapper><strong>Forces de combat :</strong> <button type="button" class="add-strength-link button">+ Ajouter</button><ul class="strength-links-list"></ul></div><input type="text" name="item_image['+tIdx+']['+iIdx+']" placeholder="URL image" /><input type="text" name="item_link['+tIdx+']['+iIdx+']" placeholder="Lien" /><button type="button" class="remove-item">×</button></li>');
      saveData();
    }).on('click','.remove-item',function(){ $(this).closest('.item').remove(); saveData(); })
    .on('click','.add-strength-link',function(e){
      e.preventDefault();
      var $wrap=$(this).closest('.item'),tIdx=$wrap.closest('.tier').data('tier'),iIdx=$wrap.data('item'),lIdx=$wrap.find('.strength-link').length;
      var sOpts='<option value="">-- Force --</option>'; $('#strengths-list .strength-global').each(function(){var n=$(this).find('input[name^="strength_name"]').val();if(n)sOpts+='<option value="'+n+'">'+n+'</option>';});
      var tOpts='<option value="">-- Palier --</option>'; $('#tiers-list .tier > input[name^="tier_name"]').each(function(){var n=$(this).val();if(n)tOpts+='<option value="'+n+'">'+n+'</option>';});
      $wrap.find('.strength-links-list').append('<li class="strength-link" data-link="'+lIdx+'"><select name="item_strength_link_name['+tIdx+']['+iIdx+']['+lIdx+']">'+sOpts+'</select><select name="item_strength_link_tier['+tIdx+']['+iIdx+']['+lIdx+']">'+tOpts+'</select><button type="button" class="remove-strength-link">×</button></li>');
      saveData();
    }).on('click','.remove-strength-link',function(){ $(this).closest('.strength-link').remove(); saveData(); })
    .on('change','input, select', saveData);
});