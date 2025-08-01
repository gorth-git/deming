@extends("layout")

@section("content")
<?php
function bytesToHuman($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
<div data-role="panel" data-title-caption="{{ trans('cruds.document.title.templates') }}" data-collapsible="false" data-title-icon="<span class='mif-file-text'></span>">

    @include('partials.errors')

    <form action="/doc/template" method="POST" role="form" enctype="multipart/form-data">
    @csrf
        <div class="grid">
            <div class="row">
                <div class="cell-5">
                    <a href="/doc/template?id=1" target="_new">{{ trans('cruds.document.model.control') }}</a>
                    @if (file_exists(storage_path('app/models/control_.docx')))
                        / <a href="/doc/template?id=2" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <input type="file" data-role="file" name="template1"/>
                </div>
            </div>
            <div class="row">
                <div class="cell-5">
                    <a href="/doc/template?id=3" target="_new">{{ trans('cruds.document.model.report') }}</a>
                    @if (file_exists(storage_path('app/models/pilotage_.docx')))
                        / <a href="/doc/template?id=4" target="_new">{{ trans('cruds.document.model.custom') }}</a>
                    @endif
                    <input type="file" data-role="file" name="template2"/>
                </div>
            </div>
            <div class="row">
                <div class="cell-4">

                <button type="submit" class="button success"><span class="mif-ok"></span>
                    <span class="mif-floppy-disk2"></span>
                    &nbsp;
                    {{ trans("common.save") }}
                </button>
                &nbsp;
                <a class="button cancel" href="/">
                    <span class="mif-cancel"></span>
                    &nbsp;
                    {{ trans("common.cancel") }}
                </a>
                </div>
            </div>
        </div>
    </form>
</div>
<br>
<div data-role="panel" data-title-caption="{{ trans('cruds.document.title.storage') }}" data-collapsible="false" data-title-icon="<span class='mif-file-text'></span>">
    <div class="grid">
        <div class="row">
            <div class="cell-3">
            {{ trans('cruds.document.count') }} : {{ $count }}
            </div>
        </div>
        <div class="row">
            <div class="cell-3">
            {{ trans('cruds.document.total_size') }} : {{ bytesToHuman($sum) }}
            </div>
        </div>
        <div class="row">
            <div class="cell-3">
                <a href="/doc/check" class="button success">
                    <span class="mif-done-all"></span>
                    &nbsp;
                    {{ trans('common.check') }}
                </a>
            </div>
        </div>
    </div>
</div>
<br>
<div data-role="panel" data-title-caption="{{ trans('cruds.document.title.cleanup') }}" data-collapsible="false" data-title-icon="<span class='mif-file-text'></span>">
    <form id="cleanupForm" action="/doc/config" method="POST">
	@csrf
    <div class="grid">
        <div class="row">
            <div class="cell-4">
                {{ trans('cruds.document.title.cleanup_detail') }}
            </div>
        </div>
        <div class="row">
            <div class="cell-2">
                <select id="durationSelect" name="duration" data-role="select" data-prepend="{{ trans('cruds.document.month') }}">
                    <option value="0" {{ ($duration=="0" || $duration==null)  ? 'selected' : '' }}>{{ trans('cruds.document.never') }}</option>
                    <option {{ $duration=="12" ? 'selected' : '' }}>12</option>
                    <option {{ $duration=="24" ? 'selected' : '' }}>24</option>
                    <option {{ $duration=="36" ? 'selected' : '' }}>36</option>
                    <option {{ $duration=="48" ? 'selected' : '' }}>48</option>
                    <option {{ $duration=="60" ? 'selected' : '' }}>60</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="cell-3">
                <button type="submit" class="button success" name="action" value="save">
                    <span class="mif-floppy-disk2"></span>
                    &nbsp;
                    {{ trans("common.save") }}
                </button>
                &nbsp;
                <button type="submit" class="button info" name="action" value="test">
                    <span class="mif-lab"></span>
                    &nbsp;
                    {{ trans("common.test") }}
                </button>
                &nbsp;
                <button type="submit" class="button alert" name="action" value="delete" id="deleteButton">
                    <span class="mif-bin"></span>
                    &nbsp;
                    {{ trans("common.delete") }}
                </button>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("deleteButton").addEventListener("click", function (event) {
        const select = document.getElementById("durationSelect");
        const selected = select.options[select.selectedIndex].value;
        if ((selected==0)||(!confirm("{{ trans('cruds.control.confirm_delete') }}")))
            event.preventDefault();
    });
});
</script>

@endsection
