@extends('reports.layout')

@section('title', 'Company Profile')
@section('container_class', 'infographic')

@section('styles')
    /* Reset & Fonts */
    .report-wrapper { font-family: 'Inter', system-ui, sans-serif; color: #1e293b; background: white; }
    
    /* Layout Grid */
    .grid-sections { display: grid; grid-template-columns: 30% 70%; gap: 30px; margin-bottom: 40px; }
    
    /* Header */
    .header { border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
    .header h1 { font-size: 2.5rem; color: #0f172a; text-transform: uppercase; letter-spacing: -1px; line-height: 1; margin: 0; }
    .header .subtitle { font-size: 0.9rem; color: #64748b; margin-top: 10px; max-width: 600px; }
    
    /* Sidebar (Left) */
    .sidebar-box { background: #0f172a; color: white; padding: 30px; border-radius: 4px; height: 100%; }
    .brand-icon { text-align: center; margin-bottom: 40px; }
    .brand-icon svg { width: 80px; height: 80px; stroke: white; opacity: 0.8; }
    .sidebar-section h3 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; margin-bottom: 20px; color: #94a3b8; }
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li { font-size: 0.8rem; margin-bottom: 12px; color: #cbd5e1; }
    .info-list strong { color: white; display: block; margin-bottom: 2px; }

    /* Main Content (Right) */
    .metrics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .metric-card { border: 1px solid #e2e8f0; padding: 20px; text-align: center; border-radius: 4px; }
    .metric-value { font-size: 2rem; font-weight: 700; color: #059669; display: block; }
    .metric-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; }

    /* Services Grid */
    .services-title { text-align: center; text-transform: uppercase; letter-spacing: 2px; font-size: 1rem; color: #334155; margin: 40px 0 20px; position: relative; }
    .services-title:before, .services-title:after { content: ''; position: absolute; top: 50%; width: 40%; height: 1px; background: #e2e8f0; }
    .services-title:before { left: 0; }
    .services-title:after { right: 0; }

    .services-grid { display: display; grid-template-columns: 1fr 1fr; border: 1px solid #e2e8f0; }
    .service-item { padding: 25px; border: 1px solid #e2e8f0; position: relative; }
    .service-number { position: absolute; top: 10px; right: 20px; font-size: 4rem; color: #f1f5f9; font-weight: 900; z-index: 0; line-height: 1; }
    .service-content { position: relative; z-index: 1; }
    .service-content h4 { color: #0f172a; margin: 0 0 10px; font-size: 1.1rem; }
    .service-content p { font-size: 0.85rem; color: #64748b; line-height: 1.5; margin: 0; }

    /* Footer / Testimonials */
    .footer-section { background: #0f172a; color: white; padding: 30px; margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
    .testimonial p { font-style: italic; font-size: 0.9rem; line-height: 1.6; color: #cbd5e1; margin-bottom: 20px; }
    .author { display: flex; align-items: center; gap: 15px; }
    .author-img { width: 40px; height: 40px; background: #334155; border-radius: 50%; }
    .author-info strong { display: block; font-size: 0.9rem; }
    .author-info span { font-size: 0.75rem; color: #94a3b8; }
@endsection

@section('content')
    <div class="header">
        <h1>Professional<br>Company Profile</h1>
        <div class="subtitle">Trusted by 250+ partners worldwide. Provided in 2025, we are leaders in renewable energy technologies and pioneering sustainable solutions.</div>
    </div>

    <div class="grid-sections">
        <!-- Sidebar -->
        <div class="sidebar-box">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
            </div>
            
            <div class="sidebar-section">
                <h3>Key Information</h3>
                <ul class="info-list">
                    <li><strong>Established</strong> 2008</li>
                    <li><strong>Founder/CEO</strong> Jane Smith</li>
                    <li><strong>Headquarters</strong> Silicon Valley, California</li>
                    <li><strong>Sector</strong> Solar Energy Systems</li>
                    <li><strong>Staff</strong> 1,200+</li>
                </ul>
            </div>
            
            <div class="sidebar-section" style="margin-top: 40px;">
                <h3>Contact</h3>
                <ul class="info-list">
                    <li>hello@renewable-tech.com</li>
                    <li>+1 (555) 123-4567</li>
                    <li>www.renewable-tech.com</li>
                </ul>
            </div>
        </div>

        <!-- Right Content -->
        <div class="main-content">
            <div class="metrics-grid">
                <div class="metric-card" style="grid-column: span 2;">
                    <span class="metric-value" style="color: #0f172a; font-size: 2.5rem;">$1,000,000,000</span>
                    <span class="metric-label">Annual Gross Revenue in 2025</span>
                </div>
                <div class="metric-card">
                    <span class="metric-value">20%</span>
                    <span class="metric-label">Profit Margin</span>
                </div>
                <div class="metric-card">
                    <span class="metric-value">40%</span>
                    <span class="metric-label">Market Share</span>
                </div>
                <div class="metric-card" style="grid-column: span 2; border: none; background: #f8fafc;">
                    <span class="metric-value" style="color: #0f172a;">100%</span>
                    <span class="metric-label">Customer Satisfaction Guarantee</span>
                </div>
            </div>
            
            <div style="margin-top: 40px;">
                <h3 style="font-size: 0.9rem; text-transform: uppercase; color: #64748b; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Why Us?</h3>
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="width: 20px; padding-top: 5px;">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.9rem; color: #0f172a; margin-bottom: 5px;">Innovation and Expertise</strong>
                        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Our team pioneers cutting-edge solar technologies, ensuring you stay ahead of the energy curve.</p>
                    </div>
                </div>
                <div style="display: flex; gap: 20px;">
                    <div style="width: 20px; padding-top: 5px;">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <strong style="display: block; font-size: 0.9rem; color: #0f172a; margin-bottom: 5px;">Quality and Durability</strong>
                        <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Rigorous testing protocols guarantee products that withstand environmental stressors.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="services-title">Our Services</div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="service-item">
            <span class="service-number">1</span>
            <div class="service-content">
                <h4>Solar Panels</h4>
                <p>High-performance photovoltaic modules aiming for maximum energy capture efficiency.</p>
            </div>
        </div>
        <div class="service-item">
            <span class="service-number">2</span>
            <div class="service-content">
                <h4>Energy Storage</h4>
                <p>Cutting-edge storage solutions ensuring consistent energy supply day and night.</p>
            </div>
        </div>
        <div class="service-item">
            <span class="service-number">3</span>
            <div class="service-content">
                <h4>Solar Inverters</h4>
                <p>State-of-the-art inverters intended to convert renewable DC to stable AC power.</p>
            </div>
        </div>
        <div class="service-item">
            <span class="service-number">4</span>
            <div class="service-content">
                <h4>Grid Monitoring</h4>
                <p>Advanced software platforms for real-time monitoring and predictive maintenance.</p>
            </div>
        </div>
    </div>

    <div class="footer-section">
        <div class="testimonial">
            <p>"Outstanding service from initial consultation to final installation. exceeded expectations in every way. Highly recommend for solar solutions!"</p>
            <div class="author">
                <div class="author-img"></div>
                <div class="author-info">
                    <strong>Michael Robinson</strong>
                    <span>CEO at ECO Innovation</span>
                </div>
            </div>
        </div>
        <div class="testimonial">
            <p>"Efficiency increased by 40% in our operations thanks to the quality of the solar panel installation. Excellent company to work with."</p>
            <div class="author">
                <div class="author-img"></div>
                <div class="author-info">
                    <strong>Sarah Lee</strong>
                    <span>CTO at GreenWave</span>
                </div>
            </div>
        </div>
    </div>
@endsection
