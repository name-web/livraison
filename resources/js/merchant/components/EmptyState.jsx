import React from 'react';

export default function EmptyState({ urls }) {
    return (
        <div className="wc-card overflow-hidden">
            <div className="wc-empty">
                <div className="wc-empty-icon">
                    <i className="fas fa-box-open"></i>
                </div>
                <h2 className="text-[20px] font-extrabold text-wc-ink m-0 mb-2">Bienvenue sur votre tableau de bord</h2>
                <p className="text-[14px] text-wc-muted mb-7 max-w-md mx-auto">
                    Enregistrez votre premier colis pour voir apparaître ici vos statistiques, vos graphiques et vos finances.
                </p>
                <div className="flex items-center justify-center gap-3 flex-wrap">
                    <a href={urls.create} className="wc-btn wc-btn-primary wc-btn-lg inline-flex">
                        <i className="fas fa-plus text-[14px]"></i>
                        Enregistrer un colis
                    </a>
                    <a href={urls.shops} className="wc-btn wc-btn-outline wc-btn-lg inline-flex">
                        <i className="fas fa-store text-[14px]"></i>
                        Mes boutiques
                    </a>
                </div>
            </div>
        </div>
    );
}