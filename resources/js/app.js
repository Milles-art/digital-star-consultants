const nav=document.getElementById('site-nav');
if(nav) window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',window.scrollY>30),{passive:true});
const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('in');observer.unobserve(entry.target)}}),{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>observer.observe(el));
document.querySelectorAll('[data-faq]').forEach(item=>item.querySelector('button')?.addEventListener('click',()=>item.classList.toggle('open')));
const canvas=document.getElementById('stars');
if(canvas && !matchMedia('(prefers-reduced-motion: reduce)').matches){const ctx=canvas.getContext('2d');let stars=[];const resize=()=>{canvas.width=innerWidth;canvas.height=innerHeight;stars=Array.from({length:Math.min(150,Math.floor(innerWidth*innerHeight/11000))},()=>({x:Math.random()*canvas.width,y:Math.random()*canvas.height,r:Math.random()*.9+.2,a:Math.random()*.45+.1,p:Math.random()*Math.PI*2}))};resize();addEventListener('resize',resize,{passive:true});const tick=t=>{ctx.clearRect(0,0,canvas.width,canvas.height);stars.forEach(s=>{ctx.globalAlpha=s.a*(.65+.35*Math.sin(t*.001+s.p));ctx.fillStyle='#fff';ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fill()});requestAnimationFrame(tick)};requestAnimationFrame(tick)}

const dsMenu=document.querySelector('.ds-menu-toggle'); const dsMobile=document.querySelector('.ds-mobile-nav'); if(dsMenu&&dsMobile){dsMenu.addEventListener('click',()=>{const open=dsMobile.classList.toggle('open');dsMenu.setAttribute('aria-expanded',open?'true':'false');}); dsMobile.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{dsMobile.classList.remove('open');dsMenu.setAttribute('aria-expanded','false')}));}
