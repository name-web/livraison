import React from 'react';

const INCOME = 1;

export default function RecentTransactions({ transactions = [], urls, formatPrice }) {
    const hasData = Array.isArray(transactions) && transactions.length > 0;

    return (
        <div className="wc-card overflow-hidden h-full flex flex-col">
            <div className="wc-card-header">
                <div className="wc-card-icon bg-[#ecfdf5] text-[#059669]">
                    <i className="fas fa-arrow-right-arrow-left"></i>
                </div>
                <div className="flex-1 min-w-0">
                    <h3 className="wc-card-title">Dernières transactions</h3>
                    <p className="text-[12px] text-wc-muted m-0">Activité récente de votre compte</p>
                </div>
                <a href={urls.statements} className="wc-btn wc-btn-ghost wc-btn-sm flex-shrink-0">
                    Voir tout <i className="fas fa-arrow-right text-[11px]"></i>
                </a>
            </div>

            {hasData ? (
                <div className="flex-1 divide-y divide-wc-border">
                    {transactions.map((t) => {
                        const income = t.type === INCOME;
                        return (
                            <div key={t.id} className="rt-row flex items-center gap-3.5 px-5 py-3.5">
                                <div className={`w-10 h-10 rounded-[12px] flex items-center justify-center text-[13px] flex-shrink-0 ${income ? 'bg-[#ecfdf5] text-[#059669]' : 'bg-[#fef2f2] text-[#dc2626]'}`}>
                                    <i className={`fas ${income ? 'fa-arrow-up' : 'fa-arrow-down'}`}></i>
                                </div>
                                <div className="min-w-0 flex-1">
                                    <p className="text-[13.5px] font-extrabold text-wc-ink m-0 truncate">
                                        {income ? 'Encaissement colis' : 'Paiement livraison'}
                                    </p>
                                    <p className="text-[12px] text-wc-muted m-0 mt-0.5 truncate">
                                        {t.tracking_id ? (
                                            <span className="font-mono text-[11.5px] text-wc-muted-2">{t.tracking_id}</span>
                                        ) : (
                                            'Transaction générale'
                                        )}
                                        <span className="mx-1.5 text-wc-border">•</span>
                                        {t.created_at}
                                    </p>
                                </div>
                                <div className="text-right flex-shrink-0">
                                    <p className={`text-[15px] font-extrabold wc-tabular m-0 ${income ? 'text-[#059669]' : 'text-[#dc2626]'}`}>
                                        {income ? '+' : '-'} {formatPrice(t.amount)}
                                    </p>
                                </div>
                            </div>
                        );
                    })}
                </div>
            ) : (
                <div className="flex-1 flex flex-col items-center justify-center py-10 px-6 text-center">
                    <div className="w-12 h-12 rounded-full flex items-center justify-center mb-3 bg-[#eef1f5] text-[#94a3b8]">
                        <i className="fas fa-receipt text-[16px]"></i>
                    </div>
                    <p className="text-[13.5px] font-bold text-wc-ink m-0">Aucune transaction récente</p>
                    <p className="text-[12px] text-wc-muted m-0 mt-1">Les mouvements de votre compte apparaîtront ici.</p>
                </div>
            )}
        </div>
    );
}