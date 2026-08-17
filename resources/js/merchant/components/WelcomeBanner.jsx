import React, { useEffect, useRef, useState } from 'react';

export default function WelcomeBanner({ merchant, period, urls, formatPrice, counts, amounts }) {
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
        <section className="wcp-hero">
            <div className="wcp-hero-art" aria-hidden="true"></div>
            <div className="wcp-hero-glow" aria-hidden="true"></div>

            <div className="wcp-hero-inner">
                <div className="wcp-hero-top">
                    <div className="min-w-0">
                        <span className="wcp-eyebrow">
                            <i className="fas fa-gauge-high"></i>
                            Tableau de bord
                        </span>

                        <h1 className="wcp-hero-title">
                            Bonjour, <span>{merchant.name}</span>
                        </h1>

                        <p className="wcp-hero-sub">
                            {period
                                ? `Voici votre activité du ${period.from} au ${period.to}.`
                                : 'Voici un aperçu de votre activité des 7 derniers jours.'}
                        </p>

                        <div className="flex flex-wrap items-center gap-2 mt-4">
                            <span className="wcp-chip wcp-chip-money">
                                <i className="fas fa-wallet"></i>
                                Solde : {formatPrice(merchant.currentBalance)}
                            </span>

                            <span className="wcp-chip">
                                <i className="fas fa-coins"></i>
                                Wallet : {formatPrice(merchant.walletBalance)}
                            </span>

                            {period && (
                                <span className="wcp-chip">
                                    <i className="fas fa-calendar-day"></i>
                                    Période personnalisée
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="wcp-hero-actions">
                        <form onSubmit={handleSubmit} className="flex items-center gap-2 flex-wrap">
                            <div className="wcp-date-field">
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

                        <a href={urls.create} className="wc-btn wc-btn-primary">
                            <i className="fas fa-plus"></i>
                            <span>Nouveau colis</span>
                        </a>
                    </div>
                </div>

                <div className="wcp-hero-stats">
                    <div className="wcp-hero-stat">
                        <p className="wcp-hero-stat-v">{(counts && counts.total) || 0}</p>
                        <p className="wcp-hero-stat-l">Total colis</p>
                    </div>
                    <div className="wcp-hero-stat">
                        <p className="wcp-hero-stat-v">{(counts && counts.pending) || 0}</p>
                        <p className="wcp-hero-stat-l">En attente</p>
                    </div>
                    <div className="wcp-hero-stat">
                        <p className="wcp-hero-stat-v">{(counts && counts.delivered) || 0}</p>
                        <p className="wcp-hero-stat-l">Livrés</p>
                    </div>
                    <div className="wcp-hero-stat">
                        <p className="wcp-hero-stat-v">{(amounts && formatPrice(amounts.cash_collection)) || '0 ' + merchant.currency}</p>
                        <p className="wcp-hero-stat-l">Encaissements</p>
                    </div>
                </div>
            </div>
        </section>
    );
}
