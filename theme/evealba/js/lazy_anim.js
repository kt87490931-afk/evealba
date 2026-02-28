(function(){
  if(!('IntersectionObserver' in window)) {
    document.querySelectorAll('[data-lazy-anim]').forEach(function(card){
      activateCard(card);
    });
    return;
  }

  function activateCard(card){
    card.querySelectorAll('[data-wave]').forEach(function(el){
      el.style.cssText += el.getAttribute('data-wave');
      el.removeAttribute('data-wave');
    });
    card.querySelectorAll('[data-motion]').forEach(function(el){
      el.classList.add(el.getAttribute('data-motion'));
      el.removeAttribute('data-motion');
    });
    card.setAttribute('data-anim-active','1');
  }

  function deactivateCard(card){
    card.querySelectorAll('[data-anim-active-wave]').forEach(function(el){
      el.style.animation = '';
      el.style.backgroundSize = '';
      el.setAttribute('data-wave', el.getAttribute('data-anim-active-wave'));
      el.removeAttribute('data-anim-active-wave');
    });
    card.querySelectorAll('[data-anim-active-motion]').forEach(function(el){
      el.classList.remove(el.getAttribute('data-anim-active-motion'));
      el.setAttribute('data-motion', el.getAttribute('data-anim-active-motion'));
      el.removeAttribute('data-anim-active-motion');
    });
    card.removeAttribute('data-anim-active');
  }

  function activateCardSafe(card){
    card.querySelectorAll('[data-wave]').forEach(function(el){
      el.setAttribute('data-anim-active-wave', el.getAttribute('data-wave'));
      el.style.cssText += el.getAttribute('data-wave');
      el.removeAttribute('data-wave');
    });
    card.querySelectorAll('[data-motion]').forEach(function(el){
      el.setAttribute('data-anim-active-motion', el.getAttribute('data-motion'));
      el.classList.add(el.getAttribute('data-motion'));
      el.removeAttribute('data-motion');
    });
    card.setAttribute('data-anim-active','1');
  }

  var observer = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if(entry.isIntersecting){
        if(!entry.target.hasAttribute('data-anim-active')){
          activateCardSafe(entry.target);
        }
      } else {
        if(entry.target.hasAttribute('data-anim-active')){
          deactivateCard(entry.target);
        }
      }
    });
  }, {rootMargin:'200px 0px'});

  document.querySelectorAll('[data-lazy-anim]').forEach(function(card){
    observer.observe(card);
  });
})();
