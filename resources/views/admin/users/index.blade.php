@extends('layouts.admin')
@section('page_title','Team & access')
@section('content')
<div class="admin-page-intro">
    <div>
        <div class="admin-kicker">PEOPLE & ACCESS</div>
        <h2>Run the team with clarity.</h2>
        <p>Manage management accounts and staff access, keep roles explicit, and see who is active before assigning work.</p>
    </div>
    <button class="admin-button primary" type="button" data-user-modal-open="create">+ Add team member</button>
</div>

<div class="admin-kpi-grid admin-kpi-grid-5">
    <div class="admin-kpi"><span class="admin-kpi-label">Total users</span><strong>{{ $stats['total'] }}</strong><small>All accounts</small></div>
    <div class="admin-kpi"><span class="admin-kpi-label">Active</span><strong>{{ $stats['active'] }}</strong><small>Can sign in</small></div>
    <div class="admin-kpi"><span class="admin-kpi-label">Inactive</span><strong>{{ $stats['inactive'] }}</strong><small>Access paused</small></div>
    <div class="admin-kpi"><span class="admin-kpi-label">Management</span><strong>{{ $stats['management'] }}</strong><small>Admin, CEO & GM</small></div>
    <div class="admin-kpi"><span class="admin-kpi-label">Staff</span><strong>{{ $stats['staff'] }}</strong><small>Operations team</small></div>
</div>

<section class="admin-panel">
    <div class="admin-panel-head admin-panel-head-stack-mobile">
        <div><div class="admin-kicker">ACCESS DIRECTORY</div><h3>People & permissions</h3></div>
        <div class="admin-inline-note">{{ $stats['active'] }} active accounts of {{ $stats['total'] }}</div>
    </div>
    <div class="user-toolbar">
        <label class="user-search"><span>Search</span><input type="search" id="user-search" placeholder="Name or email…" autocomplete="off"></label>
        <label><span>Role</span><select id="user-role"><option value="">All roles</option>@foreach($roles as $value=>$label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
        <label><span>Status</span><select id="user-status"><option value="">All statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
        <button class="admin-button secondary" type="button" id="user-clear">Reset filters</button>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table user-table" id="users-table">
            <thead><tr><th>Team member</th><th>Role</th><th>Status</th><th>Last login</th><th>Joined</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $u)
                @php $initials = $u->initials(); @endphp
                <tr data-user-row data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}" data-role="{{ $u->role }}" data-status="{{ $u->is_active ? 'active' : 'inactive' }}">
                    <td><div class="user-person"><span class="user-avatar">{{ $initials }}</span><div><strong>{{ $u->name }}</strong><small>{{ $u->email }}</small></div></div></td>
                    <td><span class="role-chip role-{{ $u->role }}">{{ $u->role_label }}</span><small class="table-muted">{{ $roleDescriptions[$u->role] ?? '' }}</small></td>
                    <td><span class="status-pill {{ $u->is_active ? 'is-active' : 'is-inactive' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td><strong>{{ $u->last_login_at?->format('d M Y') ?? 'Never' }}</strong><small class="table-muted">{{ $u->last_login_at?->format('H:i') ?? 'No sign-in yet' }}</small></td>
                    <td><small class="table-muted">{{ $u->created_at?->format('d M Y') ?? '—' }}</small></td>
                    <td><div class="user-actions">
                        <button type="button" class="icon-button" title="Edit" data-user-edit data-id="{{ $u->id }}" data-name="{{ e($u->name) }}" data-email="{{ e($u->email) }}" data-role="{{ $u->role }}" data-active="{{ $u->is_active ? '1' : '0' }}">Edit</button>
                        <button type="button" class="icon-button" title="Reset password" data-user-reset data-id="{{ $u->id }}" data-name="{{ e($u->name) }}">Reset</button>
                        @if(auth()->id() !== $u->id)
                            <button type="button" class="icon-button danger-text" data-user-toggle data-id="{{ $u->id }}" data-name="{{ e($u->name) }}" data-active="{{ $u->is_active ? '1' : '0' }}">{{ $u->is_active ? 'Pause' : 'Activate' }}</button>
                            <button type="button" class="icon-button danger-text" data-user-delete data-id="{{ $u->id }}" data-name="{{ e($u->name) }}">Delete</button>
                        @else
                            <span class="you-badge">You</span>
                        @endif
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="admin-empty"><div class="admin-empty-icon">◎</div><strong>No team members yet</strong><span>Create the first management or staff account to get started.</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="user-empty-filter" id="user-empty-filter" hidden>No users match the current filters.</div>
</section>

<section class="admin-panel role-guide">
    <div class="admin-panel-head"><div><div class="admin-kicker">ROLE MODEL</div><h3>Keep access intentional</h3></div></div>
    <div class="role-guide-grid">
        @foreach($roles as $value=>$label)
            <article><span class="role-chip role-{{ $value }}">{{ $label }}</span><p>{{ $roleDescriptions[$value] }}</p></article>
        @endforeach
    </div>
</section>

<div class="user-modal" id="user-modal" hidden aria-hidden="true">
    <div class="user-modal-backdrop" data-user-modal-close></div>
    <section class="user-modal-card" role="dialog" aria-modal="true" aria-labelledby="user-modal-title">
        <div class="user-modal-head"><div><div class="admin-kicker">TEAM ACCESS</div><h3 id="user-modal-title">Add team member</h3></div><button type="button" class="modal-close" data-user-modal-close aria-label="Close">×</button></div>
        <form id="user-form" class="user-form">
            <input type="hidden" id="user-id">
            <label><span>Full name</span><input id="user-name" required maxlength="255" autocomplete="name" placeholder="e.g. Asha Mushi"></label>
            <label><span>Email address</span><input id="user-email" type="email" required maxlength="255" autocomplete="email" placeholder="name@digitalstar.co.tz"></label>
            <label><span>Role</span><select id="user-role-form" required>@foreach($roles as $value=>$label)<option value="{{ $value }}">{{ $label }} — {{ $roleDescriptions[$value] }}</option>@endforeach</select></label>
            <label class="user-form-status" id="user-active-wrap"><span>Account status</span><select id="user-active"><option value="1">Active</option><option value="0">Inactive</option></select></label>
            <div class="user-form-note">New accounts receive a generated temporary password. It will be shown once after creation.</div>
            <div class="user-form-error" id="user-form-error" hidden></div>
            <div class="user-modal-actions"><button type="button" class="admin-button secondary" data-user-modal-close>Cancel</button><button type="submit" class="admin-button primary" id="user-submit">Create account</button></div>
        </form>
    </section>
</div>

<div class="credential-modal" id="credential-modal" hidden aria-hidden="true">
    <div class="user-modal-backdrop" data-credential-close></div>
    <section class="credential-card" role="dialog" aria-modal="true" aria-labelledby="credential-title">
        <div class="credential-icon">✓</div><div class="admin-kicker">ONE-TIME CREDENTIAL</div><h3 id="credential-title">Temporary password ready</h3><p id="credential-message">Share this securely with the team member.</p>
        <div class="credential-box"><span>Email</span><strong id="credential-email">—</strong><span>Temporary password</span><strong id="credential-password">—</strong></div>
        <button type="button" class="admin-button primary full" id="credential-copy">Copy credentials</button>
        <button type="button" class="admin-button ghost full" data-credential-close>Close</button>
    </section>
</div>
@endsection

@push('scripts')
<script>
(()=>{
 const routes={store:@json(route('admin.users.store')),update:id=>@json(url('/admin/users')).replace(/\/$/,'')+'/'+id,toggle:id=>@json(url('/admin/users')).replace(/\/$/,'')+'/'+id+'/toggle-active',reset:id=>@json(url('/admin/users')).replace(/\/$/,'')+'/'+id+'/reset-password',destroy:id=>@json(url('/admin/users')).replace(/\/$/,'')+'/'+id};
 const csrf=document.querySelector('meta[name="csrf-token"]')?.content;
 const modal=document.getElementById('user-modal'), form=document.getElementById('user-form'), title=document.getElementById('user-modal-title'), submit=document.getElementById('user-submit'), error=document.getElementById('user-form-error');
 const credential=document.getElementById('credential-modal');
 let editing=false;
 const q=(s,r=document)=>r.querySelector(s), qa=(s,r=document)=>[...r.querySelectorAll(s)];
 function openUser(mode,data={}){ editing=mode==='edit'; title.textContent=editing?'Edit team member':'Add team member'; submit.textContent=editing?'Save changes':'Create account'; q('#user-id').value=data.id||''; q('#user-name').value=data.name||''; q('#user-email').value=data.email||''; q('#user-role-form').value=data.role||'staff'; q('#user-active').value=data.active==='0'?'0':'1'; q('#user-active-wrap').style.display=editing?'grid':'none'; q('.user-form-note').style.display=editing?'none':'block'; error.hidden=true; error.textContent=''; modal.hidden=false; modal.setAttribute('aria-hidden','false'); document.body.classList.add('modal-open'); setTimeout(()=>q('#user-name').focus(),40); }
 function closeUser(){ modal.hidden=true; modal.setAttribute('aria-hidden','true'); document.body.classList.remove('modal-open'); }
 function showError(msg){error.hidden=false;error.textContent=msg||'Something went wrong.';}
 async function request(url,method,body={}){ const res=await fetch(url,{method,headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},body:method==='GET'?undefined:JSON.stringify(body)}); let json={}; try{json=await res.json()}catch{} if(!res.ok) throw new Error(json.message||Object.values(json.errors||{})?.flat?.()[0]||'Request failed.'); return json; }
 function refresh(){window.location.reload();}
 function showCredentials(c,titleText){q('#credential-title').textContent=titleText;q('#credential-email').textContent=c.email;q('#credential-password').textContent=c.temporary_password;credential.hidden=false;credential.setAttribute('aria-hidden','false');document.body.classList.add('modal-open');}
 qa('[data-user-modal-open]').forEach(b=>b.addEventListener('click',()=>openUser(b.dataset.userModalOpen)));
 qa('[data-user-modal-close]').forEach(b=>b.addEventListener('click',closeUser));
 qa('[data-credential-close]').forEach(b=>b.addEventListener('click',()=>{credential.hidden=true;credential.setAttribute('aria-hidden','true');document.body.classList.remove('modal-open')}));
 qa('[data-user-edit]').forEach(b=>b.addEventListener('click',()=>openUser('edit',b.dataset)));
 form.addEventListener('submit',async e=>{e.preventDefault();submit.disabled=true;showError('');try{const id=q('#user-id').value;const payload={name:q('#user-name').value.trim(),email:q('#user-email').value.trim(),role:q('#user-role-form').value};if(editing)payload.is_active=q('#user-active').value==='1';const res=await request(editing?routes.update(id):routes.store,'POST',editing?payload:{...payload});closeUser(); if(res.credentials)showCredentials(res.credentials,'Temporary password ready');else refresh();}catch(err){showError(err.message)}finally{submit.disabled=false}});
 qa('[data-user-reset]').forEach(b=>b.addEventListener('click',async()=>{if(!confirm(`Reset the password for ${b.dataset.name}?`))return;try{const res=await request(routes.reset(b.dataset.id),'POST');showCredentials(res.credentials,'Password reset complete')}catch(err){alert(err.message)}}));
 qa('[data-user-toggle]').forEach(b=>b.addEventListener('click',async()=>{const next=b.dataset.active==='1'?'deactivate':'activate';if(!confirm(`Are you sure you want to ${next} ${b.dataset.name}?`))return;try{await request(routes.toggle(b.dataset.id),'POST');refresh()}catch(err){alert(err.message)}}));
 qa('[data-user-delete]').forEach(b=>b.addEventListener('click',async()=>{if(!confirm(`Delete ${b.dataset.name}? This cannot be undone.`))return;try{await request(routes.destroy(b.dataset.id),'DELETE');refresh()}catch(err){alert(err.message)}}));
 const search=q('#user-search'),role=q('#user-role'),status=q('#user-status'),empty=q('#user-empty-filter');
 function filter(){const text=(search.value||'').toLowerCase().trim(),rv=role.value,sv=status.value;let shown=0;qa('[data-user-row]').forEach(r=>{const ok=(!text||`${r.dataset.name} ${r.dataset.email}`.includes(text))&&(!rv||r.dataset.role===rv)&&(!sv||r.dataset.status===sv);r.hidden=!ok;if(ok)shown++});empty.hidden=shown>0;}
 [search,role,status].forEach(el=>el.addEventListener('input',filter));q('#user-clear').addEventListener('click',()=>{search.value='';role.value='';status.value='';filter()});
 q('#credential-copy').addEventListener('click',async()=>{const text=`Email: ${q('#credential-email').textContent}\nTemporary password: ${q('#credential-password').textContent}`;try{await navigator.clipboard.writeText(text);q('#credential-copy').textContent='Copied';setTimeout(()=>q('#credential-copy').textContent='Copy credentials',1200)}catch{alert(text)}});
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!modal.hidden)closeUser();if(!credential.hidden){credential.hidden=true;credential.setAttribute('aria-hidden','true');document.body.classList.remove('modal-open')}}});
})();
</script>
@endpush
