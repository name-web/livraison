import React, { useEffect, useRef, useState } from 'react';

function useCountUp(target, duration = 900) {
    const [count, setCount] = useState(0);
    const ref = useRef(null);

    useEffect(() => {
        const num = parseFloat(target) || 0;
        if (num === 0) { setCount(0); return; }
        let startTime = null;
        function step(now) {
            if (startTime === null) startTime = now;
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            setCount(num * eased);
            if (progress < 1) ref.current = requestAnimationFrame(step);
        }
        ref.current = requestAnimationFrame(step);
        return () => cancelAnimationFrame(ref.current);
    }, [target, duration]);

    return count;
}

function Tile({ icon, iconBg, iconColor, label, value, url, delay, money, formatPrice }) {
    const count = useCountUp(value);

    return (
        <a
            href={url}
            className="wc-kpi group"
            style={{ animationDelay: `${delay}s` }}
        >
            <div className="wc-kpi-icon" style={{ background: iconBg, color: iconColor }}>
                <i className={`fas ${icon}`}></i>
            </div>
            <div className="flex-1 min-w-0">
                <p className="text-[12.5px] text-wc-muted font-bold m-0 leading-tight truncate">{label}</p>
                <p className="text-[24px] font-extrabold text-wc-ink m-0 leading-tight tracking-tight wc-tabular">
                    {money ? formatPrice(Math.round(count)) : Math.round(count).toLocaleString('fr-FR')}
                </p>
            </div>
        </a>
    );
}

export default function KpiCards({ counts, amounts, sales, merchant, urls, formatPrice }) {
    const margin = (parseFloat(amounts.cash_collection) || 0) - (parseFloat(amounts.selling_price) || 0);

    const moneyKpis = [
        {
            icon: 'fa-hand-holding-usd', iconBg: '#eef1f5', iconColor: '#334155',
            label: 'Encaissements', value: amounts.cash_collection, url: urls.statements, money: true,
        },
        {
            icon: 'fa-coins', iconBg: '#eff6ff', iconColor: '#2563eb',
            label: 'Solde net', value: sales.net, url: urls.wallet, money: true,
        },
        {
            icon: 'fa-chart-simple', iconBg: '#f5f3ff', iconColor: '#7c3aed',
            label: 'Marge', value: margin, url: urls.transaction, money: true,
        },
    ];

    const volumeKpis = [
        { icon: 'fa-box', iconBg: '#eef1f5', iconColor: '#334155', label: 'Total colis', value: counts.total, url: urls.parcelIndex },
        { icon: 'fa-clock', iconBg: '#fffbeb', iconColor: '#d97706', label: 'En attente', value: counts.pending, url: urls.parcelIndex },
        { icon: 'fa-truck', iconBg: '#eff6ff', iconColor: '#2563eb', label: 'En cours', value: counts.on_going, url: urls.parcelIndex },
        { icon: 'fa-check-circle', iconBg: '#ecfdf5', iconColor: '#059669', label: 'Livrés', value: counts.delivered, url: urls.parcelIndex },
        { icon: 'fa-hand-holding-usd', iconBg: '#f5f3ff', iconColor: '#7c3aed', label: 'Partiels', value: counts.partial, url: urls.parcelIndex },
        { icon: 'fa-undo', iconBg: '#fef2f2', iconColor: '#dc2626', label: 'Retournés', value: counts.returned, url: urls.parcelIndex },
    ];

    return (
        <div className="space-y-5">
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {moneyKpis.map((kpi, i) => (
                    <Tile key={kpi.label} {...kpi} delay={i * 0.05} formatPrice={formatPrice} />
                ))}
            </div>
            <div className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
                {volumeKpis.map((kpi, i) => (
                    <Tile key={kpi.label} {...kpi} delay={i * 0.04} formatPrice={formatPrice} />
                ))}
            </div>
        </div>
    );
}