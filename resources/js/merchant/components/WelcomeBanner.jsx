import React, { useEffect, useRef, useState } from 'react';

export default function WelcomeBanner({ merchant, period, urls, formatPrice }) {
    const [dateRange, setDateRange] = useState('');
    const inputRef = useRef(null);

    useEffect(() => {
        if (inputRef.current && window.jQuery && jQuery.fn.daterangepicker) {
            const $el = jQuery(inputRef.current);
            if (!$el.data('daterangepicker')) {
                $el.daterangepicker({
                    opens: 'left',
                    autoUpdateInput: false,
                    locale: { cancelLabel: 'Effacer', applyLabel: 'Appliquer', format: 'MM/DD/YYYY' }
                });
                $el.on('apply.daterangepicker', (ev, picker) => {
                    setDateRange(picker.startDate.format('MM/DD/YYYY') + ' To ' + picker.endDate.format('MM/DD/YYYY'));
                    $el.val(picker.startDate.format('MM/DD/YYYY') + ' To ' + picker.endDate.format('MM/DD/YYYY'));
                });
                $el.on('cancel.daterangepicker', () => {
                    setDateRange('');
                    $el.val('');
                });
            }
        }
    }, []);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!dateRange) return;
        const parts = dateRange.split(' To ');
        if (parts.length !== 2) return;
        const form = document.getElementById('wcFilterForm');
        const input = document.getElementById('wcFilterDate');
        if (form && input) {
            input.value = dateRange;
            form.submit();
        }
    };

    return (
        <div className="wc-card overflow-hidden">
            <div className="p-6 md:p-7 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div className="flex items-start gap-4 min-w-0">
                    <div className="w-12 h-12 rounded-[14px] bg-[#eef1f5] text-[#475569] flex items-center justify-center text-[20px] flex-shrink-0">
                        <i className="fas fa-box-open"></i>
                    </div>
                    <div className="min-w-0">
                        <h1 className="text-[21px] md:text-[23px] font-extrabold tracking-tight m-0 leading-tight">
                            Bonjour, <span className="text-wc-primary">{merchant.name}</span>
                        </h1>
                        <p className="text-[13.5px] text-wc-muted m-0 mt-1">
                            {period
                                ? `Voici votre activité du ${period.from} au ${period.to}`
                                : 'Voici un résumé de votre activité des 7 derniers jours'}
                        </p>
                    </div>
                </div>

                <div className="flex flex-wrap items-center gap-2.5 flex-shrink-0">
                    <form onSubmit={handleSubmit} className="flex items-center gap-2">
                        <div className="relative">
                            <i className="fas fa-calendar-alt text-[13px] text-wc-muted-2 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input
                                ref={inputRef}
                                type="text"
                                className="wc-date-picker wc-input pl-10 !w-[215px] md:!w-[245px]"
                                placeholder="Période"
                                autoComplete="off"
                            />
                        </div>
                        <button type="submit" className="wc-btn wc-btn-primary" title="Filtrer sur la période">
                            <i className="fas fa-filter text-[12px]"></i>
                            <span className="hidden sm:inline">Appliquer</span>
                        </button>
                        {period && (
                            <a href={urls.filter.replace('/filter', '')} className="wc-btn wc-btn-outline" title="Réinitialiser le filtre">
                                <i className="fas fa-times text-[12px]"></i>
                            </a>
                        )}
                    </form>
                    <a href={urls.create} className="wc-btn wc-btn-soft">
                        <i className="fas fa-plus text-[12px]"></i>
                        Enregistrer un colis
                    </a>
                </div>
            </div>
        </div>
    );
}