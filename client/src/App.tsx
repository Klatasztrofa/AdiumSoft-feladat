import { useEffect, useState } from "react";
import { WeatherTable } from "./components/WeatherTable";
import type { City, Region, WeatherRow, UnitSystem } from "./types";

export function App() {
    const [cities, setCities] = useState<City[]>([]);
    const [regions, setRegions] = useState<Region[]>([]);
    
    const [cityInfo, setCityInfo] = useState<string | null>(null);
    const [regionInfo, setRegionInfo] = useState<string | null>(null);

    const [selectedCityId, setSelectedCityId] = useState("");
    const [cityStartDate, setCityStartDate] = useState("2023-01-01");
    const [cityEndDate, setCityEndDate] = useState("2023-01-07");
    const [cityRows, setCityRows] = useState<WeatherRow[]>([]);
    const [cityError, setCityError] = useState<string | null>(null);

    const [selectedRegion, setSelectedRegion] = useState("");
    const [regionStartDate, setRegionStartDate] = useState("2023-01-01");
    const [regionEndDate, setRegionEndDate] = useState("2023-01-07");
    const [regionRows, setRegionRows] = useState<WeatherRow[]>([]);
    const [regionError, setRegionError] = useState<string | null>(null);

    const [unitSystem, setUnitSystem] = useState<UnitSystem>("si");
    
    useEffect(() => {
        async function loadData() {
        try {
            const cityList = await getCities();
            const regionList = await getRegions();

            setCities(cityList);
            setRegions(regionList);

            if (cityList.length > 0) {
                setSelectedCityId(String(cityList[0].id));
            }

            if (regionList.length > 0) {
                setSelectedRegion(regionList[0].name);
            }
        } catch (error) {
            console.error(error);
        }
        }

        void loadData();
    }, []);

    const onCityWeatherRequest = async () => {
        if (selectedCityId.length === 0) {
        setCityError("Kötelező várost választani");
        setCityInfo(null);
        setCityRows([]);
        return;
        }

        setCityError(null);
        setCityInfo(null);

        try {
            const rows = await getCityWeather(selectedCityId, cityStartDate, cityEndDate);
            setCityRows(rows);

            if (rows.length === 0) {
                setCityInfo("Nincs találat a megadott feltételekre.");
            }
        } catch (error) {
            setCityRows([]);
            setCityInfo(null);
            setCityError(String(error));
    }
    };

    const onRegionWeatherRequest = async () => {
        if (selectedRegion.trim().length === 0) {
            setRegionError("Kötelező régiót választani");
            setRegionInfo(null);
            setRegionRows([]);
        return;
        }

        setRegionError(null);
        setRegionInfo(null);

        try {
            const rows = await getRegionWeather(selectedRegion, regionStartDate, regionEndDate);
            setRegionRows(rows);

            if (rows.length === 0) {
                setRegionInfo("Nincs találat a megadott feltételekre.");
            }
        } catch (error) {
            setRegionRows([]);
            setRegionInfo(null);
            setRegionError(String(error));
        }
    };

    return (
        <div className="min-h-screen bg-slate-100 px-4 py-8">
        <div className="mx-auto flex max-w-6xl flex-col gap-8">
            <div className="rounded-lg border-2 border-slate-200 bg-white p-6">
            <h1 className="text-3xl font-bold">Meteorológiai kimutatások</h1>
            <p className="mt-2 text-slate-600">
                Városok és régiók időjárási adatainak lekérdezése.
            </p>
            </div>

            <div className="mt-4 flex items-center gap-3">
                <span className="font-medium">Mértékegység:</span>
                <button
                    type="button"
                    onClick={() => {
                    setUnitSystem("si");
                    }}
                    className={`rounded-lg px-3 py-2 font-semibold ${
                    unitSystem === "si" ? "bg-slate-900 text-white" : "bg-slate-200 text-slate-800"
                    }`}
                >
                    SI
                </button>
                <button
                    type="button"
                    onClick={() => {
                    setUnitSystem("imperial");
                    }}
                    className={`rounded-lg px-3 py-2 font-semibold ${
                    unitSystem === "imperial" ? "bg-slate-900 text-white" : "bg-slate-200 text-slate-800"
                    }`}
                >
                    Angolszász
                </button>
                </div>

            <div className="rounded-lg border-2 border-amber-500 bg-white p-6">
            <h2 className="mb-4 text-2xl font-bold">Város szerinti lekérdezés</h2>

            <div className="grid gap-4 md:grid-cols-4">
                <div className="flex flex-col">
                <label htmlFor="cityId" className="p-2 font-bold">
                    Város
                </label>
                <select
                    id="cityId"
                    value={selectedCityId}
                    onChange={(event) => {
                    setSelectedCityId(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                >
                    {cities.map((city) => (
                    <option key={city.id} value={city.id}>
                        {city.name} ({city.region})
                    </option>
                    ))}
                </select>
                </div>

                <div className="flex flex-col">
                <label htmlFor="cityStartDate" className="p-2 font-bold">
                    Kezdődátum
                </label>
                <input
                    id="cityStartDate"
                    type="date"
                    value={cityStartDate}
                    onChange={(event) => {
                    setCityStartDate(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                />
                </div>

                <div className="flex flex-col">
                <label htmlFor="cityEndDate" className="p-2 font-bold">
                    Végdátum
                </label>
                <input
                    id="cityEndDate"
                    type="date"
                    value={cityEndDate}
                    onChange={(event) => {
                    setCityEndDate(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                />
                </div>

                <div className="flex items-end">
                <button
                    onClick={(event) => {
                    event.preventDefault();
                    void onCityWeatherRequest();
                    }}
                    className="w-full cursor-pointer rounded-lg bg-amber-300 px-4 py-2 font-bold hover:bg-amber-400 active:bg-amber-500"
                >
                    Lekérés
                </button>
                </div>
            </div>

            {cityError && <span className="mt-3 block text-sm text-red-500">{cityError}</span>}
            {cityInfo && <span className="mt-3 block text-sm text-slate-600">{cityInfo}</span>}

            {cityRows.length > 0 && (
                <div className="mt-6">
                <WeatherTable rows={cityRows} unitSystem={unitSystem} />
                </div>
            )}
            </div>

            <div className="rounded-lg border-2 border-sky-500 bg-white p-6">
            <h2 className="mb-4 text-2xl font-bold">Régió szerinti lekérdezés</h2>

            <div className="grid gap-4 md:grid-cols-4">
                <div className="flex flex-col">
                <label htmlFor="region" className="p-2 font-bold">
                    Régió
                </label>
                <select
                    id="region"
                    value={selectedRegion}
                    onChange={(event) => {
                    setSelectedRegion(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                >
                    {regions.map((region) => (
                    <option key={region.name} value={region.name}>
                        {region.name}
                    </option>
                    ))}
                </select>
                </div>

                <div className="flex flex-col">
                <label htmlFor="regionStartDate" className="p-2 font-bold">
                    Kezdődátum
                </label>
                <input
                    id="regionStartDate"
                    type="date"
                    value={regionStartDate}
                    onChange={(event) => {
                    setRegionStartDate(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                />
                </div>

                <div className="flex flex-col">
                <label htmlFor="regionEndDate" className="p-2 font-bold">
                    Végdátum
                </label>
                <input
                    id="regionEndDate"
                    type="date"
                    value={regionEndDate}
                    onChange={(event) => {
                    setRegionEndDate(event.target.value);
                    }}
                    className="rounded-t-md border-b-1 bg-gray-100 p-2"
                />
                </div>

                <div className="flex items-end">
                <button
                    onClick={(event) => {
                    event.preventDefault();
                    void onRegionWeatherRequest();
                    }}
                    className="w-full cursor-pointer rounded-lg bg-sky-400 px-4 py-2 font-bold text-white hover:bg-sky-500 active:bg-sky-600"
                >
                    Lekérés
                </button>
                </div>
            </div>

            {regionError && <span className="mt-3 block text-sm text-red-500">{regionError}</span>}
            {regionInfo && <span className="mt-3 block text-sm text-red-500">{regionInfo}</span>}

            {regionRows.length > 0 && (
                <div className="mt-6">
                <WeatherTable rows={regionRows} unitSystem={unitSystem} />
                </div>
            )}
            </div>
        </div>
        </div>
    );
    }

    async function getCities(): Promise<City[]> {
    const baseUrl = import.meta.env.VITE_API_URL;

    const response = await fetch(`${baseUrl}/cities`);
    if (!response.ok) {
        const error = await response.text();
        throw Error(error);
    }

    const body = (await response.json()) as { rows: City[] };
    return body.rows;
    }

    async function getRegions(): Promise<Region[]> {
    const baseUrl = import.meta.env.VITE_API_URL;

    const response = await fetch(`${baseUrl}/regions`);
    if (!response.ok) {
        const error = await response.text();
        throw Error(error);
    }

    const body = (await response.json()) as { rows: Region[] };
    return body.rows;
    }

    async function getCityWeather(
    cityId: string,
    startDate: string,
    endDate: string,
    ): Promise<WeatherRow[]> {
    const baseUrl = import.meta.env.VITE_API_URL;

    const response = await fetch(
        `${baseUrl}/weather/by-city?cityId=${cityId}&startDate=${startDate}&endDate=${endDate}`,
    );
    if (!response.ok) {
        const error = await response.text();
        throw Error(error);
    }

    const body = (await response.json()) as { rows: WeatherRow[] };
    return body.rows;
    }

    async function getRegionWeather(
    region: string,
    startDate: string,
    endDate: string,
    ): Promise<WeatherRow[]> {
    const baseUrl = import.meta.env.VITE_API_URL;

    const params = new URLSearchParams({
        region,
        startDate,
        endDate,
    });

    const response = await fetch(`${baseUrl}/weather/by-region?${params.toString()}`);
    if (!response.ok) {
        const error = await response.text();
        throw Error(error);
    }

    const body = (await response.json()) as { rows: WeatherRow[] };
    return body.rows;
}