import React, { useEffect, useRef } from 'react';

export default function AreaChart({ series }) {
    const chartRef = useRef(null);
    const instanceRef = useRef(null);

    useEffect(() => {
        if (!chartRef.current || !window.ApexCharts) return;

        const options = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, Nunito, sans-serif',
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 500 },
                zoom: { enabled: false },
            },
            colors: ['#34d399', '#fbbf24', '#38bdf8', '#a78bfa', '#fb7185'],
            series: [
                { name: 'Total', data: series.totals || [] },
                { name: 'En attente', data: series.pendings || [] },
                { name: 'Livrés', data: series.delivers || [] },
                { name: 'Partiels', data: series.parDelivers || [] },
                { name: 'Retournés', data: series.returns || [] },
            ],
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.22, opacityTo: 0.03, stops: [0, 90, 100] },
            },
            xaxis: {
                categories: series.dates || [],
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 },
                    rotate: series.dates.length > 12 ? -35 : 0,
                    rotateAlways: false,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px', fontWeight: 600 },
                    formatter: (val) => Math.round(val),
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12.5px',
                fontWeight: 600,
                labels: { colors: '#cbd5e1' },
                markers: { radius: 3, width: 10, height: 10 },
                itemMargin: { horizontal: 12, vertical: 2 },
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '13px', fontFamily: 'Inter, sans-serif' },
                y: { formatter: (val) => `${Math.round(val)} colis` },
            },
            grid: { borderColor: 'rgba(255,255,255,.07)', strokeDashArray: 4, padding: { top: 8 } },
            dataLabels: { enabled: false },
        };

        instanceRef.current = new window.ApexCharts(chartRef.current, options);
        instanceRef.current.render();

        return () => {
            if (instanceRef.current) instanceRef.current.destroy();
        };
    }, [series]);

    return (
        <div className="wc-card overflow-hidden h-full flex flex-col">
            <div className="wc-card-header">
                <div className="wc-card-icon" style={{ background: 'rgba(56,189,248,.14)', color: '#38bdf8' }}>
                    <i className="fas fa-chart-line"></i>
                </div>
                <div>
                    <h3 className="wc-card-title">Activité des colis</h3>
                    <p className="text-[12px] text-wc-muted m-0">Évolution quotidienne sur la période</p>
                </div>
            </div>
            <div className="p-4 md:p-5 flex-1 min-h-0">
                <div ref={chartRef} id="wc-area-chart"></div>
            </div>
        </div>
    );
}