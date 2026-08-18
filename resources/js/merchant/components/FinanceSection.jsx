import React from 'react';

function StatTile({ label, value, icon, grad, accent }) {
    return (
        <div className="flex items-center gap-3 p-4 rounded-[12px] border min-h-[72px]"
            style={{
                background: '#f8fafc',
                borderColor: '#eef0f3',
            }}>
            <div className="w-9 h-9 rounded-[10px] flex items-center justify-center text-[14px] flex-shrink-0"
                style={accent
                    ? { background: '#ecfdf5', color: '#059669' }
                    : { background: '#eef1f5', color: '#64748b' }}>
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
<div className="wc-card-icon bg-[#eef1f5] text-[#64748b]">
                        <i className="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Votre solde</h3>
                        <p className="text-[12px] text-wc-muted m-0">Disponible pour vos envois</p>
                    </div>
                </div>
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
                    <i className="fas fa-info-circle mr-1.5 text-wc-muted-2"></i>
                    Le solde wallet est utilisé pour les envois en prépaiement.
                </div>
            </div>

            {/* Aperçu de la période */}
            <div className="wc-card overflow-hidden flex-1 flex flex-col">
                <div className="wc-card-header">
<div className="wc-card-icon bg-[#fffbeb] text-[#d97706]">
                        <i className="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Aperçu de la période</h3>
                        <p className="text-[12px] text-wc-muted m-0">Encaissements et ventes</p>
                    </div>
                </div>
<div className="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1 content-start">
                    <StatTile icon="fa-hand-holding-usd" label="Encaissements" value={formatPrice(amounts.cash_collection)} />
                    <StatTile icon="fa-tags" label="Prix de vente" value={formatPrice(amounts.selling_price)} />
                    <StatTile icon="fa-chart-simple" label="Marge" value={formatPrice(margin)} accent />
                    <StatTile icon="fa-truck" label="Frais de livraison" value={formatPrice(sales.delivery_fee)} />
                    <StatTile icon="fa-percent" label="TVA payée" value={formatPrice(sales.vat)} />
                    <StatTile icon="fa-coins" label="Solde net" value={formatPrice(sales.net)} accent />
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
<div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(245,158,11,.10)', border: '1px solid rgba(245,158,11,.28)' }}>
                            <p className="text-[11px] font-extrabold uppercase tracking-wide m-0" style={{ color: '#d97706' }}>En attente</p>
                            <p className="text-[15px] font-extrabold m-0 mt-0.5 wc-tabular" style={{ color: '#d97706' }}>{formatPrice(payments.pending)}</p>
                        </div>
                        <div className="rounded-[10px] px-3.5 py-3 text-center" style={{ background: 'rgba(148,163,184,.10)', border: '1px solid rgba(148,163,184,.28)' }}>
                            <p className="text-[11px] font-extrabold uppercase tracking-wide m-0" style={{ color: '#64748b' }}>Payés</p>
                            <p className="text-[15px] font-extrabold m-0 mt-0.5 wc-tabular" style={{ color: '#64748b' }}>{formatPrice(payments.paid)}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
