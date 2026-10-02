@extends('layouts.digitalstar')

@section('title', 'Reset password | Digital Star Consultants')

@section('content')
<section class="ds-section">
    <div class="ds-container ds-narrow">
        <div class="ds-panel ds-reveal">
            <span class="ds-kicker">Account security</span>
            <h3 style="font-size:28px">Reset password.</h3>
            <p>Choose a new secure password for your account.</p>
            <form id="reset-form" class="ds-form" style="margin-top:24px">
                <input type="hidden" name="token" value="{{ $token }}">
                <label class="ds-field">Email
                    <input type="email" name="email" required autocomplete="email" placeholder="you@example.com">
                </label>
                <label class="ds-field">New password
                    <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters">
                </label>
                <label class="ds-field">Confirm password
                    <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Repeat password">
                </label>
                <button class="ds-btn ds-btn-primary ds-btn-block" style="height:48px">Reset password <i data-lucide="arrow-right"></i></button>
                <div id="reset-msg" class="ds-alert" hidden></div>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>document.getElementById('reset-form').addEventListener('submit',async e=>{e.preventDefault();const r=await fetch('/reset-password',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify(Object.fromEntries(new FormData(e.currentTarget)))});const d=await r.json(),m=document.getElementById('reset-msg');m.hidden=false;m.className='ds-alert '+(r.ok?'ds-alert-success':'ds-alert-error');m.textContent=d.message||'Done.'})</script>
@endpush
