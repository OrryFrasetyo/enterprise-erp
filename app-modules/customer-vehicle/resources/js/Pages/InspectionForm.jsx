import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

const getInitialChecklistByTransmission = (transmission) => {
    const common = {
        brakes_and_tires: {
            front_brake_pad: { status: "good", note: "" },
            rear_brake_pad: { status: "good", note: "" },
            tire_condition: { status: "good", note: "" },
        },
        electrical: {
            headlamp_and_signals: { status: "good", note: "" },
            battery_health: { status: "good", note: "" },
        },
    };

    if (transmission === "cvt" || transmission === "matic") {
        return {
            ...common,
            cvt_and_engine: {
                v_belt_condition: { status: "good", note: "" },
                roller_and_pulley: { status: "good", note: "" },
                engine_oil_leak: { status: "good", note: "" },
            },
        };
    }

    if (transmission === "ev") {
        return {
            ...common,
            electric_drive: {
                battery_soh_percentage: { status: "good", note: "" },
                bldc_hub_motor: { status: "good", note: "" },
                controller_socket: { status: "good", note: "" },
            },
        };
    }

    return {
        ...common,
        manual_drivetrain: {
            chain_and_sprocket: { status: "good", note: "" },
            clutch_response: { status: "good", note: "" },
            engine_oil_leak: { status: "good", note: "" },
        },
    };
};

export default function InspectionForm({ vehicle, recentInspections = [] }) {
    const [savedBanner, setSavedBanner] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        current_odometer: vehicle.current_odometer || 0,
        fuel_level_percentage: 50,
        checklist_groups: getInitialChecklistByTransmission(
            vehicle.transmission_type,
        ),
        body_defects: [],
        customer_belongings: ["Helm"],
    });

    const updateCheckItem = (groupKey, itemKey, field, value) => {
        setData("checklist_groups", {
            ...data.checklist_groups,
            [groupKey]: {
                ...data.checklist_groups[groupKey],
                [itemKey]: {
                    ...data.checklist_groups[groupKey][itemKey],
                    [field]: value,
                },
            },
        });
    };

    const addBodyDefect = () => {
        setData("body_defects", [
            ...data.body_defects,
            { zone: "", type: "scratch", note: "" },
        ]);
    };

    const updateBodyDefect = (index, field, value) => {
        const updated = [...data.body_defects];
        updated[index][field] = value;
        setData("body_defects", updated);
    };

    const removeBodyDefect = (index) => {
        setData(
            "body_defects",
            data.body_defects.filter((_, i) => i !== index),
        );
    };

    const addBelonging = () => {
        setData("customer_belongings", [...data.customer_belongings, ""]);
    };

    const updateBelonging = (index, value) => {
        const updated = [...data.customer_belongings];
        updated[index] = value;
        setData("customer_belongings", updated);
    };

    const removeBelonging = (index) => {
        setData(
            "customer_belongings",
            data.customer_belongings.filter((_, i) => i !== index),
        );
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("customer-vehicle.inspect.store", vehicle.id), {
            preserveScroll: true,
            onSuccess: () => setSavedBanner(true),
        });
    };

    return (
        <div className="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">
            <Head title={`Inspeksi ${vehicle.license_plate}`} />

            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header Informasi Kendaraan (Dari PostgreSQL) */}
                <div className="bg-white p-6 rounded-lg shadow border-l-4 border-indigo-600">
                    <div className="flex justify-between items-start">
                        <div>
                            <span className="text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded">
                                Transmisi:{" "}
                                {vehicle.transmission_type.toUpperCase()}
                            </span>
                            <h1 className="text-2xl font-bold text-gray-900 mt-2">
                                {vehicle.license_plate} — {vehicle.brand}{" "}
                                {vehicle.model} ({vehicle.year})
                            </h1>
                            <p className="text-sm text-gray-600">
                                Pemilik:{" "}
                                <span className="font-semibold">
                                    {vehicle.customer?.name}
                                </span>{" "}
                                ({vehicle.customer?.phone})
                            </p>
                        </div>
                        <div className="text-right text-xs text-gray-500">
                            <p>No. Rangka: {vehicle.vin}</p>
                            <p>No. Mesin: {vehicle.engine_number}</p>
                        </div>
                    </div>
                </div>

                {savedBanner && (
                    <div className="bg-green-50 border border-green-400 text-green-800 px-4 py-3 rounded">
                        Dokumen checklist inspeksi berhasil disimpan ke MongoDB!
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Parameter Dasar */}
                    <div className="bg-white p-6 rounded-lg shadow grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Odometer Saat Ini (KM)
                            </label>
                            <input
                                type="number"
                                value={data.current_odometer}
                                onChange={(e) =>
                                    setData(
                                        "current_odometer",
                                        parseInt(e.target.value, 10) || 0,
                                    )
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.current_odometer && (
                                <p className="text-red-500 text-xs mt-1">
                                    {errors.current_odometer}
                                </p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Indikator BBM / Baterai (
                                {data.fuel_level_percentage}%)
                            </label>
                            <input
                                type="range"
                                min="0"
                                max="100"
                                step="5"
                                value={data.fuel_level_percentage}
                                onChange={(e) =>
                                    setData(
                                        "fuel_level_percentage",
                                        parseInt(e.target.value, 10),
                                    )
                                }
                                className="mt-3 block w-full accent-indigo-600"
                            />
                        </div>
                    </div>

                    {/* Dynamic Checklist Groups (Menyesuaikan Transmisi) */}
                    {Object.entries(data.checklist_groups).map(
                        ([groupKey, items]) => (
                            <div
                                key={groupKey}
                                className="bg-white p-6 rounded-lg shadow space-y-4"
                            >
                                <h2 className="text-lg font-bold text-gray-800 capitalize border-b pb-2">
                                    Pemeriksaan: {groupKey.replace(/_/g, " ")}
                                </h2>

                                <div className="space-y-3">
                                    {Object.entries(items).map(
                                        ([itemKey, itemValue]) => (
                                            <div
                                                key={itemKey}
                                                className="grid grid-cols-1 md:grid-cols-12 gap-3 items-center bg-gray-50 p-3 rounded"
                                            >
                                                <span className="md:col-span-4 text-sm font-medium text-gray-700 capitalize">
                                                    {itemKey.replace(/_/g, " ")}
                                                </span>

                                                <div className="md:col-span-3">
                                                    <select
                                                        value={itemValue.status}
                                                        onChange={(e) =>
                                                            updateCheckItem(
                                                                groupKey,
                                                                itemKey,
                                                                "status",
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="block w-full text-sm rounded-md border-gray-300 shadow-sm"
                                                    >
                                                        <option value="good">
                                                            Normal / Baik
                                                        </option>
                                                        <option value="warning">
                                                            Perlu Perhatian
                                                        </option>
                                                        <option value="replace">
                                                            Wajib Ganti / Rusak
                                                        </option>
                                                    </select>
                                                </div>

                                                <div className="md:col-span-5">
                                                    <input
                                                        type="text"
                                                        placeholder="Catatan mekanik / SA (opsional)..."
                                                        value={itemValue.note}
                                                        onChange={(e) =>
                                                            updateCheckItem(
                                                                groupKey,
                                                                itemKey,
                                                                "note",
                                                                e.target.value,
                                                            )
                                                        }
                                                        className="block w-full text-sm rounded-md border-gray-300 shadow-sm"
                                                    />
                                                </div>
                                            </div>
                                        ),
                                    )}
                                </div>
                            </div>
                        ),
                    )}

                    {/* Catatan Lecet / Cacat Bodi Kendaraan */}
                    <div className="bg-white p-6 rounded-lg shadow space-y-4">
                        <div className="flex justify-between items-center border-b pb-2">
                            <h2 className="text-lg font-bold text-gray-800">
                                Temuan Cacat / Lecet Bodi
                            </h2>
                            <button
                                type="button"
                                onClick={addBodyDefect}
                                className="text-sm bg-gray-800 text-white px-3 py-1.5 rounded hover:bg-gray-700"
                            >
                                + Tambah Titik Lecet
                            </button>
                        </div>

                        {data.body_defects.length === 0 && (
                            <p className="text-sm text-gray-500 italic">
                                Tidak ada cacat bodi yang dicatat.
                            </p>
                        )}

                        {data.body_defects.map((defect, index) => (
                            <div
                                key={index}
                                className="grid grid-cols-1 md:grid-cols-12 gap-3 items-center"
                            >
                                <input
                                    type="text"
                                    placeholder="Bagian bodi (misal: Spakbor Depan)"
                                    value={defect.zone}
                                    onChange={(e) =>
                                        updateBodyDefect(
                                            index,
                                            "zone",
                                            e.target.value,
                                        )
                                    }
                                    className="md:col-span-4 text-sm rounded-md border-gray-300"
                                    required
                                />
                                <select
                                    value={defect.type}
                                    onChange={(e) =>
                                        updateBodyDefect(
                                            index,
                                            "type",
                                            e.target.value,
                                        )
                                    }
                                    className="md:col-span-3 text-sm rounded-md border-gray-300"
                                >
                                    <option value="scratch">
                                        Baret / Lecet
                                    </option>
                                    <option value="dent">Penyok</option>
                                    <option value="crack">Retak / Pecah</option>
                                </select>
                                <input
                                    type="text"
                                    placeholder="Keterangan detail..."
                                    value={defect.note}
                                    onChange={(e) =>
                                        updateBodyDefect(
                                            index,
                                            "note",
                                            e.target.value,
                                        )
                                    }
                                    className="md:col-span-4 text-sm rounded-md border-gray-300"
                                />
                                <button
                                    type="button"
                                    onClick={() => removeBodyDefect(index)}
                                    className="md:col-span-1 text-red-600 text-sm font-bold hover:underline"
                                >
                                    Hapus
                                </button>
                            </div>
                        ))}
                    </div>

                    {/* Barang Bawaan Pelanggan */}
                    <div className="bg-white p-6 rounded-lg shadow space-y-4">
                        <div className="flex justify-between items-center border-b pb-2">
                            <h2 className="text-lg font-bold text-gray-800">
                                Barang Bawaan Pelanggan
                            </h2>
                            <button
                                type="button"
                                onClick={addBelonging}
                                className="text-sm bg-gray-800 text-white px-3 py-1.5 rounded hover:bg-gray-700"
                            >
                                + Tambah Barang
                            </button>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {data.customer_belongings.map((item, index) => (
                                <div key={index} className="flex gap-2">
                                    <input
                                        type="text"
                                        placeholder="Contoh: Jas Hujan, Helm..."
                                        value={item}
                                        onChange={(e) =>
                                            updateBelonging(
                                                index,
                                                e.target.value,
                                            )
                                        }
                                        className="flex-1 text-sm rounded-md border-gray-300"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => removeBelonging(index)}
                                        className="text-red-600 text-sm px-2"
                                    >
                                        ✕
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="bg-white p-4 rounded-lg shadow flex items-center justify-between border-t-2 border-indigo-100">
                        <a
                            href="/admin/vehicles"
                            className="text-sm font-medium text-gray-600 hover:text-gray-900 underline"
                        >
                            ← Kembali ke Daftar Kendaraan
                        </a>

                        <button
                            type="submit"
                            disabled={processing}
                            style={{
                                backgroundColor: "#4f46e5",
                                color: "#ffffff",
                            }}
                            className="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg shadow hover:bg-indigo-700 disabled:opacity-50 cursor-pointer"
                        >
                            {processing
                                ? "Menyimpan..."
                                : "Simpan Hasil Inspeksi"}
                        </button>
                    </div>
                </form>

                {/* Riwayat Inspeksi Sebelumnya dari MongoDB */}
                {recentInspections.length > 0 && (
                    <div className="bg-white p-6 rounded-lg shadow space-y-3">
                        <h3 className="text-md font-bold text-gray-800">
                            Riwayat Inspeksi Terakhir (MongoDB)
                        </h3>
                        <div className="divide-y text-sm">
                            {recentInspections.map((insp) => (
                                <div
                                    key={insp.id || insp._id}
                                    className="py-2 flex justify-between"
                                >
                                    <span>
                                        Kategori:{" "}
                                        <strong>{insp.vehicle_category}</strong>{" "}
                                        | BBM: {insp.fuel_level_percentage}%
                                    </span>
                                    <span className="text-gray-500">
                                        {insp.created_at}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
