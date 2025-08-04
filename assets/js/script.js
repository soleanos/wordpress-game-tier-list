
/* v1.65 fixed tooltip if clipped */
document.addEventListener('DOMContentLoaded',function(){
  document.querySelectorAll('.tier-item').forEach(function(item){
    const localTip = item.querySelector('.tier-tooltip');
    if(!localTip) return;
    const fixed = document.createElement('div');
    fixed.className = 'tl-fixed-tip arrow-top';
    document.body.appendChild(fixed);

    function show(){
      // copy content
      fixed.innerHTML = localTip.innerHTML;
      const rc = item.querySelector('img')?.style.borderColor || '';
      if(rc){
        fixed.style.setProperty('--rc', rc);
        const inner = fixed.querySelector('.tier-tooltip-inner');
        if(inner){ inner.style.borderColor = rc; }
      }
      // Decide if clipped
      localTip.style.opacity = '1';
      localTip.style.visibility = 'visible';
      const rect = localTip.getBoundingClientRect();
      const margin = 8;
      const clipped = rect.top < margin || rect.bottom > window.innerHeight - margin ||
                      rect.left < margin || rect.right > window.innerWidth - margin;
      if(!clipped){
        fixed.classList.remove('visible');
        return;
      }
      // Position fixed
      const itemRect = item.getBoundingClientRect();
      fixed.classList.add('visible');
      const tipRect = fixed.getBoundingClientRect(); // update after visible
      let top = itemRect.top - tipRect.height - margin;
      let arrowTop = true;
      if(top < margin){
        top = itemRect.bottom + margin;
        arrowTop = false;
      }
      let left = itemRect.left + itemRect.width/2 - tipRect.width/2;
      if(left < margin) left = margin;
      if(left + tipRect.width > window.innerWidth - margin) left = window.innerWidth - tipRect.width - margin;
      fixed.style.top = (top + window.scrollY) + 'px';
      fixed.style.left = (left + window.scrollX) + 'px';
      fixed.classList.toggle('arrow-top', arrowTop);
      fixed.classList.toggle('arrow-bottom', !arrowTop);
    }
    function hide(){
      fixed.classList.remove('visible');
      localTip.style.opacity = '';
      localTip.style.visibility = '';
    }
    item.addEventListener('mouseenter', show);
    item.addEventListener('mouseleave', hide);
    window.addEventListener('scroll', function(){ if(fixed.classList.contains('visible')) show(); });
    window.addEventListener('resize', function(){ if(fixed.classList.contains('visible')) show(); });
  });
});
