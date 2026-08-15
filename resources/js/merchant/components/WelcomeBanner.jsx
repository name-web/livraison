import React, { useEffect, useRef, useState } from 'react';

export default function WelcomeBanner({ merchant, period, urls }) {
    const [dateRange, setDateRange] = useState('');
    const inputRef = useRef(null);

    useEffect(() => {
        if (!inputRef.current || !window.jQuery || !jQuery.fn.daterangepicker) {
            return;
        }

        const $el = jQuery(inputRef.current);

        if ($el.data('daterangepicker')) {
            return;
        }

        $el.daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Effacer',
                applyLabel: 'Appliquer',
                format: 'MM/DD/YYYY',
            },
        });

        $el.on('apply.daterangepicker', (ev, picker) => {
            const value =
                `${picker.startDate.format('MM/DD/YYYY')} To ` +
                `${picker.endDate.format('MM/DD/YYYY')}`;

            setDateRange(value);
            $el.val(value);
        });

        $el.on('cancel.daterangepicker', () => {
            setDateRange('');
            $el.val('');
        });

        return () => {
            $el.off('.daterangepicker');

            if ($el.data('daterangepicker')) {
                $el.data('daterangepicker').remove();
            }
        };
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();

        if (!dateRange) {
            return;
        }

        const parts = dateRange.split(' To ');

        if (parts.length !== 2) {
            return;
        }

        const form = document.getElementById('wcFilterForm');
        const input = document.getElementById('wcFilterDate');

        if (form && input) {
            input.value = dateRange;
            form.submit();
        }
    };

    const resetUrl = urls.filter.replace('/filter', '');

    return (
        <section className="wc-dashboard-hero">
            <div className="wc-dashboard-hero-main">
                <div className="flex items-start gap-4 min-w-0">
                    <div className="wc-dashboard-hero-icon">
                        <i className="fas fa-chart-line"></i>
                    </div>

                    <div className="min-w-0">
                        <div className="flex flex-wrap items-center gap-2 mb-1.5">
                            <span className="wc-eyebrow">
                                Tableau de bord
                            </span>

                            {period && (
                                <span className="wc-period-badge">
                                    <i className="fas fa-calendar-day"></i>
                                    Période personnalisée
                                </span>
                            )}
                        </div>

                        <h1 className="wc-dashboard-title">
                            Bonjour, <span>{merchant.name}</span>
                        </h1>

                        <p className="wc-dashboard-subtitle">
                            {period
                                ? `Voici votre activité du ${period.from} au ${period.to}.`
                                : 'Voici un aperçu de votre activité des 7 derniers jours.'}
                        </p>
                    </div>
                </div>

                <div className="wc-dashboard-actions">
                    <form onSubmit={handleSubmit} className="wc-filter-form">
                        <div className="wc-date-field">
                            <i className="fas fa-calendar-alt"></i>

                            <input
                                ref={inputRef}
                                type="text"
                                className="wc-input"
                                placeholder="Sélectionner une période"
                                autoComplete="off"
                                aria-label="Sélectionner une période"
                            />
                        </div>

                        <button
                            type="submit"
                            className="wc-btn wc-btn-primary"
                            title="Appliquer le filtre"
                        >
                            <i className="fas fa-filter"></i>
                            <span>Filtrer</span>
                        </button>

                        {period && (
                            <a
                                href={resetUrl}
                                className="wc-btn wc-btn-outline"
                                title="Réinitialiser le filtre"
                                aria-label="Réinitialiser le filtre"
                            >
                                <i className="fas fa-rotate-left"></i>
                            </a>
                        )}
                    </form>

                    <a href={urls.create} className="wc-btn wc-btn-dark">
                        <i className="fas fa-plus"></i>
                        <span>Nouveau colis</span>
                    </a>
                </div>
            </div>
        </section>
    );
}
