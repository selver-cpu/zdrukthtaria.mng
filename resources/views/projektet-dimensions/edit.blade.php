@extends('layouts.app')
@section('title','Edito Dimensionin')
@section('content')
<div class="container mt-3">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Edito Dimensionin</h5>
      <div>
        <a href="{{ route('projektet-dimensions.show',$dimension) }}" class="btn btn-sm btn-secondary">Shfaq</a>
        <a href="{{ route('projektet-dimensions.index') }}" class="btn btn-sm btn-outline-secondary">Kthehu</a>
      </div>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('projektet-dimensions.update',$dimension) }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Projekti *</label>
            <select name="projekt_id" class="form-control" required>
              @foreach($projektet as $p)
                <option value="{{ $p->projekt_id }}" {{ old('projekt_id',$dimension->projekt_id)==$p->projekt_id?'selected':'' }}>{{ $p->emri_projektit }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Emri i pjesës *</label>
            <input name="emri_pjeses" class="form-control" value="{{ old('emri_pjeses',$dimension->emri_pjeses) }}" required>
          </div>
          <div class="col-md-3"><label class="form-label">Gjatësia (mm)</label><input type="number" step="0.01" name="gjatesia" class="form-control" value="{{ old('gjatesia',$dimension->gjatesia) }}" required></div>
          <div class="col-md-3"><label class="form-label">Gjerësia (mm)</label><input type="number" step="0.01" name="gjeresia" class="form-control" value="{{ old('gjeresia',$dimension->gjeresia) }}" required></div>
          <div class="col-md-3"><label class="form-label">Trashësia (mm)</label><input type="number" step="0.01" name="trashesia" class="form-control" value="{{ old('trashesia',$dimension->trashesia) }}" required></div>
          <div class="col-md-3">
            <label class="form-label">Njësia Matëse *</label>
            <select name="njesi_matese" class="form-control" required>
              @foreach(['mm','cm','m'] as $u)
                <option value="{{ $u }}" {{ old('njesi_matese',$dimension->njesi_matese)===$u?'selected':'' }}>{{ $u }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label class="form-label">Sasia *</label><input type="number" min="1" step="1" name="sasia" class="form-control" value="{{ old('sasia',$dimension->sasia) }}" required></div>
          <div class="col-md-9">
            <label class="form-label">Materiali</label>
            <div class="input-group">
              <select name="materiali_id" class="form-control">
                <option value="">— Zgjidh Materialin —</option>
                @foreach($materialet as $m)
                  <option value="{{ $m->material_id }}" {{ old('materiali_id',$dimension->materiali_id)==$m->material_id?'selected':'' }}>{{ $m->emri_materialit }} ({{ $m->njesia_matese }})</option>
                @endforeach
              </select>
              <span class="input-group-text">ose</span>
              <input name="materiali_personal" class="form-control" placeholder="Material personal" value="{{ old('materiali_personal',$dimension->materiali_personal) }}">
            </div>
          </div>
          <div class="col-12"><hr class="my-2"></div>
          <div class="col-md-2 form-check ms-2">
            <input type="checkbox" class="form-check-input" id="kantim_needed" name="kantim_needed" value="1" {{ old('kantim_needed',$dimension->kantim_needed)?'checked':'' }}>
            <label class="form-check-label" for="kantim_needed">Kërkohet Kantim</label>
          </div>
          <div class="col-md-4">
            <label class="form-label">Lloji i Kantimit</label>
            <select name="kantim_type" class="form-control">
              <option value="">—</option>
              @foreach(['PVC','ABS','Wood Veneer','Aluminum'] as $t)
                <option value="{{ $t }}" {{ old('kantim_type',$dimension->kantim_type)===$t?'selected':'' }}>{{ $t }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label class="form-label">Trashësia e Kantimit (mm)</label><input type="number" step="0.01" name="kantim_thickness" class="form-control" value="{{ old('kantim_thickness',$dimension->kantim_thickness) }}"></div>
          <div class="col-md-3">
            <label class="form-label">Qoshet *</label>
            <select name="kantim_corners" class="form-control" required>
              @foreach(['square'=>'Katror','rounded'=>'Rrumbullakët'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('kantim_corners',$dimension->kantim_corners)===$val?'selected':'' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12">
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="kantim_front" id="kantim_front" value="1" {{ old('kantim_front',$dimension->kantim_front)?'checked':'' }}><label class="form-check-label" for="kantim_front">Përpara</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="kantim_left" id="kantim_left" value="1" {{ old('kantim_left',$dimension->kantim_left)?'checked':'' }}><label class="form-check-label" for="kantim_left">Majtas</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="kantim_right" id="kantim_right" value="1" {{ old('kantim_right',$dimension->kantim_right)?'checked':'' }}><label class="form-check-label" for="kantim_right">Djathtas</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="kantim_back" id="kantim_back" value="1" {{ old('kantim_back',$dimension->kantim_back)?'checked':'' }}><label class="form-check-label" for="kantim_back">Pas</label></div>
          </div>
          <div class="col-12"><hr class="my-2"></div>
          <div class="col-md-6">
            <label class="form-label">Statusi i Prodhimit *</label>
            <select name="statusi_prodhimit" class="form-control" required>
              @foreach(['pending'=>'Në pritje','cutting'=>'Duke prerë','edge_banding'=>'Duke kantuar','completed'=>'Përfunduar'] as $v=>$l)
                <option value="{{ $v }}" {{ old('statusi_prodhimit',$dimension->statusi_prodhimit)===$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">Workstation</label><input name="workstation_current" class="form-control" value="{{ old('workstation_current',$dimension->workstation_current) }}" placeholder="p.sh. EDGE-01"></div>
          <div class="col-12"><label class="form-label">Përshkrimi</label><textarea name="pershkrimi" rows="3" class="form-control">{{ old('pershkrimi',$dimension->pershkrimi) }}</textarea></div>
          <div class="col-12 d-flex justify-content-end mt-3">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Ruaj</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
