<script type="text/javascript">
    //apext charts parcels
    var options = {
    series: [
         {
            name: '{{ __("dashboard.total")}}',
            type: 'area',
            data: [@foreach($series['totals'] as $value){{ $value }},@endforeach]
        },
        {
            name: '{{  __("dashboard.pending") }}',
            type: 'area',
            data: [@foreach($series['pendings'] as $value){{ $value }},@endforeach]
        },
        {
            name: '{{  __("dashboard.deliver") }}',
            type: 'area',
            data: [@foreach($series['delivers'] as $value){{ $value }},@endforeach]
        },
        {
            name: '{{  __("dashboard.par_deliver") }}',
            type: 'area',
            data: [@foreach($series['parDelivers'] as $value){{ $value }},@endforeach]
        },
        {
            name: '{{  __("dashboard.return") }}',
            type: 'area',
            data: [@foreach($series['returns'] as $value){{ $value }},@endforeach]
        }
    ],
    chart: {
        height: 380,
        width: '100%',
        type: 'area',
    },
    stroke: {
        curve: 'smooth'
    },
    colors:['#2E93fA', '#ff407b','#009688','#2ec551','#0998b0'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.7,
            stops: [0, 100]
        }
    },
    labels: [@foreach($series['dates'] as $date)'{{ $date }}',@endforeach],
    markers: {
        size: 0
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0);
                }
                return y;
            }
        }
    }
};
var chart = new ApexCharts(document.querySelector("#apexparcels"), options);
chart.render();
 //apex charts parcelspiecharts
 var options = {
          series: [{{ $data['counts']['pending'] }},{{ $data['counts']['delivered'] }},{{ $data['counts']['partial'] }},{{ $data['counts']['returned'] }}],
          chart: {
          width: '100%',
          height: 380,
          type: 'pie',
        },
        colors:[ '#ff407b','#009688','#2ec551','#0998b0'],
        labels: ["{{ __('dashboard.pending') }}","{{ __('dashboard.deliver') }}","{{ __('dashboard.par_deliver') }}","{{ __('dashboard.return') }}"],
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 300
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };
var chart = new ApexCharts(document.querySelector("#apexparcelspiechart"), options);
chart.render();
</script>