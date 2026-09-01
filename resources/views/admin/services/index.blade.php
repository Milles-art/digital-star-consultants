@extends('layouts.admin')
@section('page_title','Services')
@section('content')
<div class="admin-page-intro">
    <div><span class="admin-kicker">SERVICE CATALOGUE</span><h2>Manage every service customers can request.</h2><p>Create, edit, publish and configure the services behind the public catalogue.</p></div>
    <button class="button button-yellow" type="button" data-modal-open="service-modal">+ Add service</button>
</div>

<div class="service-admin-stats">
    <div><span>Total services</span><strong>{{ $services->count() }}</strong><small>Across the catalogue</small></div>
    <div><span>Active</span><strong>{{ $services->where('is_active',true)->count() }}</strong><small>Visible to customers</small></div>
    <div><span>Inactive</span><strong>{{ $services->where('is_active',false)->count() }}</strong><small>Hidden from public</small></div>
    <div><span>Groups</span><strong>{{ $categories->whereNotNull('parent_id')->count() }}</strong><small>Service groups</small></div>
</div>

<section class="admin-panel">
    <form class="service-admin-filters" method="GET">
        <label><span>Search</span><input name="search" value="{{ request('search') }}" placeholder="Search service name..."></label>
        <label><span>Category</span><select name="category_id"><option value="">All categories</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected((string)request('category_id')===(string)$c->id)>{{ $c->full_path }}</option>@endforeach</select></label>
        <label><span>Status</span><select name="is_active"><option value="">All statuses</option><option value="1" @selected(request('is_active')==='1')>Active</option><option value="0" @selected(request('is_active')==='0')>Inactive</option></select></label>
        <div class="service-filter-actions"><button class="button button-dark">Apply filters</button><a class="button button-outline" href="{{ route('admin.services.index') }}">Reset</a></div>
    </form>
</section>

<section class="admin-panel">
    <div class="admin-panel-head"><div><span class="admin-kicker">LIVE CATALOGUE</span><h3>Services</h3></div><span class="admin-inline-note">{{ $services->count() }} result{{ $services->count() === 1 ? '' : 's' }}</span></div>
    <div class="service-admin-grid">
        @forelse($services as $service)
            <article class="service-admin-card" data-service-card data-id="{{ $service->id }}">
                <div class="service-admin-card-top"><span class="admin-service-icon">{{ $service->category->icon ?: '✦' }}</span><span class="badge {{ $service->is_active ? 'success' : 'secondary' }}" data-status>{{ $service->is_active ? 'Active' : 'Inactive' }}</span></div>
                <span class="service-admin-path">{{ $service->category->full_path ?? 'Uncategorized' }}</span>
                <h3>{{ $service->name }}</h3>
                <p>{{ $service->description ?: 'No service description added yet.' }}</p>
                <div class="service-admin-meta"><span>{{ $service->formatted_price }}</span><span>{{ $service->fields->count() }} fields</span><span>{{ $service->duration_minutes ? $service->duration_minutes . ' min' : 'No duration' }}</span></div>
                <div class="service-admin-actions">
                    <a class="button button-outline" href="{{ route('admin.fields.index',$service) }}">Form builder</a>
                    <button class="button button-soft" type="button" data-edit-service="{{ $service->id }}">Edit</button>
                    <button class="icon-action" type="button" title="Toggle active status" data-toggle-service="{{ $service->id }}">↻</button>
                </div>
            </article>
        @empty
            <div class="admin-empty"><div class="admin-empty-icon">✦</div><strong>No services match your filters</strong><span>Try clearing the filters or add a new service.</span></div>
        @endforelse
    </div>
</section>

<div class="admin-modal" id="service-modal" aria-hidden="true">
    <div class="admin-modal-backdrop" data-modal-close="service-modal"></div>
    <div class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="service-modal-title">
        <div class="admin-modal-head"><div><span class="admin-kicker" data-modal-kicker>NEW SERVICE</span><h3 id="service-modal-title">Add a service</h3></div><button class="modal-close" type="button" data-modal-close="service-modal">×</button></div>
        <form id="service-form" class="admin-form-grid">
            @csrf
            <input type="hidden" name="_method" id="service-method" value="POST">
            <input type="hidden" id="service-id">
            <label class="span-2"><span>Service name</span><input name="name" id="service-name" required placeholder="e.g. Passport Renewal"></label>
            <label class="span-2"><span>Category / group</span><select name="service_category_id" id="service-category" required>@foreach($categories->whereNotNull('parent_id') as $c)<option value="{{ $c->id }}">{{ $c->full_path }}</option>@endforeach</select></label>
            <label><span>Price (optional)</span><input type="number" name="price" id="service-price" min="0" step="0.01" placeholder="0"></label>
            <label><span>Duration (minutes)</span><input type="number" name="duration_minutes" id="service-duration" min="1" placeholder="60"></label>
            <label class="span-2"><span>Description</span><textarea name="description" id="service-description" rows="4" placeholder="Explain what this service helps the customer accomplish."></textarea></label>
            <label><span>Sort order</span><input type="number" name="sort_order" id="service-sort" min="0" value="0"></label>
            <label class="toggle-field"><span>Visibility</span><input type="hidden" name="is_active" value="0"><span class="check-row"><input type="checkbox" name="is_active" id="service-active" value="1" checked> Visible to customers</span></label>
            <div class="admin-modal-foot span-2"><span class="modal-status" id="service-status"></span><div><button class="button button-outline" type="button" data-modal-close="service-modal">Cancel</button><button class="button button-yellow" type="submit" id="service-submit">Create service</button></div></div>
        </form>
    </div>
</div>

<script>
(() => {
 const modal=document.getElementById('service-modal'); const form=document.getElementById('service-form'); const csrf=document.querySelector('meta[name="csrf-token"]').content;
 const fields={id:document.getElementById('service-id'),method:document.getElementById('service-method'),name:document.getElementById('service-name'),category:document.getElementById('service-category'),price:document.getElementById('service-price'),duration:document.getElementById('service-duration'),description:document.getElementById('service-description'),sort:document.getElementById('service-sort'),active:document.getElementById('service-active')};
 const title=document.getElementById('service-modal-title'), kicker=document.querySelector('[data-modal-kicker]'), submit=document.getElementById('service-submit'), status=document.getElementById('service-status');
 function open(){modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');document.body.classList.add('modal-open');}
 function close(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');document.body.classList.remove('modal-open');status.textContent='';}
 function reset(){form.reset();fields.id.value='';fields.method.value='POST';fields.sort.value='0';fields.active.checked=true;title.textContent='Add a service';kicker.textContent='NEW SERVICE';submit.textContent='Create service';}
 document.querySelectorAll('[data-modal-open]').forEach(b=>b.addEventListener('click',()=>{reset();open()})); document.querySelectorAll('[data-modal-close]').forEach(b=>b.addEventListener('click',close));
 document.querySelectorAll('[data-edit-service]').forEach(b=>b.addEventListener('click',async()=>{status.textContent='Loading…';open();kicker.textContent='EDIT SERVICE';title.textContent='Edit service';try{const r=await fetch('{{ url('/admin/services') }}/'+b.dataset.editService,{headers:{Accept:'application/json'}});const j=await r.json();const s=j.data;fields.id.value=s.id;fields.method.value='PUT';fields.name.value=s.name||'';fields.category.value=s.service_category_id||s.category_id||'';fields.price.value=s.price ?? '';fields.duration.value=s.duration_minutes ?? '';fields.description.value=s.description||'';fields.sort.value=s.sort_order||0;fields.active.checked=!!s.is_active;submit.textContent='Save changes';status.textContent='';}catch(e){status.textContent='Could not load service.';}}));
 form.addEventListener('submit',async e=>{e.preventDefault();status.textContent='Saving…';const id=fields.id.value;const url=id?'{{ url('/admin/services') }}/'+id:'{{ route('admin.services.store') }}';const fd=new FormData(form);fd.set('_method',id?'PUT':'POST');try{const r=await fetch(url,{method:'POST',headers:{Accept:'application/json','X-CSRF-TOKEN':csrf},body:fd});const j=await r.json();if(!r.ok)throw new Error(j.message||'Save failed');location.reload();}catch(err){status.textContent=err.message||'Could not save service.';}});
 document.querySelectorAll('[data-toggle-service]').forEach(b=>b.addEventListener('click',async()=>{if(!confirm('Change this service visibility?'))return;const card=b.closest('[data-service-card]');try{const r=await fetch('{{ url('/admin/services') }}/'+b.dataset.toggleService+'/toggle-active',{method:'POST',headers:{Accept:'application/json','X-CSRF-TOKEN':csrf}});if(!r.ok)throw new Error();location.reload();}catch(e){alert('Could not change service status.');}}));
})();
</script>
@endsection
