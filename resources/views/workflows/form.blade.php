@extends('layouts.app')

@section('content')
    @php
        $workflow = $workflow ?? null;
        $steps = old(
            'steps',
            $workflow
                ? $workflow->steps
                    ->map(
                        fn($step) => $step->only([
                            'approver_type',
                            'approver_reference',
                            'is_required',
                            'allow_reject',
                            'allow_revision',
                        ]),
                    )
                    ->all()
                : array_fill(0, 5, [
                    'approver_type' => 'USER',
                    'approver_reference' => '',
                    'is_required' => 1,
                    'allow_reject' => 1,
                    'allow_revision' => 1,
                ]),
        );
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            {{ $workflow ? 'Ubah Alur Persetujuan' : 'Buat Alur Persetujuan' }}</h1>
        <p class="text-slate-500">Tentukan pihak yang berwenang menyetujui dokumen pada setiap langkah.
        </p>
    </div>

    <form method="post"
          action="{{ $workflow ? route('workflows.update', $workflow) : route('workflows.store') }}"
          class="max-w-4xl rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($workflow)
            @method('PUT')
        @endif

        <div class="grid gap-5 md:grid-cols-2">
            <label>Nama<input name="name"
                       required
                       value="{{ old('name', $workflow?->name) }}"></label>
            <label>Kode<input name="code"
                       required
                       value="{{ old('code', $workflow?->code) }}"></label>
            <label>Jenis<select name="document_type_id">
                    @foreach ($types as $documentType)
                        <option value="{{ $documentType->id }}"
                                @selected(old('document_type_id', $workflow?->document_type_id) == $documentType->id)>{{ $documentType->name }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>Unit<select name="unit_id">
                    <option value="">Semua unit</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}"
                                @selected(old('unit_id', $workflow?->unit_id) == $unit->id)>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <label class="mt-5 block"><input type="hidden"
                   name="is_active"
                   value="0"><input type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', $workflow?->is_active ?? true))> Aktif</label>
        <h2 class="mt-6 font-semibold">Langkah Persetujuan</h2>

        @foreach ($steps as $index => $step)
            <fieldset class="mt-4 border p-4">
                <legend>Langkah {{ $index + 1 }}</legend>
                <div class="grid gap-4 md:grid-cols-2">
                    <label>Tipe penyetuju<select name="steps[{{ $index }}][approver_type]">
                            @foreach (['USER' => 'Pengguna', 'POSITION' => 'Jabatan', 'UNIT_POSITION' => 'Unit dan jabatan', 'ROLE' => 'Peran', 'PERMISSION' => 'Izin'] as $approverType => $approverLabel)
                                <option value="{{ $approverType }}"
                                        @selected(($step['approver_type'] ?? 'USER') === $approverType)>{{ $approverLabel }}
                                </option>
                            @endforeach
                        </select></label>
                    <label>ID referensi<input name="steps[{{ $index }}][approver_reference]"
                               value="{{ $step['approver_reference'] ?? '' }}"></label>
                </div>
                <label><input type="hidden"
                           name="steps[{{ $index }}][is_required]"
                           value="0"><input type="checkbox"
                           name="steps[{{ $index }}][is_required]"
                           value="1"
                           @checked($step['is_required'] ?? false)> Wajib</label>
                <label><input type="hidden"
                           name="steps[{{ $index }}][allow_reject]"
                           value="0"><input type="checkbox"
                           name="steps[{{ $index }}][allow_reject]"
                           value="1"
                           @checked($step['allow_reject'] ?? false)> Tolak</label>
                <label><input type="hidden"
                           name="steps[{{ $index }}][allow_revision]"
                           value="0"><input type="checkbox"
                           name="steps[{{ $index }}][allow_revision]"
                           value="1"
                           @checked($step['allow_revision'] ?? false)> Revisi</label>
            </fieldset>
        @endforeach

        <p class="mt-4 text-sm text-slate-500">Masukkan ID sesuai tipe penyetuju. Data tersedia pada
            data pengguna, jabatan,
            peran, dan izin.</p>
        <button class="btn mt-6"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
    </form>
@endsection
