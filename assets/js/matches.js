/* Matches — real data from API */
(function(){
  var ALL = [];
  var favSet = {};
  var currentUserId = 0;
  var isApproved = false;
  var state = {search:'', gender:'all', ageMin:18, ageMax:70, hMin:54, hMax:76, salaryMin:1, salaryMax:50, religions:[], tongues:[], locations:[], education:[], professions:[], maritalStatuses:[], castes:[], sort:'recommended'};
  var AGE_MIN=18, AGE_MAX=70, H_MIN=54, H_MAX=76, SAL_MIN=1, SAL_MAX=50;
  function $(s,c){return (c||document).querySelector(s)}
  function $all(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))}
  function fmt(n){return n.toLocaleString('en-IN')}
  function ftIn(v){return Math.floor(v/12)+"'"+(v%12)+'"'}
  function esc(s){return String(s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]})}
  function parseHeight(str){
    if(!str) return null;
    var m = str.match(/(\d+)'(\d+)/);
    if(m) return parseInt(m[1])*12 + parseInt(m[2]);
    var cm = parseInt(str);
    if(cm > 30 && cm < 220) return Math.round(cm / 2.54);
    return null;
  }
  function parseSalary(str){
    if(!str) return null;
    var m = str.match(/(\d+)/);
    return m ? parseInt(m[1]) : null;
  }
  function ageFromDOB(dob){
    if(!dob) return null;
    var d = new Date(dob);
    var now = new Date();
    var a = now.getFullYear() - d.getFullYear();
    var m = now.getMonth() - d.getMonth();
    if(m < 0 || (m === 0 && now.getDate() < d.getDate())) a--;
    return a;
  }
  function showToast(msg){
    var t = document.createElement('div');
    t.className = 'm-toast';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.classList.add('show'); }, 10);
    setTimeout(function(){ t.classList.remove('show'); setTimeout(function(){ t.remove(); }, 300); }, 2600);
  }
  function fetchFavourites(){
    if(!currentUserId) return Promise.resolve();
    return fetch('./favourites_api.php')
      .then(function(r){ return r.json(); })
      .then(function(data){ (data.favourites||[]).forEach(function(id){ favSet[id]=1; }); })
      .catch(function(){});
  }
  function csrf(){ var m=document.querySelector('meta[name="csrf-token"]'); return m?m.content:''; }
  function toggleFavourite(e, profileId, btn){
    e.stopPropagation();
    e.preventDefault();
    if(!currentUserId){
      showToast('Please log in to save favourites.');
      setTimeout(function(){ window.location.href='./login.php'; }, 900);
      return;
    }
    var icon = $('i', btn);
    var headers = {'Content-Type':'application/json','X-CSRF-Token':csrf()};
    if(favSet[profileId]){
      fetch('./favourites_api.php',{method:'DELETE',headers:headers,body:JSON.stringify({profile_id:profileId})})
        .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw d; return d; }); })
        .then(function(){ delete favSet[profileId]; btn.classList.remove('fav-active'); icon.className='bi bi-heart'; showToast('Removed from favourites'); })
        .catch(function(err){ showToast(err && err.error ? err.error : 'Could not remove favourite'); });
    } else {
      fetch('./favourites_api.php',{method:'POST',headers:headers,body:JSON.stringify({profile_id:profileId})})
        .then(function(r){ return r.json().then(function(d){ if(!r.ok) throw d; return d; }); })
        .then(function(data){
          if(data.error){ showToast(data.error); return; }
          favSet[profileId]=1; btn.classList.add('fav-active'); icon.className='bi bi-heart-fill'; showToast('Added to favourites');
        })
        .catch(function(err){ showToast(err && err.error ? err.error : 'Could not add favourite'); });
    }
  }
  function fetchProfiles(){
    // Fetch all pages of profiles from the paginated API
    var perPage = 100;
    var seen = {};
    var allPages = [];
    function loadPage(page){
      return fetch('./matches_api.php?page='+page+'&per_page='+perPage)
        .then(function(r){ return r.json(); })
        .then(function(data){
          var rows = data.profiles || [];
          rows.forEach(function(p){ if(!seen[p.id]){ seen[p.id]=1; allPages.push(p); } });
          if(allPages.length < (data.total||0) && rows.length >= perPage){
            return loadPage(page+1);
          }
          return allPages;
        });
    }
    return loadPage(1)
      .then(function(list){ ALL = list; })
      .catch(function(){ ALL = []; });
  }
  function passes(p){
    if(state.gender!=='all' && p.gender!==state.gender) return false;
    var age = p.age != null ? p.age : ageFromDOB(p.dob);
    if(age != null && (age < state.ageMin || age > state.ageMax)) return false;
    var hIn = p.heightInches != null ? p.heightInches : parseHeight(p.height);
    if(hIn != null && (hIn < state.hMin || hIn > state.hMax)) return false;
    var sal = parseSalary(p.salary);
    if(sal != null && (sal < state.salaryMin || sal > state.salaryMax)) return false;
    if(state.religions.length && state.religions.indexOf(p.religion)===-1) return false;
    if(state.tongues.length && state.tongues.indexOf(p.tongue)===-1) return false;
    if(state.locations.length && state.locations.indexOf(p.city)===-1) return false;
    if(state.education.length && state.education.indexOf(p.education)===-1) return false;
    if(state.professions.length && state.professions.indexOf(p.profession)===-1) return false;
    if(state.maritalStatuses.length && state.maritalStatuses.indexOf(p.marital)===-1) return false;
    if(state.castes.length && state.castes.indexOf(p.caste)===-1) return false;
    if(state.search){ var hay=[p.name,p.city,p.religion,p.education,p.profession,p.tongue].join(' ').toLowerCase(); if(hay.indexOf(state.search.toLowerCase())===-1) return false; }
    return true;
  }
  function applySorting(list){
    var s=state.sort;
    return list.slice().sort(function(a,b){
      switch(s){case 'newest':return b.created-a.created; case 'recently_active':return b.active-a.active; case 'age_asc':return (a.age||0)-(b.age||0); case 'age_desc':return (b.age||0)-(a.age||0); default:return b.created-a.created;}
    });
  }
  function cardHTML(p){
    var age = p.age != null ? p.age : '';
    var h = p.height || '';
    var photo = p.photo ? '<img src="'+esc(p.photo)+'" alt="'+esc(p.name)+'" class="m-card-img" loading="lazy" draggable="false" oncontextmenu="return false" ondragstart="return false" style="pointer-events:auto;">' : '<i class="bi bi-person-circle"></i>';
    var isFav = favSet[p.id];
    var favClass = isFav ? ' fav-active' : '';
    var favIcon = isFav ? 'bi-heart-fill' : 'bi-heart';
    return '<article class="m-card" data-id="'+p.id+'" tabindex="0">'
      +'<div class="m-card-head">'+photo+'<button type="button" class="m-fav-btn'+favClass+'" data-pid="'+p.id+'" title="Add to Favourites" aria-label="Add to Favourites"><i class="bi '+favIcon+'"></i></button></div>'
      +'<div class="m-card-body">'
      +'<h3 class="m-card-title">'+esc(p.name)+(age ? ', '+age : '')+'</h3>'
      +'<p class="m-card-sub">'+(h ? esc(h)+' · ' : '')+esc(p.city)+(p.tongue ? ' · '+esc(p.tongue) : '')+'</p>'
      +'<p class="m-card-meta">'+(p.profession ? esc(p.profession)+' · ' : '')+(p.education ? esc(p.education)+' · ' : '')+esc(p.religion)+'</p>'
      +'<div class="m-card-actions"><button type="button" class="m-btn m-btn-primary" style="flex:1">Connect</button></div>'
      +'</div></article>';
  }
  var grid, chipsWrap, countEl, sidebarCountEl, emptyState;
  function renderProfiles(){
    var filtered=applySorting(ALL.filter(passes));
    if(countEl) countEl.innerHTML='<strong>'+fmt(filtered.length)+'</strong> profiles found';
    if(sidebarCountEl) sidebarCountEl.textContent=fmt(filtered.length);
    if(!filtered.length){ grid.hidden=true; emptyState.hidden=false; return; }
    emptyState.hidden=true; grid.hidden=false;
    grid.innerHTML=filtered.map(cardHTML).join('');
  }
  function chipData(){
    var chips=[];
    if(!(state.ageMin===18&&state.ageMax===70)) chips.push({key:'age', label:'Age '+state.ageMin+'–'+state.ageMax});
    if(!(state.hMin===54&&state.hMax===76)) chips.push({key:'height', label:'Height '+ftIn(state.hMin)+' — '+ftIn(state.hMax)});
    if(!(state.salaryMin===1&&state.salaryMax===50)) chips.push({key:'salary', label:'₹'+fmt(state.salaryMin)+' – '+fmt(state.salaryMax)+' LPA'});
    if(state.gender!=='all') chips.push({key:'gender', label:state.gender==='women'?'Women':'Men'});
    [['religions','religion'],['tongues','tongue'],['locations','location'],['education','education'],['professions','profession'],['maritalStatuses','marital'],['castes','caste']].forEach(function(pair){ state[pair[0]].forEach(function(v){chips.push({key:pair[0], value:v, label:v})}) });
    if(state.search) chips.push({key:'search', label:'"'+state.search+'"'});
    return chips;
  }
  function renderChips(){
    var chips=chipData();
    if(!chips.length){ chipsWrap.innerHTML=''; return; }
    var html=chips.map(function(c){ var dv=c.value?' data-value="'+esc(c.value)+'"':''; return '<span class="chip">'+esc(c.label)+'<button type="button" data-key="'+c.key+'"'+dv+'>&times;</button></span>'; }).join('');
    chipsWrap.innerHTML=html+'<button type="button" class="clear-all-btn">Clear all</button>';
  }
  function syncControls(scope){
    $all('.filter-checkbox[data-group]',scope).forEach(function(cb){ cb.checked=state[cb.getAttribute('data-group')].indexOf(cb.value)!==-1; });
    $all('input[name="m-gender"]',scope).forEach(function(r){ r.checked=r.value===state.gender; });
    $all('.js-age-min',scope).forEach(function(el){el.value=state.ageMin});
    $all('.js-age-max',scope).forEach(function(el){el.value=state.ageMax});
    $all('.js-h-min',scope).forEach(function(el){el.value=state.hMin});
    $all('.js-h-max',scope).forEach(function(el){el.value=state.hMax});
    $all('.js-salary-min',scope).forEach(function(el){el.value=state.salaryMin});
    $all('.js-salary-max',scope).forEach(function(el){el.value=state.salaryMax});
    updateRange(scope);
  }
  function updateRange(scope){
    $all('.js-age-wrap',scope).forEach(function(w){ var fill=$('.m-fill',w); if(!fill) return; fill.style.left=((state.ageMin-AGE_MIN)/(AGE_MAX-AGE_MIN))*100+'%'; fill.style.right=(100-((state.ageMax-AGE_MIN)/(AGE_MAX-AGE_MIN))*100)+'%'; });
    $all('.js-height-wrap',scope).forEach(function(w){ var fill=$('.m-fill',w); if(!fill) return; fill.style.left=((state.hMin-H_MIN)/(H_MAX-H_MIN))*100+'%'; fill.style.right=(100-((state.hMax-H_MIN)/(H_MAX-H_MIN))*100)+'%'; });
    $all('.js-salary-wrap',scope).forEach(function(w){ var fill=$('.m-fill',w); if(!fill) return; fill.style.left=((state.salaryMin-SAL_MIN)/(SAL_MAX-SAL_MIN))*100+'%'; fill.style.right=(100-((state.salaryMax-SAL_MIN)/(SAL_MAX-SAL_MIN))*100)+'%'; });
    $all('.js-age-display',scope).forEach(function(d){ d.textContent=state.ageMin+' – '+state.ageMax+' years'; });
    $all('.js-height-display',scope).forEach(function(d){ d.textContent=ftIn(state.hMin)+' — '+ftIn(state.hMax); });
    $all('.js-salary-display',scope).forEach(function(d){ d.textContent='₹'+fmt(state.salaryMin)+' – '+fmt(state.salaryMax)+' LPA'; });
  }
  function updateURL(){
    if(!history.replaceState) return;
    var p=new URLSearchParams();
    if(state.gender!=='all') p.set('g',state.gender);
    p.set('ageMin',state.ageMin); p.set('ageMax',state.ageMax); p.set('hMin',state.hMin); p.set('hMax',state.hMax);
    if(state.search) p.set('q',state.search);
    var MAP={rel:'religions', tongue:'tongues', loc:'locations', edu:'education', prof:'professions', ms:'maritalStatuses', cs:'castes'};
    Object.keys(MAP).forEach(function(k){ if(state[MAP[k]].length) p.set(k,state[MAP[k]].join(','))});
    if(state.sort!=='recommended') p.set('sort',state.sort);
    history.replaceState({},'', location.pathname+(p.toString()?'?'+p.toString():''));
  }
  function loadURL(){
    var p=new URLSearchParams(location.search);
    if(p.get('g')) state.gender=p.get('g');
    if(p.get('ageMin')) state.ageMin=Math.max(AGE_MIN,parseInt(p.get('ageMin'),10));
    if(p.get('ageMax')) state.ageMax=Math.min(AGE_MAX,parseInt(p.get('ageMax'),10));
    if(p.get('hMin')) state.hMin=Math.max(H_MIN,parseInt(p.get('hMin'),10));
    if(p.get('hMax')) state.hMax=Math.min(H_MAX,parseInt(p.get('hMax'),10));
    if(p.get('q')) state.search=p.get('q');
    var MAP={rel:'religions', tongue:'tongues', loc:'locations', edu:'education', prof:'professions', ms:'maritalStatuses', cs:'castes'};
    Object.keys(MAP).forEach(function(k){ if(p.get(k)) state[MAP[k]]=p.get(k).split(',')});
    var stored=null; try{stored=localStorage.getItem('matrimony_sort')}catch(e){}
    state.sort=p.get('sort')||stored||'recommended';
  }
  var urlTimer;
  function refresh(){ renderChips(); renderProfiles(); clearTimeout(urlTimer); urlTimer=setTimeout(updateURL,200); }
  function showSkeleton(n){
    var html=''; for(var i=0;i<n;i++) html+='<div class="skeleton-card"><div class="shimmer skeleton-image"></div><div class="skeleton-body"><div class="shimmer skeleton-row w60"></div><div class="shimmer skeleton-row w80"></div><div class="shimmer skeleton-row w40"></div></div></div>';
    grid.hidden=false; emptyState.hidden=true; grid.innerHTML=html;
  }
  function wire(){
    $all('input[name="m-gender"]').forEach(function(r){ r.addEventListener('change',function(){ state.gender=r.value; syncControls(document); refresh(); })});
    $all('.filter-checkbox[data-group]').forEach(function(cb){
      cb.addEventListener('change',function(){
        var g=cb.getAttribute('data-group'), arr=state[g], idx=arr.indexOf(cb.value);
        if(cb.checked&&idx===-1) arr.push(cb.value);
        if(!cb.checked&&idx>-1) arr.splice(idx,1);
        refresh();
      });
    });
    function bind(sel, loKey, hiKey){
      $all(sel+'-min').forEach(function(lo){ lo.addEventListener('input',function(){ var v=parseInt(lo.value,10); if(v>state[hiKey]-1){v=state[hiKey]-1; lo.value=v} state[loKey]=v; updateRange(document)}); lo.addEventListener('change',refresh); });
      $all(sel+'-max').forEach(function(hi){ hi.addEventListener('input',function(){ var v=parseInt(hi.value,10); if(v<state[loKey]+1){v=state[loKey]+1; hi.value=v} state[hiKey]=v; updateRange(document)}); hi.addEventListener('change',refresh); });
    }
    bind('.js-age','ageMin','ageMax'); bind('.js-h','hMin','hMax'); bind('.js-salary','salaryMin','salaryMax');
    var searchInput=$('#mSearch');
    var t;
    if(searchInput){ searchInput.addEventListener('input',function(){ clearTimeout(t); t=setTimeout(function(){ state.search=searchInput.value.trim(); refresh(); },250)}); }
    var sortSel=$('#sortSelect');
    if(sortSel){ sortSel.addEventListener('change',function(){ state.sort=sortSel.value; try{localStorage.setItem('matrimony_sort',state.sort)}catch(e){} refresh(); }); }
    chipsWrap.addEventListener('click',function(e){
      var btn=e.target.closest('button'); if(!btn) return;
      if(btn.classList.contains('clear-all-btn')){ clearAll(); return; }
      removeChip(btn.getAttribute('data-key'), btn.getAttribute('data-value'));
    });
    function clearAll(){
      state.search=''; if(searchInput) searchInput.value='';
      state.gender='all'; state.ageMin=18; state.ageMax=70; state.hMin=54; state.hMax=76; state.salaryMin=1; state.salaryMax=50;
      ['religions','tongues','locations','education','professions','maritalStatuses','castes'].forEach(function(k){state[k]=[]});
      syncControls(document); refresh();
    }
    function removeChip(key,val){
      switch(key){
        case 'age': state.ageMin=18; state.ageMax=70; break;
        case 'height': state.hMin=54; state.hMax=76; break;
        case 'salary': state.salaryMin=1; state.salaryMax=50; break;
        case 'gender': state.gender='all'; break;
        case 'search': state.search=''; if(searchInput) searchInput.value=''; break;
        default: var arr=state[key]; var idx=arr.indexOf(val); if(idx>-1) arr.splice(idx,1);
      }
      syncControls(document); refresh();
    }
    var ec=$('#emptyClear'); if(ec) ec.addEventListener('click', clearAll);
    grid.addEventListener('click',function(e){
      var favBtn=e.target.closest('.m-fav-btn');
      if(favBtn){
        toggleFavourite(e, parseInt(favBtn.getAttribute('data-pid'),10), favBtn);
        return;
      }
      var card=e.target.closest('.m-card'); if(!card) return;
      var pid = card.getAttribute('data-id');
      if(currentUserId && !isApproved){
        window.location.href='./contact.php?reason=approval';
        return;
      }
      window.location.href='./profile_view.php?id='+(pid?encodeURIComponent(pid):'');
    });
    var drawer=$('#filterDrawer'), backdrop=$('#drawerBackdrop');
    function open(){ drawer.classList.add('open'); backdrop.classList.add('open'); document.body.style.overflow='hidden'; }
    function close(){ drawer.classList.remove('open'); backdrop.classList.remove('open'); document.body.style.overflow=''; drawer.setAttribute('aria-hidden','true'); }
    var btn=$('#mobileFilterBtn'); if(btn) btn.addEventListener('click', open);
    var closeBtn=$('.m-drawer-close',drawer); if(closeBtn) closeBtn.addEventListener('click', close);
    if(backdrop) backdrop.addEventListener('click', close);
    var ca=$('#drawerClear'); if(ca) ca.addEventListener('click', clearAll);
    var sc=$('#sidebarClear'); if(sc) sc.addEventListener('click', clearAll);
    var ap=$('#drawerApply'); if(ap) ap.addEventListener('click', close);
    document.addEventListener('keydown',function(e){ if(e.key==='Escape') close(); });
  }
  function init(){
    grid=$('#profileGrid'); chipsWrap=$('#filterChips'); countEl=$('#resultCount'); sidebarCountEl=$('#sidebarCount'); emptyState=$('#emptyState');
    var mainEl = document.querySelector('.matches-page');
    if(mainEl) currentUserId = parseInt(mainEl.getAttribute('data-user-id')||'0',10);
    if(mainEl) isApproved = mainEl.getAttribute('data-approved') === '1';
    loadURL();
    var si=$('#mSearch'); if(si&&state.search) si.value=state.search;
    var ss=$('#sortSelect'); if(ss) ss.value=state.sort;
    showSkeleton(8); syncControls(document); wire();
    fetchProfiles().then(function(){
      return fetchFavourites();
    }).then(function(){
      renderChips(); renderProfiles(); updateURL();
    });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
