import { type FormEvent, useMemo, useState } from 'react';
import { ArrowRight, Check, ChevronRight, Clock3, FileText, Globe2, Headphones, Laptop2, Menu, MessageCircle, MonitorCog, Palette, PenLine, Printer, Search, Send, ShieldCheck, X } from 'lucide-react';

type Service = {
  id: string;
  title: string;
  description: string;
  icon: typeof Printer;
};

type RequestRecord = {
  reference: string;
  service: string;
  name: string;
};

const services: Service[] = [
  { id: 'internet', title: 'Internet & IT cafe', description: 'Reliable browsing, online forms, email, uploads and everyday computer support.', icon: Globe2 },
  { id: 'printing', title: 'Printing & copying', description: 'Sharp black-and-white or colour prints, copies and scans for work or school.', icon: Printer },
  { id: 'design', title: 'Design & branding', description: 'Flyers, business cards, posters and practical artwork that looks like you.', icon: Palette },
  { id: 'documents', title: 'Document services', description: 'CV typing, formatting, laminating, binding and careful document preparation.', icon: FileText },
  { id: 'stationery', title: 'Stationery supplies', description: 'The pens, paper and essentials that keep school, home and business moving.', icon: PenLine },
  { id: 'consultancy', title: 'Tech consultancy', description: 'Straight answers for devices, websites, digital workflows and small business setup.', icon: MonitorCog },
];

function App() {
  const [menuOpen, setMenuOpen] = useState(false);
  const [selectedService, setSelectedService] = useState('printing');
  const [request, setRequest] = useState<RequestRecord | null>(null);
  const [toast, setToast] = useState('');
  const [trackValue, setTrackValue] = useState('');
  const [trackResult, setTrackResult] = useState<'found' | 'missing' | null>(null);

  const selectedServiceTitle = useMemo(
    () => services.find((service) => service.id === selectedService)?.title ?? 'Printing & copying',
    [selectedService],
  );

  const showToast = (message: string) => {
    setToast(message);
    window.setTimeout(() => setToast(''), 3600);
  };

  const goToRequest = (serviceId?: string) => {
    if (serviceId) setSelectedService(serviceId);
    setMenuOpen(false);
    window.setTimeout(() => document.getElementById('request')?.scrollIntoView({ behavior: 'smooth' }), 20);
  };

  const submitRequest = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    const name = String(form.get('name') || 'Customer');
    const reference = `DSC-${new Date().getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
    setRequest({ reference, service: selectedServiceTitle, name });
    showToast('Your request draft is ready to share with our team.');
    event.currentTarget.reset();
  };

  const trackRequest = (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const normalized = trackValue.trim().toUpperCase();
    setTrackResult(request && normalized === request.reference ? 'found' : 'missing');
  };

  return (
    <div className="site-shell">
      <div className="topbar">
        <div className="container-wide topbar-inner">
          <span className="topbar-note"><span aria-hidden="true">●</span> Open today <strong>07:30–19:00</strong></span>
          <div className="topbar-contact"><span>Mbagala, Dar es Salaam</span><span>Walk in or start online</span></div>
        </div>
      </div>

      <header className="site-nav">
        <div className="container-wide nav-inner">
          <a className="brand" href="#top" data-testid="link-brand" onClick={() => setMenuOpen(false)}>
            <span className="brand-mark" aria-hidden="true">D</span>
            <span className="brand-wordmark">Digital Star<small>Consultants</small></span>
          </a>
          <nav className="nav-links" aria-label="Main navigation">
            <a href="#services" data-testid="link-services">Services</a>
            <a href="#how-it-works" data-testid="link-how-it-works">How it works</a>
            <a href="#track" data-testid="link-track">Track a request</a>
            <a href="#contact" data-testid="link-contact">Contact</a>
          </nav>
          <button className="button-primary nav-cta" type="button" onClick={() => goToRequest()} data-testid="button-start-request">Start a request <ArrowRight size={15} /></button>
          <button className="mobile-menu-button" type="button" onClick={() => setMenuOpen(!menuOpen)} aria-expanded={menuOpen} aria-label={menuOpen ? 'Close menu' : 'Open menu'} data-testid="button-mobile-menu">
            {menuOpen ? <X size={25} /> : <Menu size={25} />}
          </button>
        </div>
        {menuOpen && (
          <nav className="mobile-menu container-wide" aria-label="Mobile navigation">
            <a href="#services" onClick={() => setMenuOpen(false)} data-testid="mobile-link-services">Services</a>
            <a href="#how-it-works" onClick={() => setMenuOpen(false)} data-testid="mobile-link-how-it-works">How it works</a>
            <a href="#track" onClick={() => setMenuOpen(false)} data-testid="mobile-link-track">Track a request</a>
            <a href="#contact" onClick={() => setMenuOpen(false)} data-testid="mobile-link-contact">Contact</a>
            <button className="button-primary" type="button" onClick={() => goToRequest()} data-testid="mobile-button-start-request">Start a request <ArrowRight size={15} /></button>
          </nav>
        )}
      </header>

      <main id="top">
        <section className="hero">
          <div className="container-wide hero-inner">
            <div className="hero-content">
              <span className="eyebrow">Your local digital partner</span>
              <h1>Get important work <em>done.</em></h1>
              <p className="hero-intro">From a clean CV to a business website, Digital Star Consultants helps Mbagala move forward — one practical digital task at a time.</p>
              <div className="hero-actions">
                <button className="button-primary" type="button" onClick={() => goToRequest()} data-testid="button-hero-request">Start a service request <ArrowRight size={16} /></button>
                <a className="button-secondary" href="#services" data-testid="link-hero-services">Explore services <ChevronRight size={16} /></a>
              </div>
              <div className="hero-proof">
                <span className="proof-item"><ShieldCheck size={15} /> Clear pricing before we begin</span>
                <span className="proof-item"><Clock3 size={15} /> Fast turnaround on everyday jobs</span>
              </div>
            </div>
            <div className="hero-art" aria-label="A graphic representing connected digital services">
              <div className="hero-card">
                <div className="signal-lines" />
                <div className="hero-poster">
                  <div className="poster-kicker">D.S.C / 2024</div>
                  <div className="poster-title">MAKE<br />IT<br />MOVE</div>
                  <div className="poster-line"><span>MBAGALA</span><span>01</span></div>
                </div>
              </div>
              <div className="hero-sticker"><Headphones size={18} /><span>Here to<br />help</span></div>
            </div>
          </div>
          <div className="hero-bottom-edge" />
        </section>

        <div className="marquee" aria-label="Our services">
          <div className="container-wide marquee-track">
            <span className="marquee-item">Print</span><span className="marquee-item">Design</span><span className="marquee-item">Connect</span><span className="marquee-item">Solve</span><span className="marquee-item">Move forward</span>
          </div>
        </div>

        <section className="section" id="services">
          <div className="container-wide">
            <div className="section-header">
              <div className="section-header-copy">
                <span className="eyebrow">One place, many solutions</span>
                <h2 className="section-heading">The practical<br />stuff, handled.</h2>
                <p className="section-copy">No need to visit five different places. Tell us what you need and we’ll help you choose the simplest way to get it finished.</p>
              </div>
              <a className="button-ghost" href="#request" data-testid="link-services-request">Tell us what you need <ArrowRight size={16} /></a>
            </div>
            <div className="services-grid">
              {services.map((service) => {
                const Icon = service.icon;
                return (
                  <article className="service-card" key={service.id} data-testid={`card-service-${service.id}`}>
                    <div>
                      <div className="service-icon"><Icon size={21} /></div>
                      <h3 className="service-title">{service.title}</h3>
                      <p className="service-desc">{service.description}</p>
                    </div>
                    <button className="service-link" type="button" onClick={() => goToRequest(service.id)} data-testid={`button-request-${service.id}`}>Request this <ArrowRight size={15} /></button>
                  </article>
                );
              })}
            </div>
          </div>
        </section>

        <section className="section dark-band" id="how-it-works">
          <div className="container-wide why-grid">
            <div className="why-intro">
              <span className="eyebrow">A better way to begin</span>
              <h2 className="section-heading">Simple for you.<br />Serious about<br />the details.</h2>
              <p className="section-copy">Whether you’re sending a document from your phone or walking in with a thumb drive, we meet you where you are.</p>
            </div>
            <div className="why-list">
              <div className="why-row"><span className="why-number">01</span><div><h3>Tell us the job</h3><p>Choose a service online or describe it in your own words. No account, no complicated brief.</p></div><ChevronRight size={18} /></div>
              <div className="why-row"><span className="why-number">02</span><div><h3>We confirm the details</h3><p>Our team checks the files, timing and finish you need, then gives you a clear next step.</p></div><ChevronRight size={18} /></div>
              <div className="why-row"><span className="why-number">03</span><div><h3>Pick up or get connected</h3><p>Collect your finished work in Mbagala or let’s get your digital project moving remotely.</p></div><ChevronRight size={18} /></div>
            </div>
          </div>
        </section>

        <section className="section request-section" id="request">
          <div className="container-wide request-grid">
            <div className="request-aside">
              <span className="eyebrow">Start without an account</span>
              <h2 className="section-heading">Let’s get<br />your job<br />moving.</h2>
              <p className="section-copy">Leave the basics below. This prototype gives you a reference number so you know exactly what to mention when you call or visit us.</p>
              <div className="request-notes">
                <span className="note-item"><Check size={16} /> Attachments can be brought to the shop after you submit.</span>
                <span className="note-item"><Check size={16} /> We’ll confirm price and timing before work starts.</span>
                <span className="note-item"><Check size={16} /> Prefer to talk? Call <strong>+255 742 041 505</strong>.</span>
              </div>
            </div>
            <div className="form-shell">
              {request ? (
                <div className="form-success" data-testid="request-success">
                  <div className="success-icon"><Check size={27} /></div>
                  <h3>Good start, {request.name.split(' ')[0]}.</h3>
                  <p>Your request outline for <strong>{request.service}</strong> is ready. Keep this reference and share it with our team by phone or when you visit.</p>
                  <div className="reference-box"><span className="reference-label">Your reference</span><span className="reference-number" data-testid="text-request-reference">{request.reference}</span></div>
                  <button className="button-primary" type="button" onClick={() => setRequest(null)} data-testid="button-new-request">Start another request <ArrowRight size={15} /></button>
                </div>
              ) : (
                <form onSubmit={submitRequest}>
                  <div className="form-header"><h3>Service request</h3><span className="form-step">01 / 01</span></div>
                  <div className="form-grid">
                    <div className="field field-full"><label htmlFor="service">What can we help with?</label><select id="service" value={selectedService} onChange={(event) => setSelectedService(event.target.value)} data-testid="select-service">{services.map((service) => <option value={service.id} key={service.id}>{service.title}</option>)}</select></div>
                    <div className="field"><label htmlFor="name">Your name</label><input id="name" name="name" required placeholder="e.g. Asha M." data-testid="input-request-name" /></div>
                    <div className="field"><label htmlFor="phone">Phone number</label><input id="phone" name="phone" required type="tel" placeholder="+255 7xx xxx xxx" data-testid="input-request-phone" /></div>
                    <div className="field field-full"><label htmlFor="details">A few details</label><textarea id="details" name="details" required placeholder="Tell us what you need, how many pages or when you need it..." data-testid="textarea-request-details" /></div>
                  </div>
                  <button className="button-primary form-submit" type="submit" data-testid="button-submit-request"><Send size={16} /> Create request reference</button>
                  <p className="form-footnote">No account needed. This is a local prototype — call us to confirm the job.</p>
                </form>
              )}
            </div>
          </div>
        </section>

        <section className="track-section" id="track">
          <div className="container-wide track-grid">
            <div>
              <span className="eyebrow" style={{ color: 'hsl(169 52% 30%)' }}>Already got a reference?</span>
              <h2 className="section-heading">Know where<br />things stand.</h2>
              <p className="section-copy">Enter your request reference for a quick status check. In this prototype, the reference created above can be tracked on this device.</p>
              <form className="track-form" onSubmit={trackRequest}>
                <Search size={18} aria-hidden="true" />
                <label className="sr-only" htmlFor="track-reference">Request reference number</label>
                <input id="track-reference" value={trackValue} onChange={(event) => setTrackValue(event.target.value)} placeholder="DSC-2024-0000" required data-testid="input-track-reference" />
                <button className="button-primary" type="submit" data-testid="button-track-request">Check status</button>
              </form>
              {trackResult === 'found' && request && <div className="track-result" data-testid="status-track-found"><strong>Request received</strong><p>{request.reference} is in our queue for {request.service}. We’ll confirm timing with you directly.</p><div className="track-steps"><span className="track-step active">Received</span><span className="track-step">Confirmed</span><span className="track-step">Ready</span></div></div>}
              {trackResult === 'missing' && <div className="track-result error" data-testid="status-track-missing">We couldn’t find that reference on this device. Check the number or call us on <strong>+255 742 041 505</strong>.</div>}
            </div>
            <div aria-hidden="true">
              <div className="track-steps"><span className="track-step active">You send it</span><span className="track-step">We review</span><span className="track-step">You collect</span></div>
            </div>
          </div>
        </section>

        <section className="contact-section" id="contact">
          <div className="container-wide contact-grid">
            <div>
              <span className="eyebrow">Come by, call, or write</span>
              <h2 className="section-heading">Good work<br />starts with<br />a hello.</h2>
              <div className="contact-card">
                <div className="contact-detail"><MessageCircle size={18} /><span>Call or WhatsApp</span><a href="tel:+255742041505" data-testid="link-phone">+255 742 041 505</a></div>
                <div className="contact-detail"><Globe2 size={18} /><span>Find us</span><strong data-testid="text-address">Mbagala, Dar es Salaam</strong></div>
                <div className="contact-detail wide"><Clock3 size={18} /><span>Typical opening hours</span><strong data-testid="text-hours">Monday – Saturday, 07:30 – 19:00</strong></div>
              </div>
            </div>
            <div className="hours-card">
              <h3>What to bring</h3>
              <div className="hours-row"><span>For printing</span><strong>Your file on phone or USB</strong></div>
              <div className="hours-row"><span>For design</span><strong>Your idea, logo or example</strong></div>
              <div className="hours-row"><span>For consultancy</span><strong>Your questions and goals</strong></div>
              <div className="hours-foot"><Laptop2 size={17} /> We’ll take it from there.</div>
            </div>
          </div>
        </section>
      </main>

      <footer className="footer">
        <div className="container-wide footer-inner">
          <span className="footer-copy">© 2024 Digital Star Consultants. Practical digital help in Mbagala.</span>
          <div className="footer-links"><a href="#services" data-testid="footer-link-services">Services</a><a href="#request" data-testid="footer-link-request">Start a request</a><a href="#contact" data-testid="footer-link-contact">Contact</a></div>
        </div>
      </footer>
      {toast && <div className="toast" role="status" data-testid="status-toast"><Check size={17} /> {toast}</div>}
    </div>
  );
}

export default App;