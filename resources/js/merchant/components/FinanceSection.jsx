import React from 'react';

function StatTile({ label, value, accent, icon }) {
    return (
        <div className="flex items-start gap-3 p-4 rounded-[12px] bg-wc-bg border border-wc-border">
            <div className="w-9 h-9 rounded-[10px] flex items-center justify-center text-[14px] flex-shrink-0"
                style={{ background: accent ? '#ecfdf5' : '#fff', color: accent ? '#059669' : '#6b7280' }}>
                <i className={`fas ${icon}`}></i>
            </div>
            <div className="min-w-0">
                <p className="text-[12px] font-bold text-wc-muted m-0 leading-tight">{label}</p>
                <p className="text-[16.5px] font-extrabold text-wc-ink m-0 mt-0.5 wc-tabular leading-tight">{value}</p>
            </div>
        </div>
    );
}

export default function FinanceSection({ amounts, sales, payments, merchant, formatPrice }) {
    const margin = (parseFloat(amounts.cash_collection) || 0) - (parseFloat(amounts.selling_price) || 0);
    const paymentsTotal = parseFloat(payments.total) || 0;
    const paidShare = paymentsTotal > 0 ? Math.min((parseFloat(payments.paid) / paymentsTotal) * 100, 100) : 0;

    return (
        <div className="space-y-5">
            {/* Solde */}
            <div className="wc-card overflow-hidden">
                <div className="wc-card-header">
                    <div className="wc-card-icon bg-[#eef1f5] text-[#475569]">
                        <i className="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Votre solde</h3>
                        <p className="text-[12px] text-wc-muted m-0">Disponible pour vos envois</p>
                    </div>
                </div>
                <div className="px-5 py-5 border-b border-wc-border bg-gradient-to-br from-[#eef1f5] to-transparent">
                    <p className="text-[12px] font-bold text-wc-muted m-0">Solde actuel</p>
                    <p className="text-[30px] font-extrabold text-wc-ink m-0 mt-1 wc-tabular tracking-tight">
                        {formatPrice(merchant.currentBalance)}
                    </p>
                    <div className="flex items-center gap-2 mt-2.5">
                        <span className="wc-badge !bg-[#f3f4f6] !text-[#4b5563]">
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
                <div className="px-5 py-3 text-[12.5px] text-wc-muted border-t border-wc-border bg-wc-bg/60">
                    <i className="fas fa-info-circle mr-1.5 text-wc-muted-2"></i>
                    Le solde wallet est utilisé pour les envois en prépaiement.
                </div>
            </div>

            {/* Aperçu de la période */}
            <div className="wc-card overflow-hidden">
                <div className="wc-card-header">
                    <div className="wc-card-icon bg-wc-warning-soft text-[#d97706]">
                        <i className="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <h3 className="wc-card-title">Aperçu de la période</h3>
                        <p className="text-[12px] text-wc-muted m-0">Encaissements et ventes</p>
                    </div>
                </div>
                <div className="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                        <div className="rounded-[10px] bg-wc-warning-soft px-3.5 py-3">
                            <p className="text-[11px] font-extrabold uppercase tracking-wide text-[#b45309] m-0">En attente</p>
                            <p className="text-[15px] font-extrabold text-[#b45309] m-0 mt-0.5 wc-tabular">{formatPrice(payments.pending)}</p>
                        </div>
                        <div className="rounded-[10px] bg-[#eef1f5] px-3.5 py-3">
                            <p className="text-[11px] font-extrabold uppercase tracking-wide text-[#334155] m-0">Payés</p>
                            <p className="text-[15px] font-extrabold text-[#334155] m-0 mt-0.5 wc-tabular">{formatPrice(payments.paid)}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}