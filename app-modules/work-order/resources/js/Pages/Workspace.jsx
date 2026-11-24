import React from 'react';
import { Head, router, useForm } from '@inertiajs/react';

const STATUS_BADGE = {
    queued: { label: 'Antre', style: 'bg-gray-100 text-gray-800' },
    in_progress: { label: 'Dikerjakan', style: 'bg-blue-100 text-blue-800' },
    waiting_parts: { label: 'Tunggu Part', style: 'bg-yellow-100 text-yellow-800' },
    qc_testing: { label: 'QC / Uji Jalan', style: 'bg-purple-100 text-purple-800' },
    completed: { label: 'Selesai', style: 'bg-green-100 text-green-800' },
    cancelled: { label: 'Batal', style: 'bg-red-100 text-red-800' },
};

export default function Workspace({ workOrders = [], vehicles = [], mechanics = [], currentBranch }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        vehicle_id: '',
        mechanic_id: '',
        customer_complaint: '',
        services: [{ service_name: 'Servis Rutin Berkala', labor_cost: 75000 }],
    });

    const addServiceRow = () => {
        setData('services', [...data.services, { service_name: '', labor_cost: 0 }]);
    };

    const updateServiceRow = (index, field, value) => {
        const updated = [...data.services];
        updated[index][field] = field === 'labor_cost' ? parseInt(value, 10) || 0 : value;
        setData('services', updated);
    };

    const removeServiceRow = (index) => {
        setData('services', data.services.filter((_, i) => i !== index));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/work-orders', {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const changeStatus = (workOrderId, nextStatus) => {
        router.patch(`/work-orders/${workOrderId}/status`, {
            status: nextStatus,
        }, {
            preserveScroll: true,
        });
    };

    const formatRupiah = (amount) =>
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);

    return (
        <div className="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
            <Head title="Workspace Bengkel & Work Order" />

            <div className="max-w-7xl mx-auto space-y-6">
                {/* Header */}
                <div className="bg-white p-6 rounded-lg shadow flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-l-4 border-indigo-600">
                    <div>
                        <span className="text-xs font-bold uppercase tracking-wider text-indigo-600">
                            Operasional Bengkel • {currentBranch}
                        </span>
                        <h1 className="text-2xl font-bold text-gray-900 mt-1">
                            Workspace Service Advisor & Mekanik
                        </h1>
                    </div>
                    <a
                        href="/admin"
                        className="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700"
                    >
                        ← Kembali ke Admin Filament
                    </a>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {/* Kolom Kiri: Form Buat Work Order Baru */}
                    <div className="lg:col-span-4">
                        <form onSubmit={handleSubmit} className="bg-white p-6 rounded-lg shadow space-y-4">
                            <h2 className="text-lg font-bold text-gray-800 border-b pb-2">
                                + Daftarkan Servis Baru
                            </h2>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">Pilih Kendaraan</label>
                                <select
                                    value={data.vehicle_id}
                                    onChange={(e) => setData('vehicle_id', e.target.value)}
                                    className="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm"
                                    required
                                >
                                    <option value="">-- Pilih Nomor Polisi --</option>
                                    {vehicles.map((v) => (
                                        <option key={v.id} value={v.id}>
                                            {v.license_plate} — {v.brand} {v.model} ({v.customer?.name})
                                        </option>
                                    ))}
                                </select>
                                {errors.vehicle_id && <p className="text-red-500 text-xs mt-1">{errors.vehicle_id}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">Tugaskan Mekanik (Opsional)</label>
                                <select
                                    value={data.mechanic_id}
                                    onChange={(e) => setData('mechanic_id', e.target.value)}
                                    className="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="">-- Pilih Mekanik --</option>
                                    {mechanics.map((m) => (
                                        <option key={m.id} value={m.id}>{m.name}</option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700">Keluhan Pelanggan</label>
                                <textarea
                                    rows="3"
                                    value={data.customer_complaint}
                                    onChange={(e) => setData('customer_complaint', e.target.value)}
                                    placeholder="Contoh: Tarikan awal gredek, rem depan bunyi..."
                                    className="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm"
                                    required
                                />
                                {errors.customer_complaint && (
                                    <p className="text-red-500 text-xs mt-1">{errors.customer_complaint}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <div className="flex justify-between items-center">
                                    <label className="text-sm font-medium text-gray-700">Daftar Jasa Servis</label>
                                    <button
                                        type="button"
                                        onClick={addServiceRow}
                                        className="text-xs text-indigo-600 font-semibold hover:underline"
                                    >
                                        + Tambah Jasa
                                    </button>
                                </div>

                                {data.services.map((srv, idx) => (
                                    <div key={idx} className="flex gap-2 items-center">
                                        <input
                                            type="text"
                                            placeholder="Nama jasa"
                                            value={srv.service_name}
                                            onChange={(e) => updateServiceRow(idx, 'service_name', e.target.value)}
                                            className="flex-1 text-xs rounded-md border-gray-300"
                                            required
                                        />
                                        <input
                                            type="number"
                                            placeholder="Biaya"
                                            value={srv.labor_cost}
                                            onChange={(e) => updateServiceRow(idx, 'labor_cost', e.target.value)}
                                            className="w-24 text-xs rounded-md border-gray-300"
                                            required
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeServiceRow(idx)}
                                            className="text-red-500 text-xs font-bold"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                ))}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-indigo-600 text-white font-semibold py-2.5 px-4 rounded-md shadow hover:bg-indigo-700 disabled:opacity-50"
                            >
                                {processing ? 'Mendaftarkan...' : 'Buat Work Order & Antrean'}
                            </button>
                        </form>
                    </div>

                    {/* Kolom Kanan: Daftar Antrean & Papan Kerja Mekanik */}
                    <div className="lg:col-span-8 space-y-4">
                        <h2 className="text-lg font-bold text-gray-800">
                            Daftar Antrean Aktif ({workOrders.length})
                        </h2>

                        {workOrders.length === 0 && (
                            <div className="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                                Belum ada Work Order hari ini. Silakan daftarkan kendaraan melalui formulir di sebelah kiri.
                            </div>
                        )}

                        {workOrders.map((wo) => {
                            const badge = STATUS_BADGE[wo.status] || STATUS_BADGE.queued;
                            const totalLabor = (wo.services || []).reduce((sum, s) => sum + Number(s.labor_cost || 0), 0);

                            return (
                                <div key={wo.id} className="bg-white p-5 rounded-lg shadow space-y-3 border border-gray-200">
                                    <div className="flex flex-wrap justify-between items-start gap-2">
                                        <div className="flex items-center gap-3">
                                            <span className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-bold text-sm">
                                                #{wo.queue_number}
                                            </span>
                                            <div>
                                                <div className="flex items-center gap-2">
                                                    <span className="font-bold text-gray-900">{wo.wo_number}</span>
                                                    <span className={`text-xs font-semibold px-2.5 py-0.5 rounded-full ${badge.style}`}>
                                                        {badge.label}
                                                    </span>
                                                    {wo.inspection_mongo_id && (
                                                        <span className="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded">
                                                            ✓ Inspeksi Terlampir
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-sm text-gray-700 mt-0.5">
                                                    <strong>{wo.vehicle?.license_plate}</strong> ({wo.vehicle?.brand} {wo.vehicle?.model}) — {wo.customer?.name}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="text-right text-xs text-gray-500">
                                            <p>Mekanik: <strong>{wo.mechanic?.name || 'Belum ditentukan'}</strong></p>
                                            <p>Estimasi Jasa: <strong className="text-gray-800">{formatRupiah(totalLabor)}</strong></p>
                                        </div>
                                    </div>

                                    <p className="text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                        <strong>Keluhan:</strong> {wo.customer_complaint}
                                    </p>

                                    {/* Tombol Transisi Status Cepat untuk Mekanik */}
                                    <div className="flex flex-wrap items-center gap-2 pt-2 border-t">
                                        <span className="text-xs text-gray-500 mr-1">Ubah Status:</span>
                                        {wo.status === 'queued' && (
                                            <button
                                                onClick={() => changeStatus(wo.id, 'in_progress')}
                                                className="text-xs bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700"
                                            >
                                                ▶ Mulai Kerjakan
                                            </button>
                                        )}
                                        {wo.status === 'in_progress' && (
                                            <>
                                                <button
                                                    onClick={() => changeStatus(wo.id, 'waiting_parts')}
                                                    className="text-xs bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600"
                                                >
                                                    ⏳ Tunggu Suku Cadang
                                                </button>
                                                <button
                                                    onClick={() => changeStatus(wo.id, 'qc_testing')}
                                                    className="text-xs bg-purple-600 text-white px-3 py-1.5 rounded hover:bg-purple-700"
                                                >
                                                    🔍 Masuk QC
                                                </button>
                                            </>
                                        )}
                                        {wo.status === 'waiting_parts' && (
                                            <button
                                                onClick={() => changeStatus(wo.id, 'in_progress')}
                                                className="text-xs bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700"
                                            >
                                                ▶ Lanjutkan Pengerjaan
                                            </button>
                                        )}
                                        {(wo.status === 'in_progress' || wo.status === 'qc_testing') && (
                                            <button
                                                onClick={() => changeStatus(wo.id, 'completed')}
                                                className="text-xs bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700"
                                            >
                                                ✓ Selesaikan Servis
                                            </button>
                                        )}
                                        {wo.status === 'completed' && (
                                            <span className="text-xs font-semibold text-green-700">
                                                Pengerjaan selesai
                                            </span>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        </div>
    );
}