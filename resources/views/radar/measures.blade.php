@extends("layout")

@section("content")
<form action="/radar/domains">
    <div data-role="panel" data-title-caption={{ trans("cruds.welcome.dashboard") }} data-collapsible="false" data-title-icon="<span class='mif-gauge'></span>">

        <div class="row">
            <div class="cell-2">
                <strong>{{ trans("cruds.measure.fields.clause") }}</strong>
                <select name="clauses" data-role="select" id="clauses" data-filter="true">
                    <option></option>
                    @foreach ($clauses as $clause)
                    <option value="{{ trim($clause) }}"
                        @if (request()->get('clause') == trim($clause)) selected @endif>
                        {{ $clause }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="cell-2">
            @if (($scopes!=null) && ($scopes->count()>0))
                <strong>{{ trans("cruds.measure.fields.scope") }}</strong>
                <select name="scopes" data-role="select" id="scopes" data-filter="true">
                    <option></option>
                    @foreach ($scopes as $scope)
                    <option value="{{ trim($scope) }}"
                        @if (request()->get('scope') == trim($scope)) selected @endif>
                        {{ $scope }}
                    </option>
                    @endforeach
                </select>
            @endif
            </div>
        </div>
    </div>

    @foreach($controls as $control)
    <div class="mt-2" data-role="panel" data-title-caption="{{ $control->name }}" data-collapsible="true" data-title-icon="<span class='mif-line-chart'></span>">
        <div class="p-7" style="height: 300px;">
            <canvas id="scoreChart-{{ $control->id }}"></canvas>
        </div>

        <div>
            <div style="overflow-x: auto;">
                <table class="table table-border cell-border striped" style="width: max-content;">
                    <tbody>
                        <tr>
                            <td class="fw-bold">{{ trans("cruds.measure.fields.realisation_date") }}</td>
                            @foreach($control->measures as $measure)
                            <td><a href="/bob/show/{{ $measure->id }}">{{ $measure->realisation_date }}</a></td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ trans("cruds.measure.fields.score") }}</td>
                            @foreach($control->measures as $measure)
                            <td class="text-center"
                                {!! $measure->score == 1 ? 'style="background-color: #ce352c;"' : '' !!}
                                {!! $measure->score == 2 ? 'style="background-color: #fa6800;"' : '' !!}
                                {!! $measure->score == 3 ? 'style="background-color: #60a917;"' : '' !!}>
                            {{ $measure->note }}
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endforeach

</form>

<script>
window.addEventListener('DOMContentLoaded', function() {
    var clauseSelect = document.getElementById('clauses');
    clauseSelect.addEventListener('change', function() {
        const clauseSelectOption = clauseSelect.options[clauseSelect.selectedIndex];
        window.location = '/radar/bob?clause=' + encodeURIComponent(clauseSelectOption.value);
    }, false);

    var scopeSelect = document.getElementById('scopes');
    if (scopeSelect!=null)
        scopeSelect.addEventListener('change', function() {
            const clauseSelectOption = clauseSelect.options[clauseSelect.selectedIndex];
            const scopeSelectOption = scopeSelect.options[scopeSelect.selectedIndex];
            window.location = '/radar/bob?clause=' +
                encodeURIComponent(clauseSelectOption.value) +
                '&scope=' + encodeURIComponent(scopeSelectOption.value);
        }, false);


@foreach($controls as $control)

    const labels{{$control->id}} = @json($control->measures->pluck('realisation_date'));
    const data{{$control->id}} = @json($control->measures->pluck('note'));

    const ctx{{$control->id}} = document.getElementById('scoreChart-{{$control->id}}').getContext('2d');

    new Chart(ctx{{$control->id}}, {
        type: 'line',
        data: {
            labels: labels{{$control->id}},
            datasets: [{
                data: data{{$control->id}},
                borderColor: 'rgba(0,123,255,1)',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true,
                tension: 0.2, // Remplace lineTension
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgba(0,123,255,1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                },
                datalabels: {
                    display: false
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day',
                        tooltipFormat: 'yyyy-MM-dd'
                    },
                    ticks: {
                        autoSkip: true
                    }
                },
                y: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
@endforeach
});
</script>

@endsection
