import React, { useEffect, useRef } from 'react';

export default function PieChart({ counts }) {
    const chartRef = useRef(null);
    const instanceRef = useRef(null);

    const total = counts.total || 0;

    useEffect(() => {
        if (!chartRef.current || !window.ApexCharts) return;

        const options = {
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, Nunito, sans-serif',
                animations: { enabled: true, easing: 'easeinout', speed: 500 },
            },
            colors: ['#f59e0b', '#10b981', '#8b5cf6', '#ef4444'],
            series: [
                counts.pending || 0,
                counts.delivered || 0,
                counts.partial || 0,
                counts.returned || 0,
            ],
            labels: ['En attente', 'Livrés', 'Partiels', 'Retournés'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '74%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '12px', fontWeight: 600, color: '#9ca3af' },
                            value: {
                                show: true,
                                fontSize: '26px',
                                fontWeight: 800,
                                color: '#111827',
                                offsetY: 4,
                                formatter: (val) => Math.round(val),
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                fontSize: '12px',
                                fontWeight: 600,
                                color: '#9ca3af',
                                formatter: () => total,
                            },
                        },
                    },
                },
            },
            stroke: { width: 0 },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                fontSize: '12.5px',
                fontWeight: 600,
                labels: { colors: '#6b7280' },
                markers: { radius: 3, width: 10, height: 10 },
                itemMargin: { horizontal: 12, vertical: 4 },
            },
            tooltip: {
                theme: 'light',
                style: { fontSize: '13px', fontFamily: 'Inter, sans-serif' },
                y: { formatter: (val) => `${Math.round(val)} colis` },
            },
        };

        instanceRef.current = new window.ApexCharts(chartRef.current, options);
        instanceRef.current.render();

        return () => {
            if (instanceRef.current) instanceRef.current.destroy();
        };
    }, [counts, total]);

    return (
        <div className="wc-card overflow-hidden h-full flex flex-col">
            <div className="wc-card-header">
                <div className="wc-card-icon bg-wc-warning-soft text-[#d97706]">
                    <i className="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 className="wc-card-title">Répartition des colis</h3>
                    <p className="text-[12px] text-wc-muted m-0">Par statut, sur la période</p>
                </div>
            </div>
            <div className="p-4 md:p-5 flex-1 min-h-0 flex flex-col justify-center">
                <div ref={chartRef} id="wc-pie-chart"></div>
            </div>
        </div>
    );
}