const nav=document.getElementById('site-nav');
if(nav) window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',window.scrollY>30),{passive:true});
const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('in');observer.unobserve(entry.target)}}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
document.querySelectorAll('[data-faq]').forEach(item=>item.querySelector('button')?.addEventListener('click',()=>item.classList.toggle('open')));
const canvas=document.getElementById('stars');
if(canvas && !matchMedia('(prefers-reduced-motion: reduce)').matches){const ctx=canvas.getContext('2d');let stars=[];const resize=()=>{canvas.width=innerWidth;canvas.height=innerHeight;stars=Array.from({length:Math.min(150,Math.floor(innerWidth*innerHeight/11000))},()=>({x:Math.random()*canvas.width,y:Math.random()*canvas.height,r:Math.random()*.9+.2,a:Math.random()*.45+.1,p:Math.random()*Math.PI*2}))};resize();addEventListener('resize',resize,{passive:true});const tick=t=>{ctx.clearRect(0,0,canvas.width,canvas.height);stars.forEach(s=>{ctx.globalAlpha=s.a*(.65+.35*Math.sin(t*.001+s.p));ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fill()});requestAnimationFrame(tick)};requestAnimationFrame(tick)}

const dsMenu=document.querySelector('.ds-menu-toggle'); const dsMobile=document.querySelector('.ds-mobile-nav'); if(dsMenu&&dsMobile){dsMenu.addEventListener('click',()=>{const open=dsMobile.classList.toggle('open');dsMenu.setAttribute('aria-expanded',open?'true':'false');}); dsMobile.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{dsMobile.classList.remove('open');dsMenu.setAttribute('aria-expanded','false')}));}

// V35: works slideshow + portfolio filters
const workShowcase = document.querySelector('[data-work-slideshow]');
if (workShowcase) {
  const slides = [...workShowcase.querySelectorAll('[data-slide]')];
  const dots = [...workShowcase.querySelectorAll('[data-slide-dot]')];
  let current = 0;
  let timer = null;
  const showSlide = (index) => {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => {
      const active = i === current;
      slide.classList.toggle('is-active', active);
      slide.setAttribute('aria-hidden', active ? 'false' : 'true');
    });
    dots.forEach((dot, i) => {
      const active = i === current;
      dot.classList.toggle('is-active', active);
      dot.setAttribute('aria-selected', active ? 'true' : 'false');
    });
  };
  const start = () => { clearInterval(timer); timer = setInterval(() => showSlide(current + 1), 6000); };
  workShowcase.querySelector('[data-slide-prev]')?.addEventListener('click', () => { showSlide(current - 1); start(); });
  workShowcase.querySelector('[data-slide-next]')?.addEventListener('click', () => { showSlide(current + 1); start(); });
  dots.forEach((dot, i) => dot.addEventListener('click', () => { showSlide(i); start(); }));
  workShowcase.addEventListener('mouseenter', () => clearInterval(timer));
  workShowcase.addEventListener('mouseleave', start);
  showSlide(0); start();
}

const workFilters = document.querySelectorAll('[data-work-filter]');
const workCards = document.querySelectorAll('[data-project-category]');
if (workFilters.length && workCards.length) {
  workFilters.forEach(filter => filter.addEventListener('click', () => {
    const key = filter.dataset.workFilter;
    workFilters.forEach(btn => btn.classList.toggle('is-active', btn === filter));
    workCards.forEach(card => card.classList.toggle('is-hidden', key !== 'all' && card.dataset.projectCategory !== key));
  }));
}

const workModal = document.querySelector('[data-work-modal]');
if (workModal && Array.isArray(window.digitalStarProjects)) {
  const modalImage = workModal.querySelector('[data-modal-image]');
  const modalCategory = workModal.querySelector('[data-modal-category]');
  const modalTitle = workModal.querySelector('[data-modal-title]');
  const modalDescription = workModal.querySelector('[data-modal-description]');
  const modalMeta = workModal.querySelector('[data-modal-meta]');
  const projectButtons = document.querySelectorAll('[data-project-id]');
  const close = () => { workModal.classList.remove('is-open'); workModal.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };
  projectButtons.forEach(button => button.addEventListener('click', () => {
    const project = window.digitalStarProjects[Number(button.dataset.projectId)];
    if (!project) return;
    modalImage.src = `/` + project.image;
    modalImage.alt = project.title + ' preview';
    modalCategory.textContent = project.category;
    modalTitle.textContent = project.title;
    modalDescription.textContent = project.description;
    modalMeta.innerHTML = project.meta.map(tag => `<span>${tag}</span>`).join('');
    workModal.classList.add('is-open');
    workModal.setAttribute('aria-hidden','false');
    document.body.style.overflow='hidden';
  }));
  workModal.querySelectorAll('[data-modal-close]').forEach(button => button.addEventListener('click', close));
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && workModal.classList.contains('is-open')) close(); });
}
