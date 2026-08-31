@extends('layouts.app')
@section('title','Work — Digital Star Consultants')
@section('content')
<section class="ds-page-hero"><div class="ds-container"><span class="ds-index">WORK / SELECTED SYSTEMS</span><h1>Software that<br><em>does the job.</em></h1><p>A selection of platforms, internal tools and digital experiences designed around real operational needs.</p></div></section>
<section class="ds-section"><div class="ds-container"><div class="ds-work-catalog">
@foreach(($itProjects ?? []) as $project)<article><div class="ds-project-visual"><span>DS / {{ sprintf('%02d',$loop->iteration) }}</span><b>{{ $project->name }}</b></div><small>SOFTWARE SYSTEM</small><h2>{{ $project->name }}</h2><p>{{ $project->description }}</p></article>@endforeach
@foreach(($graphicsProjects ?? []) as $project)<article><div class="ds-project-visual graphic"><span>CREATIVE / {{ sprintf('%02d',$loop->iteration) }}</span><b>{{ $project->name }}</b></div><small>DESIGN & DIGITAL</small><h2>{{ $project->name }}</h2><p>{{ $project->description }}</p></article>@endforeach
</div></div></section>
@endsection