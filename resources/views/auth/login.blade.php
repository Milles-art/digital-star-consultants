@extends('layouts.digitalstar')

@section('title', 'Sign in | Digital Star Consultants')

@section('content')
<section class="ds-section">
    <div class="ds-container ds-narrow">
        <div class="ds-panel ds-reveal">
            <span class="ds-kicker">Secure access</span>
            <h3 style="font-size:28px">Welcome back.</h3>
            <p>Sign in to manage service requests and operations.</p>
            <form id="login-form" class="ds-form" style="margin-top:24px">
                <label class="ds-field">Email
                    <input type="email" name="email" required autocomplete="username" placeholder="you@digitalstar.co.tz">
                </label>
                <label class="ds-field">Password
                    <input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </label>
                <label class="ds-check"><input type="checkbox" name="remember"><span>Remember me</span></label>
                <div id="login-error" class="ds-alert ds-alert-error" hidden></div>
                <button class="ds-btn ds-btn-primary ds-btn-block" style="height:48px">Sign in <i data-lucide="arrow-right"></i></button>
            </form>
            <p class="ds-help"><a href="{{ route('home') }}">← Back to website</a></p>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>document.getElementById('login-form').addEventListener('submit',async e=>{e.preventDefault();const m=document.getElementById('login-error');const r=await fetch('/login',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(e.currentTarget)))});const d=await r.json();if(r.ok)location.href=d.data.redirect;else{m.hidden=false;m.textContent=d.message||'Unable to sign in.'}})</script>
@endpush
