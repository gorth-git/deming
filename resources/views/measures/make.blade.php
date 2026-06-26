@extends("layout")

@section('title', $measure->name)

@section("styles")
<style>
.CodeMirror.CodeMirror-disabled {
    background-color: #f5f5f5 !important;
    cursor: not-allowed;
}
</style>
@endsection

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.measure.make') }}" data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

@include('partials.errors')

<form method="POST" action="/bob/make" enctype="multipart/form-data">
	@csrf
	<input type="hidden" name="id" value="{{ $measure->id }}"/>

	<div class="grid">
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.clauses') }}</strong>
	    	</div>
    		<div class="cell-lg-7 cell-md-9">
                @foreach($measure->controls as $control)
                    <a href="/alice/show/{{ $control->id }}">{{ $control->clause }}</a> - {{ $control->name }}
                    @if(!$loop->last)
                    <br>
                    @endif
                @endforeach
            </div>
        </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.name') }}</strong>
	    	</div>
            @if ($measure->scope==null)
    		<div class="cell-lg-6 cell-md-10">
				{{ $measure->name }}
			</div>
			@else
    		<div class="cell-lg-5 cell-md-5">
				{{ $measure->name }}
			</div>
            <div class="cell-2">
	    		<strong>{{ trans("cruds.measure.fields.scope") }}</strong>
	    		&nbsp;
    			{{ $measure->scope }}
    		</div>
            @endif
		</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.objective') }}</strong>
	    	</div>
            <div class="cell-lg-6 cell-md-10">
                {!! \Parsedown::instance()->text($measure->objective) !!}
			</div>
		</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.input') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-10">
                {!! \Parsedown::instance()->text($measure->input) !!}
			</div>
		</div>

        <div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.model') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-10">
				<pre>{{ $measure->model }}</pre>
			</div>
		</div>

        <div class="row">

			<div class="cell-lg-1 cell-md-2">
				<b>{{ trans('cruds.measure.fields.plan_date') }}</b>
			</div>
            <div class="cell-lg-2 cell-md-4">
                @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
					<input type="text"
						data-role="calendarpicker"
						name="plan_date"
    					data-format="YYYY-MM-DD"
    					data-inputFormat="YYYY-MM-DD"
						value="{{ old('plan_date', $measure->plan_date) }}"
						/>
                @else
                    {{ $measure->plan_date }}
                @endif
			</div>
            <div class="cell-lg-2 cell-md-3" align="right">
	    		<strong>{{ trans('cruds.measure.fields.realisation_date') }}</strong>
	    	</div>
            <div class="cell-lg-2 cell-md-3">
				<input type="text"
					data-role="calendarpicker"
					name="realisation_date"
					value="{{
						\Carbon\Carbon::now()
						->format('Y-m-d')
						}}"
					data-format="YYYY-MM-DD"
                    data-inputFormat="YYYY-MM-DD"/>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.observations') }}</strong>
	    	</div>
			<div class="cell-lg-6 cell-md-10">
                <textarea name="observations" rows="5" data-role="textarea" data-clear-button="false">{{ old('observations', $measure->observations) }}</textarea>
			</div>
	    </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.evidence') }}</strong>
	    		<br>
                <a target="_new" href="/bob/template/{{ $measure->id }}" id="checklist-link">{{ trans('cruds.measure.checklist') }}</a>
	    	</div>
			<div class="cell-lg-6 cell-md-10">
				<div class="dropzone dropzone-previews" id="dropzoneFileUpload"></div>
			</div>
	    </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.note') }}</strong>
	    	</div>
            <div class="cell-lg-2 cell-md-3">
                <input
                    id="note"
                    name="note"
                    value="{{ old('note', $measure->note) }}"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0.00"
                    data-role="spinner"
                />
    		</div>
	    </div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.indicator') }}</strong>
	    	</div>
            <div class="cell-lg-6 cell-md-10">
				<pre>{{ $measure->indicator }}</pre>
			</div>
		</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>
	    			{{ trans('cruds.measure.fields.score') }}
	    		</strong>
	    	</div>
            <div class="cell-lg-6 cell-md-10">
                <input type="radio" data-role="radio" name="score" value="3" data-role="radio" data-append="<font color='green'>{{ trans('common.green') }}</font>" {{ $measure->score===3 ? "checked" : "" }} />
                <input type="radio" data-role="radio" name="score" value="2" data-role="radio" data-append="<font color='orange'>{{ trans('common.orange') }}</font>" {{ $measure->score===2 ? "checked" : "" }} />
                <input type="radio" data-role="radio" name="score" value="1" data-role="radio" data-append="<font color='red'>{{ trans('common.red') }}</font>" {{ $measure->score===1 ? "checked" : "" }} />
            </div>
		</div>
        @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
    	<div class="row">
            <div class="cell-lg-1 cell-md-2">
            </div>
            <div class="cell-3">
                <input type="checkbox" name="add_action_plan" data-role="checkbox" id="toggleTextarea" data-append="{{ trans('cruds.measure.create_action') }}"/>
            </div>
        </div>
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.action_plan') }}</strong>
	    	</div>
            <div class="cell-lg-6 cell-md-10">
                <textarea name="action_plan" class="form-control easymde disabled-editor" id="action_plan">{{ old('action_plan', $measure->action_plan) }}</textarea>
			</div>
		</div>
        @endif
		@if ($measure->periodicity!==0)
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
	    		<strong>{{ trans('cruds.measure.fields.next') }}</strong>
	    	</div>
            <div class="cell-ld-2 cell-md-3">
                @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
					<input type="text"
                        lenght="12"
						data-role="calendarpicker"
						name="next_date"
						value="{{ $next_date }}"
						data-format="YYYY-MM-DD"
    					data-inputFormat="YYYY-MM-DD"/>
                </div>
                <div class="cell-2">
                @else
                    {{ $next_date }}
                @endif
			(
			@if ($measure->periodicity==-1)
				{{ trans("common.weekly") }}
			@elseif ($measure->periodicity==1)
				{{ trans("common.monthly") }}
			@elseif ($measure->periodicity==3)
				{{ trans("common.quarterly") }}
			@elseif ($measure->periodicity==6)
				{{ trans("common.biannually") }}
			@elseif ($measure->periodicity==12)
				{{ trans("common.annually") }}
			@else
				{{ $measure->periodicity }}
			@endif
			)
			</div>
		</div>
        @endif

    	<div class="row">
    		&nbsp;
    	</div>

    	<div class="row">
              <div class="cell-12">
            @if ($measure->canMake())
                @if ($measure->status===0)
					<button type="submit" class="button success">
						<span class="mif-done"></span>
						&nbsp;
						{{ trans('common.make') }}
					</button>
					&nbsp;
                    @endif
                @endif
                @if ((Auth::User()->role === 1)||(Auth::User()->role === 2))
                    @if ($measure->status===1)
					<button type="submit" class="button success" onclick='this.form.action="/bob/accept"'>
						<span class="mif-done"></span>
						&nbsp;
						{{ trans('common.accept') }}
					</button>
					&nbsp;
					<button type="submit" class="button alert" onclick='this.form.action="/bob/reject"'>
						<span class="mif-fire"></span>
						&nbsp;
						{{ trans('common.reject') }}
					</button>
					&nbsp;
                    @endif
                @endif
                @if ($measure->canMake())
					<button type="submit" class="button primary" onclick='this.form.action="/bob/draft"'>
                        <span class="mif-floppy-disk2"></span>
			            &nbsp;
						{{ trans('common.save') }}
					</button>
					&nbsp;
                @endif
                <a href="/bob/show/{{ $measure->id }}" class="button">
					<span class="mif-cancel"></span>
					&nbsp;
	    			{{ trans('common.cancel') }}
                </a>
    		</div>
    	</div>

	</form>
</div>
</div>
<br>
<br>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---------------------------------------------------------
    // DropZone
    // ---------------------------------------------------------

    const myDropzone = new Dropzone("div#dropzoneFileUpload", {
        url: '/doc/store',
	    headers: { 'x-csrf-token': '{{csrf_token()}}' },
        params: function params(files, xhr, chunk) { return { 'control': '{{ $measure->id }}' }; },
        addRemoveLinks: true,
        timeout: 50000,
        removedfile: function(file)
        {
            console.log("remove file " + file.name + " " + file.id);
            $.ajax({
                headers: {
                  'X-CSRF-TOKEN': '{{csrf_token()}}'
                   },
                type: 'GET',
                url: '/doc/delete/'+file.id,
                success: function (data){
                    console.log("File has been successfully removed");
                },
                error: function(e) {
                    console.log("File not removed");
                    console.log(e);
                }
            });
            // console.log('{{ url( "/doc/delete" ) }}'+"/"+file.id+']');
            var fileRef;
            return (fileRef = file.previewElement) != null ?
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
        },
        success: function(file, response)
        {
            file.id=response.id;
            console.log("success response");
            console.log(response);
        },
        error: function(file, response)
        {
            console.log("error response");
            console.log(response);
            return false;
        },
        init: function () {
            // Add existing files into dropzone
            var existingFiles = [
            @foreach ($documents as $document)
                { name: "{{ $document->filename }}", size: {{ $document->size }}, id: {{ $document->id }} },
            @endforeach
            ];
            for (i = 0; i < existingFiles.length; i++) {
                this.emit("addedfile", existingFiles[i]);
                this.emit("complete", existingFiles[i]);
            	}
        	}
    	});

        // Paste document or image on page
        document.addEventListener('paste', function(event) {
          const clipboardData = event.clipboardData || window.clipboardData;
          if (!clipboardData || !clipboardData.items) return;
          const items = Array.from(clipboardData.items);
          items.forEach((item) => {
            if (item.kind === 'file') {
              const file = item.getAsFile();
              if (file) {
                if (typeof myDropzone !== 'undefined' && typeof myDropzone.addFile === 'function') {
                  myDropzone.addFile(file);
                } else {
                  console.warn('myDropzone n\'est pas défini ou n\'est pas un objet Dropzone valide.');
                }
              }
            }
          });
        });

@if ((Auth::User()->role === 1)||(Auth::User()->role === 2))

    // ---------------------------------------------------------
    // Désativte EasyMDE au chargement
    // ---------------------------------------------------------

    function waitForEasyMDE(id, callback) {
        const interval = setInterval(() => {
            if (window.editors && window.editors[id]) {
                clearInterval(interval);
                callback(window.editors[id]);
            }
        }, 100); // Vérifie toutes les 100ms
    }

    // Rendre l'éditeur en lecture seule par défaut
    waitForEasyMDE('action_plan', function(easyMDE) {
        // Lire/écrire sur l'éditeur une fois prêt
        easyMDE.codemirror.setOption("readOnly", true);
        easyMDE.codemirror.getWrapperElement().classList.add('CodeMirror-disabled');

        document.getElementById('toggleTextarea').addEventListener('change', function() {
            if (this.checked) {
                easyMDE.codemirror.setOption("readOnly", false);
                easyMDE.codemirror.getWrapperElement().classList.remove('CodeMirror-disabled');
            } else {
                easyMDE.codemirror.setOption("readOnly", true);
                easyMDE.codemirror.getWrapperElement().classList.add('CodeMirror-disabled');
            }
        });
    });

@endif

    // ---------------------------------------------------------
    // Ajoute les observations en paramètre de la template de document
    // ---------------------------------------------------------


    const link = document.querySelector('#checklist-link');
    const textarea = document.querySelector('textarea[name="observations"]');

    link.addEventListener('click', async function (event) {
        event.preventDefault();

        const action = this.getAttribute('href');
        const observations = textarea.value;

        // Préparation des données à envoyer
        const formData = new FormData();
        formData.append('observations', observations);
        formData.append('_token', '{{csrf_token()}}'); // à générer côté serveur avant rendu

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la génération du document');
            }

            const blob = await response.blob();

            // Récupération du nom du fichier depuis les en-têtes si disponible
            let filename = 'document.doc';
            const disposition = response.headers.get('Content-Disposition');
            if (disposition && disposition.includes('filename=')) {
                const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match && match[1]) {
                    filename = match[1].replace(/['"]/g, '');
                }
            }

            // Création d'un lien de téléchargement temporaire
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

        } catch (error) {
            console.error('Erreur pendant le téléchargement :', error);
            alert('Le téléchargement a échoué.');
        }
    });


    // ---------------------------------------------------------
    // Check-uncheck radio buttons
    // ---------------------------------------------------------
    let lastChecked = null;
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
      radio.addEventListener('click', function() {
        if (this === lastChecked) {
          this.checked = false;
          lastChecked = null;
        } else {
          lastChecked = this;
        }
      });
    });

    //--------------------------------------------------
    // Validation
    //---------------------------------------------------
    /*
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
    */
});

</script>

@endsection
