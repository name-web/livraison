import React from 'react';
import WelcomeBanner from './WelcomeBanner';
import KpiCards from './KpiCards';
import ParcelTable from './ParcelTable';
import AreaChart from './AreaChart';
import PieChart from './PieChart';
import FinanceSection from './FinanceSection';
import RecentTransactions from './RecentTransactions';
import EmptyState from './EmptyState';

export default function Dashboard({ merchant, counts, amounts, sales, payments, series, recent, transactions, period, urls, formatPrice, getStatusBadge }) {
    const isEmpty = counts.total === 0 && payments.total === 0;

    return (
<<<<<<< HEAD
        <div className="py-6 px-4 md:px-8 space-y-5 animate-wcFadeUp">
            <WelcomeBanner merchant={merchant} period={period} urls={urls} formatPrice={formatPrice} />
=======
        <div className="py-6 px-4 md:px-8 max-w-[1440px] mx-auto space-y-6 animate-wcFadeUp">
            <WelcomeBanner merchant={merchant} period={period} urls={urls} formatPrice={formatPrice} counts={counts} amounts={amounts} />
>>>>>>> 7e7eef8 (feat(merchant): refonte UI dashboard + shops, formattage Pint)

            {isEmpty ? (
                <EmptyState urls={urls} />
            ) : (
                <>
                    <KpiCards counts={counts} amounts={amounts} sales={sales} merchant={merchant} urls={urls} formatPrice={formatPrice} />

                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-5">
                        <div className="xl:col-span-2">
                            <AreaChart series={series} />
                        </div>
                        <PieChart counts={counts} />
                    </div>

                    <div className="grid grid-cols-1 xl:grid-cols-3 gap-5">
                        <div className="xl:col-span-2">
                            <RecentTransactions transactions={transactions || []} urls={urls} formatPrice={formatPrice} />
                        </div>
                        <FinanceSection amounts={amounts} sales={sales} payments={payments} merchant={merchant} formatPrice={formatPrice} />
                    </div>

                    <div className="grid grid-cols-1 gap-5">
                        <ParcelTable parcels={recent || []} urls={urls} getStatusBadge={getStatusBadge} formatPrice={formatPrice} />
                    </div>
                </>
            )}
        </div>
    );
}