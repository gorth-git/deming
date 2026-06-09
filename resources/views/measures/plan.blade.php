@extends("layout")

@section('title', $measure->name)

@section("content")
<div data-role="panel" data-title-caption="{{ trans('cruds.control.plan') }}" data-collapsible="false" data-title-icon="<span class='mif-paste'></span>">

    @include('partials.errors')

    <form method="POST" action="/bob/plan">
    @csrf

    <input type="hidden" name="id" value="{{ $measure->id }}"/>

    <div class="grid">
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans("cruds.control.fields.clauses") }}</strong>
        	</div>
    		<div class="cell-lg-4 cell-md-5">
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
        		<strong>{{ trans('cruds.control.fields.name') }}</strong>
        	</div>
    		<div class="cell-lg-4 cell-md-5">
        		{{ $measure->name }}
    		</div>
            @if ($measure->scope!==null)
    		<div class="cell-lg-1 cell-md-2" align="right">
        		<strong>{{ trans("cruds.control.fields.scope") }}</strong>
        	</div>
    		<div class="cell-lg-1 cell-md-2">
                <a href="/bob/index?scope={{ $measure->scope }}">
    			{{ $measure->scope }}
                </a>
    		</div>
            @endif
    	</div>
    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.control.fields.objective') }}</strong>
        	</div>
            <div class="cell-lg-6 cell-md-10">
                {!! \Parsedown::instance()->text($measure->objective) !!}
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
    			<strong>{{ trans('cruds.control.fields.plan_date') }}</strong>
        	</div>
    		<div class="cell-lg-2 cell-md-3">
    			<input type="text" data-role="calendarpicker" name="plan_date" value="{{
    			\Carbon\Carbon
    			::createFromFormat('Y-m-d',$measure->plan_date)
    			->format('Y-m-d')
    			}}"
				data-format="YYYY-MM-DD"
				data-inputFormat="YYYY-MM-DD"
                />
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
        		<strong>{{ trans('cruds.control.fields.periodicity') }}</strong>
        	</div>
    		<div class="cell-lg-2 cell-md-3">
    			<select name="periodicity" data-role="select">
    			    <option value="0" {{ $measure->periodicity==0 ? "selected" : ""}}>{{ trans('common.once') }}</option>
    			    <option value="-1" {{ $measure->periodicity==-1 ? "selected" : ""}}>{{ trans('common.weekly') }}</option>
    			    <option value="1" {{ $measure->periodicity==1 ? "selected" : ""}}>{{ trans('common.monthly') }}</option>
    			    <option value="3" {{ $measure->periodicity==3 ? "selected" : ""}}>{{ trans('common.quarterly') }}</option>
    			    <option value="6" {{ $measure->periodicity==6 ? "selected" : ""}}>{{ trans('common.biannually') }}</option>
    			    <option value="12" {{ $measure->periodicity==12 ? "selected" : ""}}>{{ trans('common.annually') }}</option>
    			 </select>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-lg-1 cell-md-2">
    			<strong>{{ trans('cruds.control.fields.owners') }}</strong>
        	</div>
            <div class="cell-lg-4 cell-md-8">
                <select data-role="select" name="owners[]" id="owners" multiple>
                    @foreach($owners as $id => $name)
                        <option
                            value="{{ $id }}"
                            {{ (in_array($id, old('owners', []))) ||
                                (
                                    (str_starts_with($id,'USR_') && $measure->users->contains(intval(substr($id, 4)))) ||
                                    (str_starts_with($id,'GRP_') && $measure->groups->contains(intval(substr($id, 4))))
                                )
                                ? 'selected' : '' }}>
                        {{ $name }}
                        </option>
                    @endforeach
                </select>
    		</div>
    	</div>

    	<div class="row">
    		<div class="cell-12">
    		@if (Auth::User()->role !== 3)
    			<button type="submit" class="button success">
    				<span class="mif-calendar"></span>
    				&nbsp;
    				{{ trans("common.plan") }}
    			</button>
    			</form>
    			&nbsp;
    		@endif
    		@if (Auth::User()->role !== 3)
                <form action="/bob/unplan" method="POST" onSubmit="if(!confirm('{{ trans('common.confirm') }}')){return false;}" class="d-inline">
    				@csrf
    				<input type="hidden" name="id" value="{{ $measure->id }}"/>
    	            <button class="button alert" type="submit">
    					<span class="mif-fire"></span>
    					&nbsp;
    					{{ trans("common.unplan") }}
    				</button>
    			</form>
    			&nbsp;
    		@endif
            <a href="/bob/show/{{$measure->id}}" class="button">
				<span class="mif-cancel"></span>
				&nbsp;
				{{ trans("common.cancel") }}
            </a>
    		</div>
    	</div>
    </div>
    </form>
</div>
@endsection
