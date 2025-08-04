
/* v1.67d - robust force filter */
document.addEventListener('DOMContentLoaded', function(){
  const grid = document.querySelector('.tier-grid');
  if(!grid) return;

  const bar = document.createElement('div');
  bar.id = 'tl-filters';
  grid.parentNode.insertBefore(bar, grid);

  bar.innerHTML = `
    <div class="force-block">
    <input type="search" id="tl-search" placeholder="Rechercher un personnage...">
    <select id="tl-filter-group"><option value="">Groupe</option></select>
    <select id="tl-filter-element"><option value="">Élément</option></select>
    <select id="tl-filter-rarity"><option value="">Rareté</option></select>
    <select id="tl-filter-force-name"><option value="">Force</option></select>
    <select id="tl-filter-force-tier" disabled><option value="">Palier</option></select></div>
    <button type="button" class="reset-btn">Reset</button>
  `;

  const selGroup  = bar.querySelector('#tl-filter-group');
  const selElem   = bar.querySelector('#tl-filter-element');
  const selRar    = bar.querySelector('#tl-filter-rarity');
  const selFName  = bar.querySelector('#tl-filter-force-name');
  const selFTier  = bar.querySelector('#tl-filter-force-tier');
  const searchInp = bar.querySelector('#tl-search');
  const resetBtn  = bar.querySelector('.reset-btn');

  const items = document.querySelectorAll('.tier-item');

  const setG=new Set(), setE=new Set(), setR=new Set();
  const mapElemIcon = {};
  items.forEach(it=>{
    const en = it.getAttribute('data-element');
    if(en){
      const ic = it.querySelector('.element-icon') ? it.querySelector('.element-icon').getAttribute('src') : '';
      if(ic && !mapElemIcon[en]) mapElemIcon[en]=ic;
    }
  });

  const mapForce = {}
  // Build first select (force names) from global list if provided
  let globalForces = Array.isArray(window.WPTLM_FORCE_NAMES_GLOBAL) ? window.WPTLM_FORCE_NAMES_GLOBAL : [];
; // name -> Set(tiers)

  function ensureDataAttrs(it){
    // if no data-force-names, try scraping tooltip html
    if(!it.getAttribute('data-force-names')){
      const tip = it.querySelector('.tier-tooltip');
      if(tip){
        const names=[], tiers=[];
        tip.querySelectorAll('.tooltip-strength').forEach(row=>{
          const n = row.querySelector('.sname')?.textContent.trim() || '';
          const t = row.querySelector('.square-text')?.textContent.trim() || '';
          if(n){ names.push(n); tiers.push(t); }
        });
        it.setAttribute('data-force-names', names.join(','));
        it.setAttribute('data-force-tiers', tiers.join(','));
      }
    }
  }

  items.forEach(it=>{
    ensureDataAttrs(it);
    const g=it.getAttribute('data-group'); if(g) setG.add(g);
    const e=it.getAttribute('data-element'); if(e) setE.add(e);
    const r=it.getAttribute('data-rarity'); if(r) setR.add(r);

    const names=(it.getAttribute('data-force-names')||'').split(',').map(v=>v.trim()).filter(Boolean);
    const tiers=(it.getAttribute('data-force-tiers')||'').split(',').map(v=>v.trim()).filter(Boolean);
    names.forEach((name,idx)=>{
      if(!name) return;
      if(!mapForce[name]) mapForce[name]=new Set();
      const t=tiers[idx]||'';
      if(t) mapForce[name].add(t);
    });
  });

  const addOptions=(sel,set)=>{ Array.from(set).sort().forEach(v=>{ const o=document.createElement('option'); o.value=v;o.textContent=v; sel.appendChild(o); }); };
  addOptions(selGroup,setG);
  addOptions(selElem,setE);

  // ---- Custom element picker with icons ----
  (function(){
    const sel = selElem;
    sel.style.display='none';
    const wrap = document.createElement('div');
    wrap.className='icon-select';
    sel.parentNode.insertBefore(wrap, sel.nextSibling);

    const btn = document.createElement('button');
    btn.type='button';
    btn.className='icon-select-toggle';
    btn.innerHTML='<span>Élément</span>';
    wrap.appendChild(btn);

    const menu = document.createElement('div');
    menu.className='icon-select-menu';
    wrap.appendChild(menu);

    const addOpt = (value,label)=>{
      const row=document.createElement('div');
      row.className='icon-option';
      row.dataset.value=value;
      const imgSrc = mapElemIcon[value]||'';
      row.innerHTML = (imgSrc?`<img src="${imgSrc}" alt=""/>`:'') + `<span>${label}</span>`;
      menu.appendChild(row);
    };

    addOpt('', 'Tous');
    Array.from(sel.options).forEach(o=>{
      if(o.value) addOpt(o.value,o.textContent);
    });

    function choose(val, label){
      sel.value = val;
      btn.innerHTML = (mapElemIcon[val]?`<img src="${mapElemIcon[val]}" alt=""/> `:'') + `<span>${label||'Élément'}</span>`;
      apply();
      menu.classList.remove('open');
    }

    btn.addEventListener('click', ()=> menu.classList.toggle('open'));
    menu.addEventListener('click', e=>{
      const opt = e.target.closest('.icon-option');
      if(!opt) return;
      choose(opt.dataset.value, opt.querySelector('span').textContent);
    });

    document.addEventListener('click', e=>{
      if(!wrap.contains(e.target)) menu.classList.remove('open');
    });
  })();

  addOptions(selRar,setR);
  Object.keys(mapForce).sort().forEach(name=>{
    const o=document.createElement('option'); o.value=name; o.textContent=name; selFName.appendChild(o);
  });

  function populateTiers(name){
    selFTier.innerHTML='<option value="">Palier</option>';
    if(!name){ selFTier.disabled=true; return; }
    selFTier.disabled=false;
    Array.from(mapForce[name]).sort().forEach(t=>{
      const o=document.createElement('option'); o.value=t; o.textContent=t; selFTier.appendChild(o);
    });
  }

  function apply(){
    const q = searchInp.value.toLowerCase();
    const g = selGroup.value;
    const e = selElem.value;
    const r = selRar.value;
    const fname = selFName.value;
    const ftier = selFTier.value;

    items.forEach(it=>{
      let ok=true;
      if(q){
        const name=(it.querySelector('.tier-item-label')?.textContent||'').toLowerCase();
        ok = name.includes(q);
      }
      if(ok && g) ok = it.getAttribute('data-group')===g;
      if(ok && e) ok = it.getAttribute('data-element')===e;
      if(ok && r) ok = it.getAttribute('data-rarity')===r;
      if(ok && fname){
        const names=(it.getAttribute('data-force-names')||'').split(',').map(v=>v.trim());
        ok = names.includes(fname);
      }
      if(ok && ftier){
        const tiers=(it.getAttribute('data-force-tiers')||'').split(',').map(v=>v.trim());
        ok = tiers.includes(ftier);
      }
      it.style.display = ok ? '' : 'none';
    });
  }

  selFName.addEventListener('change', ()=>{
    populateTiers(selFName.value);
    apply();
  });

  [searchInp, selGroup, selElem, selRar, selFTier].forEach(el=> el.addEventListener('input', apply));
  resetBtn.addEventListener('click', ()=>{
    searchInp.value='';
    selGroup.value='';
    selElem.value='';
    selRar.value='';
    selFName.value='';
    populateTiers('');
    selFTier.value='';
    apply();
  });
});
