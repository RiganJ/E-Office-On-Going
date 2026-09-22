@extends('layouts.app') @section('content')
    <h1 class="mb-1 text-2xl font-bold">
        Buat Disposisi</h1>
    <p class="mb-6 text-slate-500">{{ $document->subject }}</p>
    <form method="post"
          action="{{ route('dispositions.store', $document) }}"
          class="max-w-3xl rounded-xl bg-white p-6 shadow-sm">@csrf<div class="grid gap-5 md:grid-cols-2">
            <label>Penerima<select name="to_user_id">
                    <option value="">Pilih pengguna</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </label><label>Unit tujuan<select name="to_unit_id">
                    <option value="">Pilih unit</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </label><label>Jabatan tujuan<select name="to_position_id">
                    <option value="">Pilih jabatan</option>
                    @foreach ($positions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </label><label>Jabatan pengirim<select name="from_position_id">
                    <option value="">Pilih jabatan</option>
                    @foreach ($positions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </label><label>Unit pengirim<select name="from_unit_id">
                    <option value="">Pilih unit</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </label>
        </div><label class="mt-5 block">Instruksi
            <textarea name="instruction"
                      required
                      rows="4"></textarea>
        </label><label class="mt-5 block">Catatan
            <textarea name="notes"
                      rows="3"></textarea>
        </label>
        <div class="mt-5 grid gap-5 md:grid-cols-2"><label>Prioritas<select name="priority">
                    @foreach (['NORMAL', 'LOW', 'HIGH', 'URGENT'] as $p)
                        <option>{{ $p }}</option>
                    @endforeach
                </select></label><label>Deadline<input name="deadline"
                       type="datetime-local"></label></div><button class="btn mt-6">Kirim
            Disposisi</button>
    </form>
@endsection
