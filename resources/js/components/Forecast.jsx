import React, { useEffect, useState } from 'react';

export default function Forecast() {
    const [city, setCity] = useState('Brisbane');
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        async function load() {
            setLoading(true);
            setError(null);
            setData(null);

            try {
                const res = await fetch(`/api/forecast?city=${encodeURIComponent(city)}`);
                if (!res.ok) throw new Error('Backend failed');

                const json = await res.json();
                const days = (json.data?.days ?? []).slice(0, 5);

                if (days.length > 0) {
                    const mapped = days.map(({ forcast_max, forcast_min, forcast_date, forcast_avg }) => ({
                        date: forcast_date,
                        avg: forcast_avg,
                        max: Number.isFinite(forcast_max) ? Math.round(forcast_max) : 0,
                        min: Number.isFinite(forcast_min) ? Math.round(forcast_min) : 0,
                    }));
                    setData({ city, days: mapped });
                }
            } catch (err) {
                setError('Unable to load forecast API.');
            } finally {
                setLoading(false);
            }
        }

        load();
    }, [city]);

    const tableHeaders = ['City', 'Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'];

    return (
        <div className="border border-gray-300 rounded-lg p-4">
            <label htmlFor="city" className="block mb-2 font-medium text-gray-700">
                City:
            </label>
            <select
                id="city"
                value={city}
                onChange={(e) => setCity(e.target.value)}
                className="w-full border-2 border-blue-500 rounded-md px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white text-gray-800"
            >
                <option value="Brisbane">Brisbane</option>
                <option value="Gold Coast">Gold Coast</option>
                <option value="Sunshine Coast">Sunshine Coast</option>
            </select>

            {loading && <p className="text-gray-500">Loading…</p>}
            {error && <p className="text-red-600">{error}</p>}

            {data?.days?.length > 0 && (
                <div className="overflow-x-auto">
                    <table className="min-w-full border border-gray-300 text-left text-sm font-sans">
                        <thead className="bg-gray-100">
                        <tr>
                            {tableHeaders.map((label, idx) => (
                                <th key={idx} className="border-b border-gray-300 px-[6px] py-2 font-semibold">
                                    {label}
                                </th>
                            ))}
                        </tr>
                        </thead>
                        <tbody className="bg-white">
                        <tr>
                            <td className="border-b border-gray-300 px-[6px] py-2 font-medium">
                                {data.city}
                            </td>
                            {data.days.map((d, idx) => (
                                <td key={idx} className="border-b border-gray-300 px-[6px] py-2">
                                    <div><strong>Date:</strong> {d.date}</div>
                                    <div>Avg: {typeof d.avg === 'number' ? d.avg : 'NA'}</div>
                                    <div>Max: {typeof d.max === 'number' ? d.max : 'NA'}</div>
                                    <div>Low: {typeof d.min === 'number' ? d.min : 'NA'}</div>
                                </td>
                            ))}
                        </tr>
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}
