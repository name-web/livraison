import React from 'react';
import { createRoot } from 'react-dom/client';
import Dashboard from './components/Dashboard';

document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-bs-toggle="dropdown"]');
    if (toggle) {
        e.preventDefault();
        e.stopPropagation();
        const container = toggle.parentElement;
        const isOpen = container.classList.contains('show');
        document.querySelectorAll('body.wc-merchant .dropdown.show').forEach((d) => {
            d.classList.remove('show');
            const t = d.querySelector('[data-bs-toggle="dropdown"]');
            if (t) t.setAttribute('aria-expanded', 'false');
            const m = d.querySelector('.dropdown-menu');
            if (m) m.classList.remove('show');
        });
        if (!isOpen) {
            container.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
            const m = container.querySelector('.dropdown-menu');
            if (m) m.classList.add('show');
        }
        return;
    }
    if (!e.target.closest('.dropdown.show')) {
        document.querySelectorAll('body.wc-merchant .dropdown.show').forEach((d) => {
            d.classList.remove('show');
            const t = d.querySelector('[data-bs-toggle="dropdown"]');
            if (t) t.setAttribute('aria-expanded', 'false');
            const m = d.querySelector('.dropdown-menu');
            if (m) m.classList.remove('show');
        });
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('body.wc-merchant .dropdown.show').forEach((d) => {
        d.classList.remove('show');
        const t = d.querySelector('[data-bs-toggle="dropdown"]');
        if (t) t.setAttribute('aria-expanded', 'false');
        const m = d.querySelector('.dropdown-menu');
        if (m) m.classList.remove('show');
    });
});

function formatPrice(amount, currency = 'FCFA') {
    const num = parseFloat(amount) || 0;
    const formatted = num.toLocaleString('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    return `${formatted} ${currency}`;
}

function getStatusBadge(status) {
    const s = parseInt(status);
    if (s === 1 || s === 2 || s === 3 || s === 4) return { label: 'En attente', color: 'pending', icon: 'fa-clock' };
    if (s === 5 || s === 6 || s === 19) return { label: 'En transit', color: 'transit', icon: 'fa-truck' };
    if (s === 7 || s === 8) return { label: 'En transit', color: 'transit', icon: 'fa-truck-loading' };
    if (s === 9 || s === 10) return { label: 'Livrés', color: 'delivered', icon: 'fa-check-circle' };
    if (s === 32) return { label: 'Partiels', color: 'partial', icon: 'fa-hand-holding-usd' };
    if ([11,12,13,24,26,27,30].includes(s)) return { label: 'Retournés', color: 'returned', icon: 'fa-undo' };
    if ([14,15,16,17,18,20,21,22,23,25,28,29,31,33].includes(s)) return { label: 'Annulés', color: 'neutral', icon: 'fa-ban' };
    return { label: 'Inconnu', color: 'neutral', icon: 'fa-question' };
}

const el = document.getElementById('merchant-app');
if (el) {
    const jsonEl = document.getElementById('merchant-props');
    if (jsonEl) {
        try {
            const props = JSON.parse(jsonEl.textContent);
            props.formatPrice = formatPrice;
            props.getStatusBadge = getStatusBadge;
            createRoot(el).render(<Dashboard {...props} />);
        } catch (e) {
            console.error('Failed to parse merchant props:', e);
        }
    }
}