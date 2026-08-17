import React from 'react';

function StatTile({ label, value, icon, grad }) {
    return (
<<<<<<< HEAD
        <div className="flex items-center gap-3 p-4 rounded-[12px] border min-h-[72px]"
            style={{
                background: '#f8fafc',
                borderColor: '#eef0f3',
            }}>
            <div className="w-9 h-9 rounded-[10px] flex items-center justify-center text-[14px] flex-shrink-0"
                style={accent
                    ? { background: '#ecfdf5', color: '#059669' }
                    : { background: '#eef1f5', color: '#64748b' }}>
=======
        <div className="wcp-stat">
            <div className={`wcp-stat-ic ${grad}`}>
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                <i className={`fas ${icon}`}></i>
            </div>
            <div className="min-w-0">
                <p className="wcp-stat-l truncate">{label}</p>
                <p className="wcp-stat-v truncate">{value}</p>
            </div>
        </div>
    );
}

export default function FinanceSection({ amounts, sales, payments, merchant, formatPrice }) {
    const margin = (parseFloat(amounts.cash_collection) || 0) - (parseFloat(amounts.selling_price) || 0);
    const paymentsTotal = parseFloat(payments.total) || 0;
    const paidShare = paymentsTotal > 0 ? Math.min((parseFloat(payments.paid) / paymentsTotal) * 100, 100) : 0;

    return (
        <div className="space-y-5 h-full flex flex-col">
            {/* Solde */}
            <div className="wc-card overflow-hidden flex-1 flex flex-col">
                <div className="wc-card-header">
<<<<<<< HEAD
                    <div className="wc-card-icon bg-[#eef1f5] text-[#64748b]">
=======
                    <div className="wc-card-icon g-teal">
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                        <i className="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Votre solde</h3>
                        <p className="text-[12px] text-wc-muted m-0">Disponible pour vos envois</p>
                    </div>
                </div>
<<<<<<< HEAD
                <div className="px-5 py-5 border-b border-wc-border" style={{ background: 'linear-gradient(180deg, #f5f3ff, #fafafc)' }}>
                    <p className="text-[12px] font-bold text-wc-muted m-0">Solde actuel</p>
                    <p className="text-[30px] font-extrabold text-wc-ink m-0 mt-1 wc-tabular tracking-tight">
                        {formatPrice(merchant.currentBalance)}
                    </p>
                    <div className="flex items-center gap-2 mt-2.5">
                        <span className="wc-badge" style={{ background: '#eef1f5', color: '#475569' }}>
                            <i className="fas fa-coins text-[11px]"></i>
                            Wallet : {formatPrice(merchant.walletBalance)}
                        </span>
                    </div>
                </div>
                <div className="px-5 py-1.5">
                    <div className="flex items-center justify-between py-3 border-b border-wc-border">
                        <span className="text-[13.5px] font-semibold text-wc-muted">Solde d'ouverture</span>
                        <span className="text-[14.5px] font-extrabold text-wc-ink wc-tabular">{formatPrice(merchant.openingBalance)}</span>
                    </div>
                    <div className="flex items-center justify-between py-3">
                        <span className="text-[13.5px] font-semibold text-wc-muted">TVA marchand</span>
                        <span className="text-[14.5px] font-extrabold text-wc-ink wc-tabular">{formatPrice(merchant.vat)}</span>
                    </div>
                </div>
                <div className="px-5 py-3 text-[12.5px] text-wc-muted border-t border-wc-border mt-auto" style={{ background: '#f8fafc' }}>
=======

                <div className="wcp-fin">
                    <p className="wcp-fin-l">Solde actuel</p>
                    <p className="wcp-fin-amt">{formatPrice(merchant.currentBalance)}</p>
                    <span className="wcp-fin-chip">
                        <i className="fas fa-coins"></i>
                        Wallet : {formatPrice(merchant.walletBalance)}
                    </span>
                </div>

                <div className="px-5">
                    <div className="wcp-fin-row">
                        <span className="wcp-fin-row-l">Solde d'ouverture</span>
                        <span className="wcp-fin-row-v">{formatPrice(merchant.openingBalance)}</span>
                    </div>
                    <div className="wcp-fin-row">
                        <span className="wcp-fin-row-l">TVA marchand</span>
                        <span className="wcp-fin-row-v">{formatPrice(merchant.vat)}</span>
                    </div>
                </div>

                <div className="px-5 py-3 text-[12.5px] text-wc-muted border-t border-wc-border" style={{ background: 'rgba(100,116,139,.04)' }}>
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                    <i className="fas fa-info-circle mr-1.5 text-wc-muted-2"></i>
                    Le solde wallet est utilisé pour les envois en prépaiement.
                </div>
            </div>

            {/* Aperçu de la période */}
            <div className="wc-card overflow-hidden flex-1 flex flex-col">
                <div className="wc-card-header">
<<<<<<< HEAD
                    <div className="wc-card-icon bg-[#fffbeb] text-[#d97706]">
=======
                    <div className="wc-card-icon g-amber">
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                        <i className="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Aperçu de la période</h3>
                        <p className="text-[12px] text-wc-muted m-0">Encaissements et ventes</p>
                    </div>
                </div>
<<<<<<< HEAD
                <div className="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1 content-start">
                    <StatTile icon="fa-hand-holding-usd" label="Encaissements" value={formatPrice(amounts.cash_collection)} />
                    <StatTile icon="fa-tags" label="Prix de vente" value={formatPrice(amounts.selling_price)} />
                    <StatTile icon="fa-chart-simple" label="Marge" value={formatPrice(margin)} accent />
                    <StatTile icon="fa-truck" label="Frais de livraison" value={formatPrice(sales.delivery_fee)} />
                    <StatTile icon="fa-percent" label="TVA payée" value={formatPrice(sales.vat)} />
                    <StatTile icon="fa-coins" label="Solde net" value={formatPrice(sales.net)} accent />
=======
                <div className="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <StatTile icon="fa-hand-holding-usd" grad="g-emerald" label="Encaissements" value={formatPrice(amounts.cash_collection)} />
                    <StatTile icon="fa-tags" grad="g-blue" label="Prix de vente" value={formatPrice(amounts.selling_price)} />
                    <StatTile icon="fa-chart-simple" grad="g-violet" label="Marge" value={formatPrice(margin)} />
                    <StatTile icon="fa-truck" grad="g-amber" label="Frais de livraison" value={formatPrice(sales.delivery_fee)} />
                    <StatTile icon="fa-percent" grad="g-slate" label="TVA payée" value={formatPrice(sales.vat)} />
                    <StatTile icon="fa-coins" grad="g-teal" label="Solde net" value={formatPrice(sales.net)} />
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                </div>

                <div className="border-t border-wc-border"></div>

                <div className="p-5">
                    <h4 className="text-[13.5px] font-extrabold text-wc-ink m-0 mb-4">Paiements</h4>
                    <div className="flex items-center justify-between mb-2">
                        <span className="text-[12.5px] font-bold text-wc-muted">Payés</span>
                        <span className="text-[12.5px] font-bold text-wc-muted-2 wc-tabular">
                            {Math.round(paidShare)}%
                        </span>
                    </div>
                    <div className="wc-progress">
                        <div className="wc-progress-bar" style={{ width: `${paidShare}%` }}></div>
                    </div>
                    <div className="grid grid-cols-2 gap-3 mt-4">
<<<<<<< HEAD
                        <div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(245,158,11,.10)', border: '1px solid rgba(245,158,11,.28)' }}>
                            <p className="text-[11px] font-extrabold uppercase tracking-wide m-0" style={{ color: '#d97706' }}>En attente</p>
                            <p className="text-[15px] font-extrabold m-0 mt-0.5 wc-tabular" style={{ color: '#d97706' }}>{formatPrice(payments.pending)}</p>
                        </div>
                        <div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(148,163,184,.10)', border: '1px solid rgba(148,163,184,.28)' }}>
=======
                        <div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(245,158,11,.12)', border: '1px solid rgba(217,119,6,.25)' }}>
                            <p className="text-[11px] font-extrabold uppercase tracking-wide m-0" style={{ color: '#d97706' }}>En attente</p>
                            <p className="text-[15px] font-extrabold m-0 mt-0.5 wc-tabular" style={{ color: '#d97706' }}>{formatPrice(payments.pending)}</p>
                        </div>
                        <div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(100,116,139,.1)', border: '1px solid rgba(100,116,139,.22)' }}>
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)
                            <p className="text-[11px] font-extrabold uppercase tracking-wide m-0" style={{ color: '#64748b' }}>Payés</p>
                            <p className="text-[15px] font-extrabold m-0 mt-0.5 wc-tabular" style={{ color: '#64748b' }}>{formatPrice(payments.paid)}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
